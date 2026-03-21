<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Course;
use App\Models\Qualification;
use App\Models\CourseCategory;
use App\Models\RegistrationNotification;
use App\Services\RollNumberGenerator;
use App\Services\InstituteAdminFeeCalculator;
use App\Services\StudentAuditLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Load relationships - handle missing/deleted records gracefully
        $query = Student::with(['course', 'qualifications', 'institute', 'creator']);

        // Role-based visibility:
        // - Super Admin: sees all students (optional filters)
        // - Institute Admin (Guest): sees ONLY students they created (created_by = their user ID)
        // - Staff/Regular Admin: sees all students from their institute (both website and guest registrations)
        if ($user->isInstituteAdmin()) {
            // Institute Admin (Guest) can only see students they registered
            $query->where('created_by', $user->id);
        } elseif (!$user->isSuperAdmin()) {
            // Regular admin/staff sees all students from their institute
            $instituteId = session('current_institute_id');
            if ($instituteId) {
                $query->where('institute_id', $instituteId);
            }
        }

        // Filters
        if ($request->filled('institute_id')) {
            $query->where('institute_id', $request->input('institute_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by registration type (website or guest)
        if ($request->filled('registration_type')) {
            if ($request->input('registration_type') === 'website') {
                $query->whereNull('created_by');
            } elseif ($request->input('registration_type') === 'guest') {
                $query->whereNotNull('created_by');
            }
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('registration_number', 'like', "%{$search}%")
                    ->orWhere('roll_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $students = $query->latest()
            ->paginate(resolve_per_page($request->query('per_page')))
            ->withQueryString();

        // For filters dropdowns
        $institutes = \App\Models\Institute::where('status', 'active')->get(['id', 'name']);
        $statuses = [
            'active' => 'Active',
            'pending' => 'Pending',
            'inactive' => 'Inactive',
            'rejected' => 'Rejected',
        ];

        // Count students by status
        $statusCounts = [
            'all' => Student::when($user->isInstituteAdmin(), function($q) use ($user) {
                // Institute Admin: only their students
                $q->where('created_by', $user->id);
            })->when(!$user->isSuperAdmin() && !$user->isInstituteAdmin(), function($q) {
                // Regular admin/staff: all students from their institute
                $instituteId = session('current_institute_id');
                if ($instituteId) {
                    $q->where('institute_id', $instituteId);
                }
            })->count(),
            'active' => Student::when($user->isInstituteAdmin(), function($q) use ($user) {
                $q->where('created_by', $user->id);
            })->when(!$user->isSuperAdmin() && !$user->isInstituteAdmin(), function($q) {
                $instituteId = session('current_institute_id');
                if ($instituteId) {
                    $q->where('institute_id', $instituteId);
                }
            })->where('status', 'active')->count(),
            'pending' => Student::when($user->isInstituteAdmin(), function($q) use ($user) {
                $q->where('created_by', $user->id);
            })->when(!$user->isSuperAdmin() && !$user->isInstituteAdmin(), function($q) {
                $instituteId = session('current_institute_id');
                if ($instituteId) {
                    $q->where('institute_id', $instituteId);
                }
            })->where('status', 'pending')->count(),
        ];

        return view('admin.students.index', compact('students', 'institutes', 'statuses', 'statusCounts'));
    }

    /**
     * Display website registrations (students who registered themselves).
     */
    public function websiteRegistrations(Request $request)
    {
        $user = Auth::user();

        // Only show website registrations (where created_by IS NULL)
        $query = Student::with(['course', 'qualifications', 'institute'])
            ->whereNull('created_by');

        // Super Admin can see all, normal admin sees only their institute
        if (!$user->isSuperAdmin()) {
            $instituteId = session('current_institute_id');
            if ($instituteId) {
                $query->where('institute_id', $instituteId);
            }
        }

        // Filters
        if ($request->filled('institute_id')) {
            $query->where('institute_id', $request->input('institute_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        } else {
            // Default: show pending registrations
            $query->where('status', 'pending');
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('registration_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $students = $query->latest()
            ->paginate(resolve_per_page($request->query('per_page')))
            ->withQueryString();

        // For filters dropdowns
        $institutes = \App\Models\Institute::where('status', 'active')->get(['id', 'name']);
        $statuses = [
            'active' => 'Active',
            'pending' => 'Pending',
            'inactive' => 'Inactive',
            'rejected' => 'Rejected',
        ];

        // Count pending website registrations
        $pendingCount = Student::whereNull('created_by')
            ->where('status', 'pending')
            ->when(!$user->isSuperAdmin(), function($q) {
                $instituteId = session('current_institute_id');
                if ($instituteId) {
                    $q->where('institute_id', $instituteId);
                }
            })
            ->count();

        return view('admin.students.website-registrations', compact('students', 'institutes', 'statuses', 'pendingCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        // For all admins (Super Admin and Institute Admin), load ALL courses from ALL institutes
        // This allows all admins to switch between institutes and see all courses
        $courses = Course::with(['institute', 'category'])->where('status', 'active')->get();
        $institutes = \App\Models\Institute::where('status', 'active')->get();
        
        // Get all active categories with their institutes
        $categories = CourseCategory::where('status', 'active')
            ->with('institute')
            ->orderBy('institute_id')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();
        
        // Get current institute from session for pre-selection (default selection)
        $currentInstituteId = session('current_institute_id');
        
        // If Super Admin or Institute Admin, show institute selector
        // For Institute Admin, we'll still show the selector but default to their institute
        if (!$user->isSuperAdmin() && $user->institute_id) {
            // For Institute Admin, default to their institute if no session institute
            if (!$currentInstituteId) {
                $currentInstituteId = $user->institute_id;
            }
        }
        
        // Pass courses as JSON for JavaScript (ALL courses for all institutes)
        $coursesJson = $courses->map(function($course) {
            return [
                'id' => $course->id,
                'institute_id' => $course->institute_id,
                'category_id' => $course->category_id,
                'name' => $course->name,
                'duration_months' => $course->duration_months ?? 0,
                'tuition_fee' => $course->tuition_fee ?? 0,
            ];
        })->toJson();
        
        // Pass categories as JSON for JavaScript filtering
        $categoriesJson = $categories->map(function($category) {
            return [
                'id' => $category->id,
                'institute_id' => $category->institute_id,
                'name' => $category->name,
            ];
        })->toJson();
        
        // For all admins, show institute selector
        return view('admin.students.create', compact('courses', 'institutes', 'categories', 'coursesJson', 'categoriesJson', 'currentInstituteId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Get institute ID - All admins can select institute from dropdown
        $instituteId = $request->input('institute_id') ?? session('current_institute_id');
        
        // If no institute selected, try to use user's institute (for Institute Admin as fallback)
        if (!$instituteId && $user->institute_id) {
            $instituteId = $user->institute_id;
        }
        
        // If still no institute, return error
        if (!$instituteId) {
            return redirect()->back()->withErrors(['institute_id' => 'Please select an institute.'])->withInput();
        }

        // New registrations use current session only (same as guest registration)
        $currentYear = (int) date('Y');
        $currentSession = $currentYear . '-' . substr((string) ($currentYear + 1), -2);
        $request->merge(['session' => $currentSession]);
        
        // Validate the request
        $validated = $request->validate([
            // Personal Details
            'name' => ['required', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date', 'before:today', 'after:1900-01-01'],
            'gender' => ['required', 'in:male,female,other'],
            'category' => ['nullable', 'string', 'max:255'],
            'aadhaar_number' => ['nullable', 'string', 'max:255'],
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'signature' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'aadhar_front' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'aadhar_back' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'certificate_class_10th' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,pdf', 'max:5120'],
            'certificate_class_12th' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,pdf', 'max:5120'],
            'certificate_graduation' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,pdf', 'max:5120'],
            'certificate_others' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,pdf', 'max:5120'],
            
            // Communication Details
            'email' => ['nullable', 'email', 'max:255', 'unique:students,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:255'],
            'nationality' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:255'],
            'pin_code' => ['nullable', 'string', 'max:10'],
            'address' => ['nullable', 'string'],
            
            // Programme Details
            'institute_id' => ['nullable', 'exists:institutes,id'], // For Super Admin
            'course_id' => ['required', Rule::exists('courses', 'id')->where('institute_id', $instituteId)],
            'session' => ['required', 'string', 'max:255', 'regex:/^\d{4}-\d{2}$/'],
            
            // Fee Details
            'registration_fee' => ['nullable', 'numeric', 'min:0'],
            'entrance_fee' => ['nullable', 'numeric', 'min:0'],
            'enrollment_fee' => ['nullable', 'numeric', 'min:0'],
            'tuition_fee' => ['nullable', 'numeric', 'min:0'],
            'caution_money' => ['nullable', 'numeric', 'min:0'],
            'hostel_fee_amount' => ['nullable', 'numeric', 'min:0'],
            'late_fee' => ['nullable', 'numeric', 'min:0'],
            'total_deposit' => ['nullable', 'numeric', 'min:0'],
            'pay_in_installment' => ['nullable', 'boolean'],
            
            // Payment Details
            'payment_mode' => ['nullable', 'string', 'max:255'],
            'bank_account' => ['nullable', 'string', 'max:255'],
            'deposit_date' => ['nullable', 'date'],
            
            // Declaration
            'declaration_accepted' => ['required', 'accepted'],
            
            // Qualifications
            'qualifications' => ['nullable', 'array'],
            'qualifications.*.examination' => ['nullable', 'in:secondary,sr_secondary,graduation,post_graduation,other'],
            'qualifications.*.year_of_passing' => ['nullable', 'string'],
            'qualifications.*.board_university' => ['nullable', 'string'],
            'qualifications.*.percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'qualifications.*.cgpa' => ['nullable', 'string'],
            'qualifications.*.subjects' => ['nullable', 'string'],
        ], [], [
            'session.regex' => 'Session must be in YYYY-YY format (e.g. 2025-26).',
        ]);
        
        // Handle document uploads
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('student-documents', 'public');
            $validated['photo'] = $photoPath;
        }
        
        if ($request->hasFile('signature')) {
            $signaturePath = $request->file('signature')->store('student-documents', 'public');
            $validated['signature'] = $signaturePath;
        }
        
        if ($request->hasFile('aadhar_front')) {
            $aadharFrontPath = $request->file('aadhar_front')->store('student-documents', 'public');
            $validated['aadhar_front'] = $aadharFrontPath;
        }
        
        if ($request->hasFile('aadhar_back')) {
            $aadharBackPath = $request->file('aadhar_back')->store('student-documents', 'public');
            $validated['aadhar_back'] = $aadharBackPath;
        }
        
        // Handle certificate uploads
        if ($request->hasFile('certificate_class_10th')) {
            $cert10Path = $request->file('certificate_class_10th')->store('student-certificates', 'public');
            $validated['certificate_class_10th'] = $cert10Path;
        }
        
        if ($request->hasFile('certificate_class_12th')) {
            $cert12Path = $request->file('certificate_class_12th')->store('student-certificates', 'public');
            $validated['certificate_class_12th'] = $cert12Path;
        }
        
        if ($request->hasFile('certificate_graduation')) {
            $certGradPath = $request->file('certificate_graduation')->store('student-certificates', 'public');
            $validated['certificate_graduation'] = $certGradPath;
        }
        
        if ($request->hasFile('certificate_others')) {
            $certOthersPath = $request->file('certificate_others')->store('student-certificates', 'public');
            $validated['certificate_others'] = $certOthersPath;
        }
        
        // Calculate total deposit if not provided
        if (empty($validated['total_deposit']) || $validated['total_deposit'] == 0) {
            $validated['total_deposit'] = 
                ($validated['registration_fee'] ?? 0) +
                ($validated['entrance_fee'] ?? 0) +
                ($validated['enrollment_fee'] ?? 0) +
                ($validated['tuition_fee'] ?? 0) +
                ($validated['caution_money'] ?? 0) +
                ($validated['hostel_fee_amount'] ?? 0) +
                ($validated['late_fee'] ?? 0);
        }
        
        // Set institute_id and created_by
        $validated['institute_id'] = $instituteId;
        $validated['created_by'] = Auth::id();

        // Password = date of birth in DDMMYYYY format (e.g. 03091992); student can change after login
        $credentials = Student::passwordCredentialsFromDateOfBirth($validated['date_of_birth']);
        if (! $credentials) {
            return redirect()->back()->withErrors(['date_of_birth' => 'Invalid date of birth.'])->withInput();
        }
        $validated = array_merge($validated, $credentials);
        
        // New students start as pending until approved by admin / super admin
        $validated['status'] = 'pending';
        $validated['declaration_accepted'] = true;
        
        // Set admission_year from session field (e.g., "2025-26" -> "2025") or current year
        if (!isset($validated['admission_year']) || empty($validated['admission_year'])) {
            if (isset($validated['session']) && !empty($validated['session'])) {
                // Extract year from session (e.g., "2025-26" -> "2025")
                $sessionParts = explode('-', $validated['session']);
                $validated['admission_year'] = $sessionParts[0] ?? date('Y');
            } else {
                $validated['admission_year'] = date('Y');
            }
        }
        
        // Handle boolean fields
        $validated['pay_in_installment'] = $request->has('pay_in_installment') && $request->pay_in_installment == '1';
        
        // Generate a unique registration number for the student using session year
        $sessionYear = null;
        if (isset($validated['session']) && !empty($validated['session'])) {
            $sessionParts = explode('-', $validated['session']);
            $sessionYear = $sessionParts[0] ?? null;
        }
        $validated['registration_number'] = $this->generateRegistrationNumber($instituteId, $sessionYear);

        // Calculate and store institute admin fee (only if student is created by an admin, not website registration)
        if ($validated['created_by']) {
            $course = Course::find($validated['course_id']);
            if ($course && $course->duration_months) {
                $validated['institute_admin_fee'] = InstituteAdminFeeCalculator::calculate($course->duration_months);
            }
        }

        // Remove qualifications from validated data (will be handled separately)
        $qualifications = $validated['qualifications'] ?? [];
        unset($validated['qualifications']);
        
        // Ensure session is saved (it's required, so it should be in validated)
        if (!isset($validated['session']) || empty($validated['session'])) {
            \Log::warning('Admin student creation: Session field is missing!', ['request_data' => $request->all()]);
            return redirect()->back()
                ->withErrors(['session' => 'Session is required. Please select a session.'])
                ->withInput();
        }

        // Create student
        $student = Student::create($validated);
        StudentAuditLogger::logCreated($student);
        
        // Log to verify session was saved
        \Log::info('Student created by admin with session', [
            'student_id' => $student->id,
            'session' => $student->session,
            'registration_number' => $student->registration_number
        ]);
        
        // Create qualifications (only if examination is provided)
        if (!empty($qualifications)) {
            $createdQualifications = [];
            foreach ($qualifications as $qualification) {
                if (!empty($qualification['examination']) && !empty($qualification['year_of_passing']) && $qualification['year_of_passing'] !== 'yyyy') {
                    $newQualification = Qualification::create([
                        'student_id' => $student->id,
                        'examination' => $qualification['examination'],
                        'year_of_passing' => $qualification['year_of_passing'] ?? null,
                        'board_university' => $qualification['board_university'] ?? null,
                        'percentage' => $qualification['percentage'] ?? null,
                        'cgpa' => $qualification['cgpa'] ?? null,
                        'subjects' => $qualification['subjects'] ?? null,
                    ]);
                    $createdQualifications[] = $newQualification->only([
                        'id',
                        'examination',
                        'year_of_passing',
                        'board_university',
                        'percentage',
                        'cgpa',
                        'subjects',
                    ]);
                }
            }

            if (!empty($createdQualifications)) {
                StudentAuditLogger::logRelatedCreated($student, 'qualification', [
                    'rows' => $createdQualifications,
                ]);
            }
        }
        
        // Create notification for new guest registration
        RegistrationNotification::create([
            'student_id' => $student->id,
            'institute_id' => $instituteId,
            'registration_type' => 'guest',
        ]);
        
        // Create initial fee entry if total_deposit > 0
        if ($student->total_deposit && $student->total_deposit > 0) {
            $student->fees()->create([
                'amount' => $student->total_deposit,
                'payment_type' => 'registration',
                'payment_mode' => $student->payment_mode ?? 'offline', // Default to offline
                'semester' => 1, // Default to semester 1 for new registrations
                'status' => 'pending_verification',
                'payment_date' => $student->deposit_date ?? now(),
                'remarks' => 'Initial registration fee',
                'marked_by' => Auth::id(),
            ]);
        }
        
        return redirect()->route('admin.students.index')
            ->with('success', 'Student registered successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $user = Auth::user();

        $student = Student::with(['institute', 'course', 'qualifications', 'creator'])->findOrFail($id);

        if (! $user->canViewStudentRecord($student)) {
            abort(403, 'You are not authorized to view this student.');
        }

        // Load published semester results (only truly published ones)
        // Uses the trulyPublished scope to ensure consistency across the application
        $publishedSemesterResults = \App\Models\SemesterResult::where('student_id', $student->id)
            ->trulyPublished()
            ->with(['results.subject', 'enteredBy', 'verifiedBy'])
            ->orderBy('semester')
            ->get();

        // Mark notification as read if it exists
        $notification = RegistrationNotification::where('student_id', $student->id)
            ->whereNull('read_at')
            ->first();
        
        if ($notification) {
            $notification->markAsRead($user->id);
        }

        $studentAudits = $student->audits()
            ->with('actor:id,name,email')
            ->paginate(resolve_per_page($request->query('history_per_page'), 10), ['*'], 'history_page');

        return view('admin.students.show', compact('student', 'publishedSemesterResults', 'studentAudits'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $student = Student::with(['institute', 'course', 'creator', 'qualifications'])->findOrFail($id);

        // Only super admins can edit student details
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Only Super Admin can edit student details.');
        }

        // Load all institutes, courses, and categories for Super Admin
        $institutes = \App\Models\Institute::where('status', 'active')->get(['id', 'name']);
        $categories = CourseCategory::where('status', 'active')
            ->with('institute')
            ->orderBy('institute_id')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();
        
        // Load all courses with their relationships
        $courses = Course::with(['institute', 'category'])
            ->where('status', 'active')
            ->orderBy('institute_id')
            ->orderBy('name')
            ->get();
        
        // Convert to JSON for JavaScript filtering
        $coursesJson = $courses->map(function($course) {
            return [
                'id' => $course->id,
                'institute_id' => $course->institute_id,
                'category_id' => $course->category_id,
                'name' => $course->name,
                'tuition_fee' => $course->tuition_fee,
                'duration_months' => $course->duration_months,
            ];
        })->toJson();
        
        $categoriesJson = $categories->map(function($category) {
            return [
                'id' => $category->id,
                'institute_id' => $category->institute_id,
                'name' => $category->name,
            ];
        })->toJson();

        $statuses = [
            'pending' => 'Pending',
            'active' => 'Active',
            'inactive' => 'Inactive',
            'rejected' => 'Rejected',
        ];

        return view('admin.students.edit', compact('student', 'statuses', 'institutes', 'courses', 'categories', 'coursesJson', 'categoriesJson'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $student = Student::findOrFail($id);

        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Only Super Admin can update student details.');
        }

        // Get institute ID
        $instituteId = $request->input('institute_id') ?? $student->institute_id;
        
        // If no institute selected, return error
        if (!$instituteId) {
            return redirect()->back()->withErrors(['institute_id' => 'Please select an institute.'])->withInput();
        }

        $validated = $request->validate([
            // Personal Details
            'name' => ['required', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date', 'before:today', 'after:1900-01-01'],
            'gender' => ['required', 'in:male,female,other'],
            'category' => ['nullable', 'string', 'max:255'],
            'aadhaar_number' => ['nullable', 'string', 'max:255'],
            'passport_number' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'signature' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'aadhar_front' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'aadhar_back' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'certificate_class_10th' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,pdf', 'max:5120'],
            'certificate_class_12th' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,pdf', 'max:5120'],
            'certificate_graduation' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,pdf', 'max:5120'],
            'certificate_others' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,pdf', 'max:5120'],
            
            // Communication Details
            'email' => ['nullable', 'email', 'max:255', Rule::unique('students', 'email')->ignore($student->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'father_contact' => ['nullable', 'string', 'max:20'],
            'mother_contact' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:255'],
            'nationality' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:255'],
            'pin_code' => ['nullable', 'string', 'max:10'],
            'address' => ['nullable', 'string'],
            
            // Programme Details
            'institute_id' => ['required', 'exists:institutes,id'],
            'course_id' => ['required', Rule::exists('courses', 'id')->where('institute_id', $instituteId)],
            'session' => ['nullable', 'string', 'max:255', 'regex:/^\d{4}-\d{2}$/'],
            'admission_year' => ['nullable', 'string', 'max:255'],
            'mode_of_study' => ['nullable', 'string', 'max:255'],
            'admission_type' => ['nullable', 'string', 'max:255'],
            'hostel_facility_required' => ['nullable', 'boolean'],
            'current_semester' => ['nullable', 'integer', 'min:1'],
            'stream' => ['nullable', 'string', 'max:255'],
            
            // Status and Roll Number
            'roll_number' => ['nullable', 'string', 'max:255', Rule::unique('students', 'roll_number')->ignore($student->id)],
            'status' => ['required', Rule::in(['pending', 'active', 'inactive', 'rejected'])],
            
            // Employment Details
            'is_employed' => ['nullable', 'boolean'],
            'employer_name' => ['nullable', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
            
            // Fee Details
            'registration_fee' => ['nullable', 'numeric', 'min:0'],
            'entrance_fee' => ['nullable', 'numeric', 'min:0'],
            'enrollment_fee' => ['nullable', 'numeric', 'min:0'],
            'tuition_fee' => ['nullable', 'numeric', 'min:0'],
            'caution_money' => ['nullable', 'numeric', 'min:0'],
            'hostel_fee_amount' => ['nullable', 'numeric', 'min:0'],
            'late_fee' => ['nullable', 'numeric', 'min:0'],
            'total_deposit' => ['nullable', 'numeric', 'min:0'],
            'pay_in_installment' => ['nullable', 'boolean'],
            
            // Payment Details
            'payment_mode' => ['nullable', 'string', 'max:255'],
            'bank_account' => ['nullable', 'string', 'max:255'],
            'deposit_date' => ['nullable', 'date'],
            
            // Password (optional - only update if provided)
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            
            // Qualifications
            'qualifications' => ['nullable', 'array'],
            'qualifications.*.examination' => ['nullable', 'in:secondary,sr_secondary,graduation,post_graduation,other'],
            'qualifications.*.year_of_passing' => ['nullable', 'string'],
            'qualifications.*.board_university' => ['nullable', 'string'],
            'qualifications.*.percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'qualifications.*.cgpa' => ['nullable', 'string'],
            'qualifications.*.subjects' => ['nullable', 'string'],
        ], [], [
            'session.regex' => 'Session must be in YYYY-YY format (e.g. 2025-26).',
        ]);

        // Auto-generate roll number when status is active and roll_number is empty
        // Handle both null and empty string cases
        $rollNumberEmpty = empty($validated['roll_number']) || trim($validated['roll_number'] ?? '') === '';
        
        if ($validated['status'] === 'active' && $rollNumberEmpty) {
            try {
                // Reload student with relationships for roll number generation
                $student->load(['institute', 'course.category']);
                
                // Check if student has session (required for enrollment number generation)
                $studentSession = $validated['session'] ?? $student->session;
                if (empty($studentSession)) {
                    return back()
                        ->withErrors(['roll_number' => 'Cannot generate enrollment number: Student session is required. Please set the session first.'])
                        ->withInput();
                }
                
                // Check if course is assigned
                if (!$student->course) {
                    return back()
                        ->withErrors(['roll_number' => 'Cannot generate enrollment number: Student must have a course assigned.'])
                        ->withInput();
                }
                
                // Auto-generate enrollment number (will use session year automatically)
                $validated['roll_number'] = RollNumberGenerator::generate($student);
            } catch (\Exception $e) {
                return back()
                    ->withErrors(['roll_number' => 'Failed to generate enrollment number: ' . $e->getMessage()])
                    ->withInput();
            }
        }
        
        // If status is active and still no enrollment number, return error
        $rollNumberStillEmpty = empty($validated['roll_number']) || trim($validated['roll_number'] ?? '') === '';
        if ($validated['status'] === 'active' && $rollNumberStillEmpty) {
            return back()
                ->withErrors(['roll_number' => 'Enrollment number is required when activating a student.'])
                ->withInput();
        }

        // Handle document uploads (only if new files are uploaded)
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }
            $photoPath = $request->file('photo')->store('student-documents', 'public');
            $validated['photo'] = $photoPath;
        }
        
        if ($request->hasFile('signature')) {
            if ($student->signature) {
                Storage::disk('public')->delete($student->signature);
            }
            $signaturePath = $request->file('signature')->store('student-documents', 'public');
            $validated['signature'] = $signaturePath;
        }
        
        if ($request->hasFile('aadhar_front')) {
            if ($student->aadhar_front) {
                Storage::disk('public')->delete($student->aadhar_front);
            }
            $aadharFrontPath = $request->file('aadhar_front')->store('student-documents', 'public');
            $validated['aadhar_front'] = $aadharFrontPath;
        }
        
        if ($request->hasFile('aadhar_back')) {
            if ($student->aadhar_back) {
                Storage::disk('public')->delete($student->aadhar_back);
            }
            $aadharBackPath = $request->file('aadhar_back')->store('student-documents', 'public');
            $validated['aadhar_back'] = $aadharBackPath;
        }
        
        // Handle certificate uploads
        if ($request->hasFile('certificate_class_10th')) {
            if ($student->certificate_class_10th) {
                Storage::disk('public')->delete($student->certificate_class_10th);
            }
            $cert10Path = $request->file('certificate_class_10th')->store('student-certificates', 'public');
            $validated['certificate_class_10th'] = $cert10Path;
        }
        
        if ($request->hasFile('certificate_class_12th')) {
            if ($student->certificate_class_12th) {
                Storage::disk('public')->delete($student->certificate_class_12th);
            }
            $cert12Path = $request->file('certificate_class_12th')->store('student-certificates', 'public');
            $validated['certificate_class_12th'] = $cert12Path;
        }
        
        if ($request->hasFile('certificate_graduation')) {
            if ($student->certificate_graduation) {
                Storage::disk('public')->delete($student->certificate_graduation);
            }
            $certGradPath = $request->file('certificate_graduation')->store('student-certificates', 'public');
            $validated['certificate_graduation'] = $certGradPath;
        }
        
        if ($request->hasFile('certificate_others')) {
            if ($student->certificate_others) {
                Storage::disk('public')->delete($student->certificate_others);
            }
            $certOthersPath = $request->file('certificate_others')->store('student-certificates', 'public');
            $validated['certificate_others'] = $certOthersPath;
        }
        
        $passwordSyncedFromDob = false;

        // Handle password: explicit new password wins; else if DOB changed, sync default password from new DOB
        if (! empty($validated['password'])) {
            $plainPassword = $validated['password'];
            $validated['password'] = Hash::make($plainPassword);
            $validated['password_plain_encrypted'] = encrypt($plainPassword);
        } else {
            unset($validated['password']);
            $oldDob = $student->date_of_birth
                ? Carbon::parse($student->date_of_birth)->format('Y-m-d')
                : null;
            $newDob = Carbon::parse($validated['date_of_birth'])->format('Y-m-d');
            if ($oldDob !== $newDob) {
                $credentials = Student::passwordCredentialsFromDateOfBirth($validated['date_of_birth']);
                if (! $credentials) {
                    return redirect()->back()
                        ->withErrors(['date_of_birth' => 'Invalid date of birth.'])
                        ->withInput();
                }
                $validated = array_merge($validated, $credentials);
                $passwordSyncedFromDob = true;
            }
        }
        
        // Calculate total deposit if not provided
        if (empty($validated['total_deposit']) || $validated['total_deposit'] == 0) {
            $validated['total_deposit'] = 
                ($validated['registration_fee'] ?? 0) +
                ($validated['entrance_fee'] ?? 0) +
                ($validated['enrollment_fee'] ?? 0) +
                ($validated['tuition_fee'] ?? 0) +
                ($validated['caution_money'] ?? 0) +
                ($validated['hostel_fee_amount'] ?? 0) +
                ($validated['late_fee'] ?? 0);
        }
        
        // Set admission_year from session field if not provided
        if (!isset($validated['admission_year']) || empty($validated['admission_year'])) {
            if (isset($validated['session']) && !empty($validated['session'])) {
                $sessionParts = explode('-', $validated['session']);
                $validated['admission_year'] = $sessionParts[0] ?? date('Y');
            } else {
                $validated['admission_year'] = $student->admission_year ?? date('Y');
            }
        }
        
        // Check if session changed - if so, regenerate registration and roll numbers
        $sessionChanged = false;
        if (isset($validated['session']) && $validated['session'] !== $student->session) {
            $sessionChanged = true;
            $newSession = $validated['session'];
            $newYear = explode('-', $newSession)[0]; // Extract year (e.g., "2022-23" -> "2022")
            
            // Regenerate Registration Number
            try {
                $instituteId = $validated['institute_id'] ?? $student->institute_id;
                $newRegNumber = $this->generateRegistrationNumberForYear($instituteId, $newYear, $student->id);
                $validated['registration_number'] = $newRegNumber;
            } catch (\Exception $e) {
                return back()
                    ->withErrors(['session' => 'Failed to generate new registration number: ' . $e->getMessage()])
                    ->withInput();
            }
            
            // Regenerate Roll Number (if student is active and has roll number)
            if ($student->status === 'active' && $student->roll_number) {
                try {
                    // Reload student with relationships for enrollment number generation
                    $student->load(['institute', 'course']);
                    
                    // Check prerequisites
                    if (!$student->course) {
                        return back()
                            ->withErrors(['session' => 'Cannot generate enrollment number: Student must have a course assigned.'])
                            ->withInput();
                    }
                    
                    $newRollNumber = RollNumberGenerator::generateForYear($student, $newYear);
                    $validated['roll_number'] = $newRollNumber;
                } catch (\Exception $e) {
                    return back()
                        ->withErrors(['session' => 'Failed to generate new enrollment number: ' . $e->getMessage()])
                        ->withInput();
                }
            }
            
            // Update admission_year to match new session
            $validated['admission_year'] = $newYear;

            // Recompute academic_year, result_declaration_date, and date_of_issue per semester from new session
            $semesterResults = \App\Models\SemesterResult::where('student_id', $student->id)->get();
            foreach ($semesterResults as $sr) {
                $sem = (int) $sr->semester;
                $academicYear = \App\Models\SemesterResult::getAcademicYearForSessionSemester($newSession, $sem);
                $resultDate = \App\Models\SemesterResult::getDefaultResultDeclarationDate($newSession, $sem);
                $issueDate = \App\Models\SemesterResult::getDefaultMarksheetIssueDate($newSession, $sem);
                $sr->update([
                    'academic_year' => $academicYear,
                    'result_declaration_date' => $resultDate,
                    'date_of_issue' => $sr->date_of_issue ? $issueDate : null, // keep issue date in sync if it was set
                ]);
                // Sync child Result records to same academic_year
                \App\Models\Result::where('semester_result_id', $sr->id)->update(['academic_year' => $academicYear]);
            }
            // Legacy Result rows without semester_result_id: set academic_year from new session (year 1)
            \App\Models\Result::where('student_id', $student->id)->whereNull('semester_result_id')
                ->update(['academic_year' => $newSession]);

            // Delete old PDFs (they will regenerate with new numbers on next view)
            $this->deleteStudentPDFs($student);
        }
        
        // Handle boolean fields
        $validated['pay_in_installment'] = $request->has('pay_in_installment') && $request->pay_in_installment == '1';
        $validated['is_employed'] = $request->has('is_employed') && $request->is_employed == '1';
        $validated['hostel_facility_required'] = $request->has('hostel_facility_required') && $request->hostel_facility_required == '1';
        
        // Remove qualifications from validated data (will be handled separately)
        $qualifications = $validated['qualifications'] ?? [];
        unset($validated['qualifications']);
        $oldQualifications = $student->qualifications()
            ->get(['id', 'examination', 'year_of_passing', 'board_university', 'percentage', 'cgpa', 'subjects'])
            ->toArray();
        
        // Update student and capture field-level diffs for audit history.
        $student->fill($validated);
        $dirty = $student->getDirty();
        $oldValues = [];
        $newValues = [];
        foreach ($dirty as $field => $newValue) {
            $oldValues[$field] = $student->getOriginal($field);
            $newValues[$field] = $newValue;
        }
        $student->save();
        StudentAuditLogger::logUpdated($student, $oldValues, $newValues);
        
        // Update qualifications - delete existing and create new ones
        $student->qualifications()->delete();
        $newQualifications = [];
        if (!empty($qualifications)) {
            foreach ($qualifications as $qualification) {
                if (!empty($qualification['examination']) && !empty($qualification['year_of_passing']) && $qualification['year_of_passing'] !== 'yyyy') {
                    $newQualification = Qualification::create([
                        'student_id' => $student->id,
                        'examination' => $qualification['examination'],
                        'year_of_passing' => $qualification['year_of_passing'] ?? null,
                        'board_university' => $qualification['board_university'] ?? null,
                        'percentage' => $qualification['percentage'] ?? null,
                        'cgpa' => $qualification['cgpa'] ?? null,
                        'subjects' => $qualification['subjects'] ?? null,
                    ]);
                    $newQualifications[] = $newQualification->only([
                        'id',
                        'examination',
                        'year_of_passing',
                        'board_university',
                        'percentage',
                        'cgpa',
                        'subjects',
                    ]);
                }
            }
        }
        if ($oldQualifications !== $newQualifications) {
            StudentAuditLogger::logRelatedUpdated($student, 'qualification', [
                'rows' => $oldQualifications,
            ], [
                'rows' => $newQualifications,
            ]);
        }

        // Prepare success message
        $successMessage = 'Student details updated successfully.';
        if ($passwordSyncedFromDob ?? false) {
            $successMessage .= ' Login password was updated to match the new date of birth (DDMMYYYY).';
        }
        if ($sessionChanged) {
            $successMessage .= ' Registration number updated to: ' . $validated['registration_number'];
            if (isset($validated['roll_number'])) {
                $successMessage .= ', Enrollment No updated to: ' . $validated['roll_number'];
            }
            $successMessage .= '. Old PDFs have been deleted and will regenerate with new numbers on next view.';
        }
        
        return redirect()->route('admin.students.index')
            ->with('success', $successMessage);
    }

    /**
     * Soft-delete the student (only Super Admin).
     * No data is permanently removed; the student is hidden from lists and cannot log in.
     * Related records (results, fees, etc.) are kept for audit; they still show the student via withTrashed().
     */
    public function destroy(string $id)
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Only Super Admin can remove students.');
        }

        $student = Student::findOrFail($id);
        StudentAuditLogger::logDeleted($student);
        $student->delete(); // Soft delete: sets deleted_at

        return redirect()->route('admin.students.index')
            ->with('success', 'Student has been removed from the active list. Their records are retained for audit.');
    }

    /**
     * Generate a unique registration number for a new student.
     *
     * Format: REG-2025-00001 (consistent with PublicRegistrationController format)
     */
    /**
     * Generate a unique registration number for a student
     * Format: REG-{YEAR}-{STUDENT_NUMBER}
     * Example: REG-2025-05000 (starting from 5000)
     * Uses session year if provided, otherwise uses current year
     * 
     * @param int $instituteId
     * @param string|null $sessionYear Year from session (e.g., "2025" from "2025-26")
     * @return string
     */
    protected function generateRegistrationNumber(int $instituteId, ?string $sessionYear = null): string
    {
        $prefix = 'REG';
        
        // Use session year if provided, otherwise fallback to current year
        $year = $sessionYear ?? date('Y');
        
        // Validate year format
        if (!is_numeric($year) || strlen($year) !== 4) {
            $year = date('Y'); // Fallback to current year if invalid
        }
        
        // Get the last registration number for this year (format: REG-{YEAR}-{NUMBER})
        $lastStudent = Student::where('registration_number', 'like', $prefix . '-' . $year . '-%')
            ->orderBy('registration_number', 'desc')
            ->first();
        
        $nextNumber = 5000; // Start from 5000
        
        if ($lastStudent && $lastStudent->registration_number) {
            // Extract the sequence number from the last registration number
            // Format: REG-2025-05000 -> extract 05000
            $parts = explode('-', $lastStudent->registration_number);
            if (count($parts) >= 3) {
                $lastNumber = (int) $parts[2]; // Get the sequence part
                // If last number is less than 5000, start from 5000
                // Otherwise, increment from last number
                $nextNumber = max(5000, $lastNumber + 1);
            }
        }
        
        // Ensure uniqueness by checking if the number already exists
        // This handles race conditions where multiple registrations happen simultaneously
        $maxAttempts = 1000; // Allow up to 1000 attempts
        $attempts = 0;
        
        do {
            $sequencePadded = str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            $registrationNumber = "{$prefix}-{$year}-{$sequencePadded}";
            
            // Check if this registration number already exists
            $exists = Student::where('registration_number', $registrationNumber)->exists();
            
            if (!$exists) {
                return $registrationNumber;
            }
            
            $nextNumber++;
            $attempts++;
        } while ($attempts < $maxAttempts);
        
        // Fallback: use timestamp-based number if we can't find a unique sequence
        $timestamp = time();
        return "{$prefix}-{$year}-" . substr($timestamp, -5);
    }

    /**
     * Generate registration number for a specific year (used when session changes)
     * Format: REG-{YEAR}-{STUDENT_NUMBER}
     * Example: REG-2025-05000 (starting from 5000)
     * Excludes current student from uniqueness check
     * 
     * @param int $instituteId
     * @param string $year Year to use (e.g., "2025")
     * @param int|null $excludeStudentId Student ID to exclude from check
     * @return string
     */
    protected function generateRegistrationNumberForYear(int $instituteId, string $year, ?int $excludeStudentId = null): string
    {
        $prefix = 'REG';
        
        // Validate year format
        if (!is_numeric($year) || strlen($year) !== 4) {
            $year = date('Y'); // Fallback to current year if invalid
        }
        
        // Get the last registration number for this year (format: REG-{YEAR}-{NUMBER})
        $query = Student::where('registration_number', 'like', $prefix . '-' . $year . '-%');
        
        // Exclude current student from check
        if ($excludeStudentId) {
            $query->where('id', '!=', $excludeStudentId);
        }
        
        $lastStudent = $query->orderBy('registration_number', 'desc')->first();
        
        $nextNumber = 5000; // Start from 5000
        
        if ($lastStudent && $lastStudent->registration_number) {
            // Extract the sequence number from the last registration number
            // Format: REG-2025-05000 -> extract 05000
            $parts = explode('-', $lastStudent->registration_number);
            if (count($parts) >= 3) {
                $lastNumber = (int) $parts[2]; // Get the sequence part
                // If last number is less than 5000, start from 5000
                // Otherwise, increment from last number
                $nextNumber = max(5000, $lastNumber + 1);
            }
        }
        
        // Ensure uniqueness by checking if the number already exists
        $maxAttempts = 1000; // Allow up to 1000 attempts
        $attempts = 0;
        
        do {
            $sequencePadded = str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            $registrationNumber = "{$prefix}-{$year}-{$sequencePadded}";
            
            // Check if this registration number already exists (excluding current student)
            $existsQuery = Student::where('registration_number', $registrationNumber);
            if ($excludeStudentId) {
                $existsQuery->where('id', '!=', $excludeStudentId);
            }
            $exists = $existsQuery->exists();
            
            if (!$exists) {
                return $registrationNumber;
            }
            
            $nextNumber++;
            $attempts++;
        } while ($attempts < $maxAttempts);
        
        // Fallback: use timestamp-based number if we can't find a unique sequence
        $timestamp = time();
        return "{$prefix}-{$year}-" . substr($timestamp, -5);
    }

    /**
     * Delete all PDFs associated with a student when session changes
     */
    protected function deleteStudentPDFs(Student $student)
    {
        try {
            // Delete semester result PDFs
            $semesterResults = $student->semesterResults()->whereNotNull('pdf_path')->get();
            foreach ($semesterResults as $result) {
                if ($result->pdf_path && \Storage::disk('public')->exists($result->pdf_path)) {
                    \Storage::disk('public')->delete($result->pdf_path);
                }
                // Clear pdf_path in database (will regenerate on next view)
                $result->update(['pdf_path' => null]);
            }
            
            // Delete entire results directory if exists
            $resultsDir = 'results/' . $student->id;
            if (\Storage::disk('public')->exists($resultsDir)) {
                \Storage::disk('public')->deleteDirectory($resultsDir);
            }
        } catch (\Exception $e) {
            // Log error but don't fail the update
            Log::warning('Failed to delete PDFs for student ' . $student->id . ': ' . $e->getMessage());
        }
    }
}
