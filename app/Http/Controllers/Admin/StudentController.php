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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

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

        $students = $query->latest()->paginate(15)->withQueryString();

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

        $students = $query->latest()->paginate(15)->withQueryString();

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
        
        // Validate the request
        $validated = $request->validate([
            // Personal Details
            'name' => ['required', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
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
            'course_id' => ['required', 'exists:courses,id'],
            'session' => ['required', 'string', 'max:255'],
            
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
            
            // Password (minimum 8 characters for security)
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            
            // Qualifications
            'qualifications' => ['nullable', 'array'],
            'qualifications.*.examination' => ['nullable', 'in:secondary,sr_secondary,graduation,post_graduation,other'],
            'qualifications.*.year_of_passing' => ['nullable', 'string'],
            'qualifications.*.board_university' => ['nullable', 'string'],
            'qualifications.*.percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'qualifications.*.cgpa' => ['nullable', 'string'],
            'qualifications.*.subjects' => ['nullable', 'string'],
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
        
        // Store plain password before hashing (for encrypted storage)
        $plainPassword = $validated['password'];
        $validated['password'] = Hash::make($plainPassword);
        
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
        
        // Generate a unique registration number for the student
        $validated['registration_number'] = $this->generateRegistrationNumber($instituteId);

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
        
        // Create student
        $student = Student::create($validated);
        
        // Also store encrypted plain password for Super Admin viewing
        $student->setPlainPassword($plainPassword);
        $student->save();
        
        // Create qualifications (only if examination is provided)
        if (!empty($qualifications)) {
            foreach ($qualifications as $qualification) {
                if (!empty($qualification['examination']) && !empty($qualification['year_of_passing']) && $qualification['year_of_passing'] !== 'yyyy') {
                    Qualification::create([
                        'student_id' => $student->id,
                        'examination' => $qualification['examination'],
                        'year_of_passing' => $qualification['year_of_passing'] ?? null,
                        'board_university' => $qualification['board_university'] ?? null,
                        'percentage' => $qualification['percentage'] ?? null,
                        'cgpa' => $qualification['cgpa'] ?? null,
                        'subjects' => $qualification['subjects'] ?? null,
                    ]);
                }
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
    public function show(string $id)
    {
        $user = Auth::user();

        $student = Student::with(['institute', 'course', 'qualifications', 'creator'])->findOrFail($id);

        // Normal admins (staff) can view only students they created OR website registrations from their institute
        if (!$user->isSuperAdmin()) {
            $instituteId = session('current_institute_id');
            if ($student->created_by !== $user->id && ($student->created_by !== null || $student->institute_id != $instituteId)) {
                abort(403, 'You are not authorized to view this student.');
            }
        }

        // Load published semester results
        $publishedSemesterResults = \App\Models\SemesterResult::where('student_id', $student->id)
            ->where('status', 'published')
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

        return view('admin.students.show', compact('student', 'publishedSemesterResults'));
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
            'date_of_birth' => ['required', 'date'],
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
            'course_id' => ['required', 'exists:courses,id'],
            'session' => ['nullable', 'string', 'max:255'],
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
        ]);

        // Auto-generate roll number when status is active and roll_number is empty
        // Handle both null and empty string cases
        $rollNumberEmpty = empty($validated['roll_number']) || trim($validated['roll_number'] ?? '') === '';
        
        if ($validated['status'] === 'active' && $rollNumberEmpty) {
            try {
                // Reload student with relationships for roll number generation
                $student->load(['institute', 'course.category']);
                
                // Check if institute has institute_code set
                if (empty($student->institute->institute_code)) {
                    return back()
                        ->withErrors(['roll_number' => 'Cannot generate roll number: Institute code is not set. Please set institute_code for the institute first.'])
                        ->withInput();
                }
                
                // Check if course has category
                if (!$student->course) {
                    return back()
                        ->withErrors(['roll_number' => 'Cannot generate roll number: Student must have a course assigned.'])
                        ->withInput();
                }
                
                // Check if category has roll_number_code
                if (!$student->course->category || empty($student->course->category->roll_number_code)) {
                    return back()
                        ->withErrors(['roll_number' => 'Cannot generate roll number: Course category does not have a roll number code. Please set roll_number_code for the category first.'])
                        ->withInput();
                }
                
                // Auto-generate roll number
                $validated['roll_number'] = RollNumberGenerator::generate($student);
            } catch (\Exception $e) {
                return back()
                    ->withErrors(['roll_number' => 'Failed to generate roll number: ' . $e->getMessage()])
                    ->withInput();
            }
        }
        
        // If status is active and still no roll number, return error
        $rollNumberStillEmpty = empty($validated['roll_number']) || trim($validated['roll_number'] ?? '') === '';
        if ($validated['status'] === 'active' && $rollNumberStillEmpty) {
            return back()
                ->withErrors(['roll_number' => 'Roll number is required when activating a student.'])
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
        
        // Handle password update (if provided)
        if (!empty($validated['password'])) {
            $plainPassword = $validated['password'];
            $validated['password'] = Hash::make($plainPassword);
            // Store encrypted plain password
            $student->password_plain_encrypted = $plainPassword;
        } else {
            unset($validated['password']);
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
                    // Reload student with relationships for roll number generation
                    $student->load(['institute', 'course.category']);
                    
                    // Check prerequisites
                    if (empty($student->institute->institute_code)) {
                        return back()
                            ->withErrors(['session' => 'Cannot generate roll number: Institute code is not set.'])
                            ->withInput();
                    }
                    
                    if (!$student->course || !$student->course->category || empty($student->course->category->roll_number_code)) {
                        return back()
                            ->withErrors(['session' => 'Cannot generate roll number: Course category does not have a roll number code.'])
                            ->withInput();
                    }
                    
                    $newRollNumber = RollNumberGenerator::generateForYear($student, $newYear);
                    $validated['roll_number'] = $newRollNumber;
                } catch (\Exception $e) {
                    return back()
                        ->withErrors(['session' => 'Failed to generate new roll number: ' . $e->getMessage()])
                        ->withInput();
                }
            }
            
            // Update admission_year to match new session
            $validated['admission_year'] = $newYear;
            
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
        
        // Update student
        $student->update($validated);
        
        // Update qualifications - delete existing and create new ones
        $student->qualifications()->delete();
        if (!empty($qualifications)) {
            foreach ($qualifications as $qualification) {
                if (!empty($qualification['examination']) && !empty($qualification['year_of_passing']) && $qualification['year_of_passing'] !== 'yyyy') {
                    Qualification::create([
                        'student_id' => $student->id,
                        'examination' => $qualification['examination'],
                        'year_of_passing' => $qualification['year_of_passing'] ?? null,
                        'board_university' => $qualification['board_university'] ?? null,
                        'percentage' => $qualification['percentage'] ?? null,
                        'cgpa' => $qualification['cgpa'] ?? null,
                        'subjects' => $qualification['subjects'] ?? null,
                    ]);
                }
            }
        }

        // Prepare success message
        $successMessage = 'Student details updated successfully.';
        if ($sessionChanged) {
            $successMessage .= ' Registration number updated to: ' . $validated['registration_number'];
            if (isset($validated['roll_number'])) {
                $successMessage .= ', Roll number updated to: ' . $validated['roll_number'];
            }
            $successMessage .= '. Old PDFs have been deleted and will regenerate with new numbers on next view.';
        }
        
        return redirect()->route('admin.students.index')
            ->with('success', $successMessage);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Generate a unique registration number for a new student.
     *
     * Format: REG-2025-00001 (consistent with PublicRegistrationController format)
     */
    protected function generateRegistrationNumber(int $instituteId): string
    {
        $year = date('Y');
        $prefix = 'REG';
        
        // Get the last registration number for this institute and year
        $lastStudent = Student::where('institute_id', $instituteId)
            ->where('registration_number', 'like', $prefix . '-' . $year . '%')
            ->orderBy('registration_number', 'desc')
            ->first();
        
        if ($lastStudent && $lastStudent->registration_number) {
            // Extract the sequence number from the last registration number
            // Format: REG-2025-00001 -> extract 00001
            $parts = explode('-', $lastStudent->registration_number);
            if (count($parts) >= 3) {
                $lastNumber = (int) $parts[2]; // Get the sequence part
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
        } else {
            $newNumber = 1;
        }
        
        // Ensure uniqueness by checking if the number already exists
        // This handles race conditions where multiple registrations happen simultaneously
        $maxAttempts = 100; // Safety limit
        $attempts = 0;
        
        do {
            $sequencePadded = str_pad($newNumber, 5, '0', STR_PAD_LEFT);
            $registrationNumber = "{$prefix}-{$year}-{$sequencePadded}";
            
            // Check if this registration number already exists
            $exists = Student::where('registration_number', $registrationNumber)->exists();
            
            if (!$exists) {
                return $registrationNumber;
            }
            
            $newNumber++;
            $attempts++;
        } while ($attempts < $maxAttempts);
        
        // Fallback: use timestamp-based number if we can't find a unique sequence
        $timestamp = time();
        return "{$prefix}-{$year}-" . substr($timestamp, -5);
    }

    /**
     * Generate registration number for a specific year (used when session changes)
     * Excludes current student from uniqueness check
     */
    protected function generateRegistrationNumberForYear(int $instituteId, string $year, int $excludeStudentId = null): string
    {
        $prefix = 'REG';
        
        // Get the last registration number for this institute and year
        $query = Student::where('institute_id', $instituteId)
            ->where('registration_number', 'like', $prefix . '-' . $year . '%');
        
        // Exclude current student from check
        if ($excludeStudentId) {
            $query->where('id', '!=', $excludeStudentId);
        }
        
        $lastStudent = $query->orderBy('registration_number', 'desc')->first();
        
        if ($lastStudent && $lastStudent->registration_number) {
            // Extract the sequence number from the last registration number
            // Format: REG-2022-00001 -> extract 00001
            $parts = explode('-', $lastStudent->registration_number);
            if (count($parts) >= 3) {
                $lastNumber = (int) $parts[2]; // Get the sequence part
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
        } else {
            $newNumber = 1;
        }
        
        // Ensure uniqueness by checking if the number already exists
        $maxAttempts = 100; // Safety limit
        $attempts = 0;
        
        do {
            $sequencePadded = str_pad($newNumber, 5, '0', STR_PAD_LEFT);
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
            
            $newNumber++;
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
