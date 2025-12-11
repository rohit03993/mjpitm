<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Qualification;
use App\Models\Institute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PublicRegistrationController extends Controller
{
    /**
     * Show the public registration form
     */
    public function create(Request $request)
    {
        // Get institute from request (set by DetectInstitute middleware)
        $institute = $request->attributes->get('institute');
        $instituteId = session('current_institute_id');

        // If no institute detected, redirect to home
        if (!$institute || !$instituteId) {
            return redirect()->route('home')->with('error', 'Please access registration from the correct institute website.');
        }

        // Get category and course from URL parameters (if provided)
        $categorySlug = $request->query('category');
        $courseSlug = $request->query('course');
        
        $selectedCategory = null;
        $selectedCourse = null;

        // Find category by slug if provided (improved matching)
        if ($categorySlug) {
            $selectedCategory = CourseCategory::where('institute_id', $instituteId)
                ->where('status', 'active')
                ->get()
                ->first(function($cat) use ($categorySlug) {
                    // Try exact slug match
                    $categorySlugMatch = Str::slug($cat->name);
                    if ($categorySlugMatch === $categorySlug) {
                        return true;
                    }
                    
                    // Try case-insensitive name match
                    $categoryNameLower = strtolower(str_replace(['/', '-'], ' ', $cat->name));
                    $searchName = strtolower(str_replace('-', ' ', $categorySlug));
                    if (strpos($categoryNameLower, $searchName) !== false || strpos($searchName, $categoryNameLower) !== false) {
                        return true;
                    }
                    
                    // Try code match
                    if ($cat->code && strtolower($cat->code) === strtolower($categorySlug)) {
                        return true;
                    }
                    
                    return false;
                });
        }

        // Find course by slug if provided (improved matching)
        if ($courseSlug) {
            $selectedCourse = Course::where('institute_id', $instituteId)
                ->where('status', 'active')
                ->get()
                ->first(function($course) use ($courseSlug) {
                    // Try exact slug match
                    $courseSlugMatch = Str::slug($course->name);
                    if ($courseSlugMatch === $courseSlug) {
                        return true;
                    }
                    
                    // Try case-insensitive name match
                    $courseNameLower = strtolower(str_replace(['/', '-'], ' ', $course->name));
                    $searchName = strtolower(str_replace('-', ' ', $courseSlug));
                    if (strpos($courseNameLower, $searchName) !== false || strpos($searchName, $courseNameLower) !== false) {
                        return true;
                    }
                    
                    // Try code match
                    if ($course->code && strtolower($course->code) === strtolower($courseSlug)) {
                        return true;
                    }
                    
                    return false;
                });
            
            // If course found and category not set, get course's category
            if ($selectedCourse && $selectedCourse->category_id && !$selectedCategory) {
                $selectedCategory = CourseCategory::find($selectedCourse->category_id);
            }
        }

        // Get courses for this institute only
        $courses = Course::where('institute_id', $instituteId)
            ->where('status', 'active')
            ->with('category')
            ->orderBy('name')
            ->get();

        // Get categories for this institute
        $categories = CourseCategory::where('institute_id', $instituteId)
            ->orderBy('name')
            ->get();

        // Pass courses as JSON for JavaScript filtering
        $coursesJson = $courses->map(function($course) {
            return [
                'id' => $course->id,
                'name' => $course->name,
                'category_id' => $course->category_id,
                'tuition_fee' => $course->tuition_fee ?? 0,
                'registration_fee' => ($course->registration_fee && $course->registration_fee > 0) ? $course->registration_fee : 1000,
                'total_fee' => $course->total_fee,
                'formatted_duration' => $course->formatted_duration,
                'duration_months' => $course->duration_months ?? 0,
            ];
        })->toJson();

        // Pass categories as JSON for JavaScript filtering
        $categoriesJson = $categories->map(function($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
            ];
        })->toJson();

        return view('public.registration', compact(
            'courses', 
            'categories', 
            'coursesJson', 
            'categoriesJson', 
            'institute', 
            'instituteId',
            'selectedCategory',
            'selectedCourse'
        ));
    }

    /**
     * Store the public registration
     */
    public function store(Request $request)
    {
        // Get institute from session (set by DetectInstitute middleware)
        $instituteId = session('current_institute_id');

        // If no institute detected, return error
        if (!$instituteId) {
            return redirect()->back()->withErrors(['institute_id' => 'Please access registration from the correct institute website.'])->withInput();
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
            
            // Contact Details
            'email' => ['nullable', 'email', 'max:255', Rule::unique('students', 'email')],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'district' => ['nullable', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'pin_code' => ['required', 'string', 'max:10'],
            'country' => ['nullable', 'string', 'max:255'],
            'nationality' => ['nullable', 'string', 'max:255'],
            
            // Course Details
            'course_id' => ['required', 'exists:courses,id'],
            'session' => ['required', 'string', 'max:255'],
            
            // Login Credentials
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            
            // Academic Certificates
            'certificate_class_10th' => ['nullable', 'file', 'mimes:pdf,jpeg,png,jpg', 'max:5120'],
            'certificate_class_12th' => ['nullable', 'file', 'mimes:pdf,jpeg,png,jpg', 'max:5120'],
            'certificate_graduation' => ['nullable', 'file', 'mimes:pdf,jpeg,png,jpg', 'max:5120'],
            'certificate_others' => ['nullable', 'file', 'mimes:pdf,jpeg,png,jpg', 'max:5120'],
            
            // Declaration
            'declaration_accepted' => ['required', 'accepted'],
            
            // Qualifications
            'qualifications' => ['nullable', 'array'],
            'qualifications.*.examination' => ['nullable', 'string', 'max:255'],
            'qualifications.*.year_of_passing' => ['nullable', 'string', 'max:255'],
            'qualifications.*.board_university' => ['nullable', 'string', 'max:255'],
            'qualifications.*.percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'qualifications.*.cgpa' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'qualifications.*.subjects' => ['nullable', 'string', 'max:500'],
        ]);

        // Verify course belongs to the institute
        $course = Course::findOrFail($validated['course_id']);
        if ($course->institute_id != $instituteId) {
            return redirect()->back()->withErrors(['course_id' => 'Selected course does not belong to this institute.'])->withInput();
        }

        // Handle file uploads
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('students/photos', 'public');
        }
        
        if ($request->hasFile('signature')) {
            $validated['signature'] = $request->file('signature')->store('students/signatures', 'public');
        }
        
        if ($request->hasFile('aadhar_front')) {
            $validated['aadhar_front'] = $request->file('aadhar_front')->store('students/aadhar', 'public');
        }
        
        if ($request->hasFile('aadhar_back')) {
            $validated['aadhar_back'] = $request->file('aadhar_back')->store('students/aadhar', 'public');
        }

        // Handle academic certificate uploads
        if ($request->hasFile('certificate_class_10th')) {
            $validated['certificate_class_10th'] = $request->file('certificate_class_10th')->store('students/certificates', 'public');
        }
        if ($request->hasFile('certificate_class_12th')) {
            $validated['certificate_class_12th'] = $request->file('certificate_class_12th')->store('students/certificates', 'public');
        }
        if ($request->hasFile('certificate_graduation')) {
            $validated['certificate_graduation'] = $request->file('certificate_graduation')->store('students/certificates', 'public');
        }
        if ($request->hasFile('certificate_others')) {
            $validated['certificate_others'] = $request->file('certificate_others')->store('students/certificates', 'public');
        }

        // Store plain password before hashing (for encrypted storage)
        $plainPassword = $validated['password'];
        $validated['password'] = Hash::make($plainPassword);

        // Set institute and status
        $validated['institute_id'] = $instituteId;
        $validated['status'] = 'pending';
        $validated['created_by'] = null; // Public registration, no creator
        
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

        // Generate registration number
        $validated['registration_number'] = $this->generateRegistrationNumber($instituteId);

        // Extract qualifications
        $qualifications = $validated['qualifications'] ?? [];
        unset($validated['qualifications']);
        unset($validated['declaration_accepted']);

        // Create student
        $student = Student::create($validated);
        
        // Also store encrypted plain password for Super Admin viewing
        $student->setPlainPassword($plainPassword);
        $student->save();

        // Create notification for new website registration
        \App\Models\RegistrationNotification::create([
            'student_id' => $student->id,
            'institute_id' => $instituteId,
            'registration_type' => 'website',
        ]);

        // Create qualifications
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

        // Redirect to success page or login
        return redirect()->route('public.registration.success', $student->id)
            ->with('success', 'Registration submitted successfully! Your registration number is: ' . $student->registration_number);
    }

    /**
     * Show registration success page
     */
    public function success($studentId)
    {
        $instituteId = session('current_institute_id');
        $student = Student::findOrFail($studentId);
        
        // Verify student belongs to current institute (security check)
        if ($instituteId && $student->institute_id != $instituteId) {
            abort(403, 'Unauthorized access.');
        }
        
        return view('public.registration-success', compact('student'));
    }

    /**
     * Generate unique registration number
     */
    private function generateRegistrationNumber($instituteId)
    {
        $institute = Institute::find($instituteId);
        $prefix = $instituteId == 1 ? 'MJPITM' : 'MJPIPS';
        $year = date('Y');
        
        // Get last registration number for this institute and year
        $lastStudent = Student::where('institute_id', $instituteId)
            ->where('registration_number', 'like', $prefix . '-' . $year . '%')
            ->orderBy('registration_number', 'desc')
            ->first();
        
        if ($lastStudent && $lastStudent->registration_number) {
            $lastNumber = (int) substr($lastStudent->registration_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . '-' . $year . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}

