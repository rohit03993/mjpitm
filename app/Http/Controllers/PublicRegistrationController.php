<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Qualification;
use App\Models\Institute;
use Illuminate\Http\Request;
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

        // Single query each: categories and courses (with category) — avoids duplicate queries and speeds up the form
        $categories = CourseCategory::where('institute_id', $instituteId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $courses = Course::where('institute_id', $instituteId)
            ->where('status', 'active')
            ->with('category')
            ->orderBy('name')
            ->get();

        $categorySlug = $request->query('category');
        $courseSlug = $request->query('course');
        $selectedCategory = null;
        $selectedCourse = null;

        if ($categorySlug) {
            $selectedCategory = $categories->first(function ($cat) use ($categorySlug) {
                if (Str::slug($cat->name) === $categorySlug) return true;
                $nameLower = strtolower(str_replace(['/', '-'], ' ', $cat->name));
                $search = strtolower(str_replace('-', ' ', $categorySlug));
                if (str_contains($nameLower, $search) || str_contains($search, $nameLower)) return true;
                return $cat->code && strtolower($cat->code) === strtolower($categorySlug);
            });
        }

        if ($courseSlug) {
            $selectedCourse = $courses->first(function ($course) use ($courseSlug) {
                if (Str::slug($course->name) === $courseSlug) return true;
                $nameLower = strtolower(str_replace(['/', '-'], ' ', $course->name));
                $search = strtolower(str_replace('-', ' ', $courseSlug));
                if (str_contains($nameLower, $search) || str_contains($search, $nameLower)) return true;
                return $course->code && strtolower($course->code) === strtolower($courseSlug);
            });
            if ($selectedCourse && $selectedCourse->category_id && !$selectedCategory) {
                $selectedCategory = $categories->firstWhere('id', $selectedCourse->category_id);
            }
        }

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

        // Guests see only the current session (no dropdown of all years)
        $currentYear = (int) date('Y');
        $currentSessionForGuest = $currentYear . '-' . substr((string) ($currentYear + 1), -2);

        return view('public.registration', compact(
            'courses', 
            'categories', 
            'coursesJson', 
            'categoriesJson', 
            'institute', 
            'instituteId',
            'selectedCategory',
            'selectedCourse',
            'currentSessionForGuest'
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

        // Current session only for guests (no past/future years)
        $currentYear = (int) date('Y');
        $currentSessionOnly = $currentYear . '-' . substr((string) ($currentYear + 1), -2);

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
            
            // Contact Details
            'email' => ['nullable', 'email', 'max:255', Rule::unique('students', 'email')],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'district' => ['nullable', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'pin_code' => ['required', 'string', 'max:10'],
            'country' => ['nullable', 'string', 'max:255'],
            'nationality' => ['nullable', 'string', 'max:255'],
            
            // Course Details (course must belong to the selected institute)
            'course_id' => ['required', Rule::exists('courses', 'id')->where('institute_id', $instituteId)],
            'session' => ['required', 'string', 'max:255', 'regex:/^\d{4}-\d{2}$/'],
            
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
        ], [], [
            'session.regex' => 'Session must be in YYYY-YY format (e.g. 2025-26).',
        ]);

        // Enforce current session only for public registration (ignore any tampered value)
        $validated['session'] = $currentSessionOnly;

        // Password = date of birth in DDMMYYYY format (e.g. 03091992); student can change after login
        $credentials = Student::passwordCredentialsFromDateOfBirth($validated['date_of_birth']);
        if (! $credentials) {
            return redirect()->back()->withErrors(['date_of_birth' => 'Invalid date of birth.'])->withInput();
        }
        $validated = array_merge($validated, $credentials);

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
        // Generate registration number using session year
        $sessionYear = null;
        if (isset($validated['session']) && !empty($validated['session'])) {
            $sessionParts = explode('-', $validated['session']);
            $sessionYear = $sessionParts[0] ?? null;
        }
        $validated['registration_number'] = $this->generateRegistrationNumber($instituteId, $sessionYear);

        // Extract qualifications
        $qualifications = $validated['qualifications'] ?? [];
        unset($validated['qualifications']);
        unset($validated['declaration_accepted']);

        // Ensure session is saved (it's required, so it should be in validated)
        if (!isset($validated['session']) || empty($validated['session'])) {
            \Log::warning('Student registration: Session field is missing!', [
                'url' => $request->fullUrl(),
                'institute_id' => session('current_institute_id'),
                'has_session_input' => $request->has('session'),
            ]);
            return redirect()->back()
                ->withErrors(['session' => 'Session is required. Please select a session.'])
                ->withInput();
        }

        // Create student
        $student = Student::create($validated);
        
        // Log to verify session was saved
        \Log::info('Student created with session', [
            'student_id' => $student->id,
            'session' => $student->session,
            'registration_number' => $student->registration_number
        ]);

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
        if (!$instituteId) {
            abort(403, 'Unauthorized access.');
        }

        $student = Student::findOrFail($studentId);
        
        // Verify student belongs to current institute (security check)
        if ((int) $student->institute_id !== (int) $instituteId) {
            abort(403, 'Unauthorized access.');
        }
        
        $institute = $student->institute;
        return view('public.registration-success', compact('student', 'institute'));
    }

    /**
     * Generate unique registration number
     * Format: REG-{YEAR}-{STUDENT_NUMBER}
     * Example: REG-2025-05000 (starting from 5000)
     * Uses session year if provided, otherwise uses current year
     * 
     * @param int $instituteId
     * @param string|null $sessionYear Year from session (e.g., "2025" from "2025-26")
     * @return string
     */
    private function generateRegistrationNumber($instituteId, ?string $sessionYear = null)
    {
        $prefix = 'REG';
        
        // Use session year if provided, otherwise fallback to current year
        $year = $sessionYear ?? date('Y');
        
        // Validate year format
        if (!is_numeric($year) || strlen($year) !== 4) {
            $year = date('Y'); // Fallback to current year if invalid
        }
        
        // Get last registration number for this year (format: REG-{YEAR}-{NUMBER})
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
}

