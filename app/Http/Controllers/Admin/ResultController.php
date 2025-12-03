<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Result;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Result::with(['student.course', 'student.institute', 'subject', 'uploadedBy', 'verifiedBy']);

        // Role-based filtering: Institute Admin sees only their institute's students
        if (!$user->isSuperAdmin() && $user->institute_id) {
            $query->whereHas('student', function ($q) use ($user) {
                $q->where('institute_id', $user->institute_id);
            });
        }

        // Filter by student
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->input('student_id'));
        }

        // Filter by subject
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->input('subject_id'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by exam type
        if ($request->filled('exam_type')) {
            $query->where('exam_type', $request->input('exam_type'));
        }

        // Filter by semester
        if ($request->filled('semester')) {
            $query->where('semester', $request->input('semester'));
        }

        // Filter by academic year
        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->input('academic_year'));
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('roll_number', 'like', "%{$search}%")
                        ->orWhere('registration_number', 'like', "%{$search}%");
                })->orWhereHas('subject', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            });
        }

        $results = $query->latest('academic_year')->latest('semester')->latest()->paginate(15)->withQueryString();

        // Get students for filter dropdown (role-based)
        if ($user->isSuperAdmin()) {
            $students = Student::where('status', 'active')->orderBy('name')->get(['id', 'name', 'roll_number', 'registration_number']);
            $subjects = Subject::where('status', 'active')->with('course')->orderBy('name')->get();
        } else {
            $students = Student::where('status', 'active')
                ->where('institute_id', $user->institute_id)
                ->orderBy('name')
                ->get(['id', 'name', 'roll_number', 'registration_number']);
            $subjects = Subject::whereHas('course', function ($q) use ($user) {
                $q->whereHas('institute', function ($iq) use ($user) {
                    $iq->where('id', $user->institute_id);
                });
            })->where('status', 'active')->with('course')->orderBy('name')->get();
        }

        $statuses = [
            'pending_verification' => 'Pending Verification',
            'published' => 'Published',
            'rejected' => 'Rejected',
        ];

        $examTypes = [
            'midterm' => 'Midterm',
            'final' => 'Final',
            'assignment' => 'Assignment',
            'practical' => 'Practical',
            'internal' => 'Internal',
            'external' => 'External',
            'other' => 'Other',
        ];

        return view('admin.results.index', compact('results', 'students', 'subjects', 'statuses', 'examTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();

        // Get students for dropdown (role-based)
        if ($user->isSuperAdmin()) {
            $students = Student::where('status', 'active')
                ->with(['course', 'institute'])
                ->orderBy('name')
                ->get();
            $subjects = Subject::where('status', 'active')->with('course')->orderBy('name')->get();
        } else {
            $students = Student::where('status', 'active')
                ->where('institute_id', $user->institute_id)
                ->with(['course', 'institute'])
                ->orderBy('name')
                ->get();
            $subjects = Subject::whereHas('course', function ($q) use ($user) {
                $q->whereHas('institute', function ($iq) use ($user) {
                    $iq->where('id', $user->institute_id);
                });
            })->where('status', 'active')->with('course')->orderBy('name')->get();
        }

        $examTypes = [
            'midterm' => 'Midterm',
            'final' => 'Final',
            'assignment' => 'Assignment',
            'practical' => 'Practical',
            'internal' => 'Internal',
            'external' => 'External',
            'other' => 'Other',
        ];

        return view('admin.results.create', compact('students', 'subjects', 'examTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'exam_type' => ['required', 'in:midterm,final,assignment,practical,internal,external,other'],
            'semester' => ['required', 'string', 'max:255'],
            'academic_year' => ['required', 'string', 'max:255'],
            'marks_obtained' => ['required', 'numeric', 'min:0'],
            'total_marks' => ['required', 'numeric', 'min:0'],
            'remarks' => ['nullable', 'string'],
        ]);

        // Check if student belongs to user's institute (for Institute Admin)
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            $student = Student::findOrFail($validated['student_id']);
            if ($student->institute_id !== $user->institute_id) {
                return redirect()->back()
                    ->withErrors(['student_id' => 'You can only add results for students in your institute.'])
                    ->withInput();
            }
        }

        // Check if marks_obtained is not greater than total_marks
        if ($validated['marks_obtained'] > $validated['total_marks']) {
            return redirect()->back()
                ->withErrors(['marks_obtained' => 'Marks obtained cannot be greater than total marks.'])
                ->withInput();
        }

        $validated['status'] = 'pending_verification';
        $validated['uploaded_by'] = Auth::id();

        // Percentage and grade will be auto-calculated by the Result model
        Result::create($validated);

        return redirect()->route('admin.results.index')
            ->with('success', 'Result entry created successfully. It is now pending verification.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Result $result)
    {
        $result->load(['student.course', 'student.institute', 'subject', 'uploadedBy', 'verifiedBy']);

        // Check permission
        $user = Auth::user();
        if (!$user->isSuperAdmin() && $result->student->institute_id !== $user->institute_id) {
            abort(403, 'You are not authorized to view this result.');
        }

        return view('admin.results.show', compact('result'));
    }

    /**
     * Verify and publish a result.
     */
    public function verify(Request $request, Result $result)
    {
        // Check permission
        $user = Auth::user();
        if (!$user->isSuperAdmin() && $result->student->institute_id !== $user->institute_id) {
            abort(403, 'You are not authorized to verify this result.');
        }

        if ($result->status !== 'pending_verification') {
            return redirect()->back()
                ->with('error', 'Only pending verification results can be verified.');
        }

        $result->update([
            'status' => 'published',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'published_at' => now(),
        ]);

        return redirect()->route('admin.results.index')
            ->with('success', 'Result verified and published successfully.');
    }

    /**
     * Reject a result.
     */
    public function reject(Request $request, Result $result)
    {
        // Check permission
        $user = Auth::user();
        if (!$user->isSuperAdmin() && $result->student->institute_id !== $user->institute_id) {
            abort(403, 'You are not authorized to reject this result.');
        }

        if ($result->status !== 'pending_verification') {
            return redirect()->back()
                ->with('error', 'Only pending verification results can be rejected.');
        }

        $validated = $request->validate([
            'rejection_remarks' => ['required', 'string', 'max:500'],
        ]);

        $result->update([
            'status' => 'rejected',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'remarks' => ($result->remarks ? $result->remarks . "\n\nRejection: " : 'Rejection: ') . $validated['rejection_remarks'],
        ]);

        return redirect()->route('admin.results.index')
            ->with('success', 'Result rejected.');
    }

    /**
     * Publish a verified result (if it was verified but not published).
     */
    public function publish(Request $request, Result $result)
    {
        // Check permission
        $user = Auth::user();
        if (!$user->isSuperAdmin() && $result->student->institute_id !== $user->institute_id) {
            abort(403, 'You are not authorized to publish this result.');
        }

        if ($result->status !== 'published') {
            return redirect()->back()
                ->with('error', 'Only verified results can be published.');
        }

        $result->update([
            'published_at' => now(),
        ]);

        return redirect()->route('admin.results.index')
            ->with('success', 'Result published successfully.');
    }

    /**
     * Show verification queue (pending results).
     */
    public function verificationQueue(Request $request)
    {
        $user = Auth::user();

        $query = Result::with(['student.course', 'student.institute', 'subject', 'uploadedBy'])
            ->where('status', 'pending_verification');

        // Role-based filtering
        if (!$user->isSuperAdmin() && $user->institute_id) {
            $query->whereHas('student', function ($q) use ($user) {
                $q->where('institute_id', $user->institute_id);
            });
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('roll_number', 'like', "%{$search}%");
                })->orWhereHas('subject', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                });
            });
        }

        $results = $query->latest('academic_year')->latest('semester')->latest()->paginate(15)->withQueryString();

        return view('admin.results.verification-queue', compact('results'));
    }
}
