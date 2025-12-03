<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Course;
use App\Models\Qualification;
use App\Models\CourseCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Student::with(['course', 'qualifications', 'institute', 'creator']);

        // Role-based visibility:
        // - Super Admin: sees all students (optional filters)
        // - Normal Admin: sees only students they created
        if (!$user->isSuperAdmin()) {
            $query->where('created_by', $user->id);
        }

        // Filters
        if ($request->filled('institute_id')) {
            $query->where('institute_id', $request->input('institute_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('roll_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
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

        return view('admin.students.index', compact('students', 'institutes', 'statuses'));
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
                'duration_years' => $course->duration_years ?? 1,
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
            'passport_number' => ['nullable', 'string', 'max:255'],
            'is_employed' => ['nullable', 'boolean'],
            'employer_name' => ['nullable', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            
            // Communication Details
            'email' => ['nullable', 'email', 'max:255', 'unique:students,email'],
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
            'institute_id' => ['nullable', 'exists:institutes,id'], // For Super Admin
            'course_id' => ['required', 'exists:courses,id'],
            'session' => ['nullable', 'string', 'max:255'],
            'mode_of_study' => ['required', 'in:regular,distance'],
            'admission_type' => ['nullable', 'string', 'max:255'],
            'hostel_facility_required' => ['nullable', 'boolean'],
            'admission_year' => ['required', 'string', 'max:255'],
            'current_semester' => ['nullable', 'integer', 'min:1'],
            'stream' => ['nullable', 'string', 'max:255'],
            
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
            
            // Password
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            
            // Qualifications
            'qualifications' => ['nullable', 'array'],
            'qualifications.*.examination' => ['nullable', 'in:secondary,sr_secondary,graduation,post_graduation,other'],
            'qualifications.*.year_of_passing' => ['nullable', 'string'],
            'qualifications.*.board_university' => ['nullable', 'string'],
            'qualifications.*.percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'qualifications.*.cgpa' => ['nullable', 'string'],
            'qualifications.*.subjects' => ['nullable', 'string'],
        ]);
        
        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('student-photos', 'public');
            $validated['photo'] = $photoPath;
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
        $validated['password'] = Hash::make($validated['password']);
        // New students start as pending until approved by admin / super admin
        $validated['status'] = 'pending';
        $validated['declaration_accepted'] = true;
        
        // Handle boolean fields
        $validated['is_employed'] = $request->has('is_employed') && $request->is_employed == '1';
        $validated['hostel_facility_required'] = $request->has('hostel_facility') && $request->hostel_facility == '1';
        $validated['pay_in_installment'] = $request->has('pay_in_installment') && $request->pay_in_installment == '1';
        
        // Generate a unique registration number for the student
        $validated['registration_number'] = $this->generateRegistrationNumber($instituteId);

        // Remove qualifications from validated data (will be handled separately)
        $qualifications = $validated['qualifications'] ?? [];
        unset($validated['qualifications']);
        
        // Create student
        $student = Student::create($validated);
        
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
        
        // Create initial fee entry if total_deposit > 0
        if ($student->total_deposit && $student->total_deposit > 0) {
            $student->fees()->create([
                'amount' => $student->total_deposit,
                'payment_type' => 'registration',
                'semester' => $student->current_semester ?? 1,
                'status' => 'pending_verification',
                'payment_date' => $student->deposit_date ?? now(),
                'transaction_id' => $student->bank_account ?? null,
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

        $student = Student::with(['institute', 'course', 'qualifications', 'creator', 'fees.markedBy', 'fees.verifiedBy'])->findOrFail($id);

        // Normal admins (staff) can view only students they created
        if (!$user->isSuperAdmin() && $student->created_by !== $user->id) {
            abort(403, 'You are not authorized to view this student.');
        }

        // Calculate fee summary
        $totalCourseFee = $student->course->tuition_fee ?? 0;
        $verifiedPayments = $student->fees->where('status', 'verified')->sum('amount');
        $pendingPayments = $student->fees->where('status', 'pending_verification')->sum('amount');
        $remainingBalance = $totalCourseFee - $verifiedPayments;

        return view('admin.students.show', compact('student', 'totalCourseFee', 'verifiedPayments', 'pendingPayments', 'remainingBalance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $student = Student::with(['institute', 'course', 'creator'])->findOrFail($id);

        // Only super admins can assign roll numbers / change status for now
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Only Super Admin can update student status and roll number.');
        }

        $statuses = [
            'pending' => 'Pending',
            'active' => 'Active',
            'inactive' => 'Inactive',
            'rejected' => 'Rejected',
        ];

        return view('admin.students.edit', compact('student', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $student = Student::findOrFail($id);

        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Only Super Admin can update student status and roll number.');
        }

        $validated = $request->validate([
            'roll_number' => ['nullable', 'string', 'max:255', Rule::unique('students', 'roll_number')->ignore($student->id)],
            'status' => ['required', Rule::in(['pending', 'active', 'inactive', 'rejected'])],
        ]);

        // If status is active, ensure roll number is present
        if ($validated['status'] === 'active' && empty($validated['roll_number'])) {
            return back()
                ->withErrors(['roll_number' => 'Roll number is required when activating a student.'])
                ->withInput();
        }

        $student->roll_number = $validated['roll_number'] ?? $student->roll_number;
        $student->status = $validated['status'];
        $student->save();

        return redirect()->route('admin.students.index')
            ->with('success', 'Student status and roll number updated successfully.');
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
     * Format example: REG-2025-00123 or REG-2025-TECH-00123 (we can refine later)
     */
    protected function generateRegistrationNumber(int $instituteId): string
    {
        $year = date('Y');

        // Count existing students for this year (optional per institute)
        $sequence = Student::whereYear('created_at', $year)
            ->where('institute_id', $instituteId)
            ->count() + 1;

        $sequencePadded = str_pad($sequence, 5, '0', STR_PAD_LEFT);

        return "REG-{$year}-{$sequencePadded}";
    }
}
