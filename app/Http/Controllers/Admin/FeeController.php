<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fee;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class FeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Fee::with(['student.course', 'student.institute', 'markedBy', 'verifiedBy']);

        // Role-based filtering: Staff sees only fees for students they created
        if (!$user->isSuperAdmin()) {
            $query->whereHas('student', function ($q) use ($user) {
                $q->where('created_by', $user->id);
            });
        }

        // Filter by student
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->input('student_id'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by payment type
        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->input('payment_type'));
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->input('date_to'));
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%")
                            ->orWhere('roll_number', 'like', "%{$search}%")
                            ->orWhere('registration_number', 'like', "%{$search}%");
                    });
            });
        }

        $fees = $query->latest('payment_date')->latest()->paginate(15)->withQueryString();

        // Get students for filter dropdown (role-based)
        if ($user->isSuperAdmin()) {
            $students = Student::orderBy('name')->get(['id', 'name', 'roll_number', 'registration_number']);
        } else {
            // Staff sees only students they created
            $students = Student::where('created_by', $user->id)
                ->orderBy('name')
                ->get(['id', 'name', 'roll_number', 'registration_number']);
        }

        $statuses = [
            'pending_verification' => 'Pending Verification',
            'verified' => 'Verified',
            'rejected' => 'Rejected',
        ];

        $paymentTypes = [
            'tuition' => 'Tuition Fee',
        ];

        return view('admin.fees.index', compact('fees', 'students', 'statuses', 'paymentTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = Auth::user();

        // Get students for dropdown (role-based)
        // For Staff: only students they created
        // For Super Admin: all active students
        if ($user->isSuperAdmin()) {
            $students = Student::where('status', 'active')
                ->with(['course', 'institute'])
                ->orderBy('name')
                ->get();
        } else {
            $students = Student::where('created_by', $user->id)
                ->with(['course', 'institute'])
                ->orderBy('name')
                ->get();
        }

        $paymentTypes = [
            'tuition' => 'Tuition Fee',
            'semester' => 'Semester Fee',
            'annual' => 'Annual Fee',
            'exam' => 'Exam Fee',
            'hostel' => 'Hostel Fee',
            'other' => 'Other',
        ];

        // Handle pre-selected student from query parameter
        $selectedStudentId = $request->input('student_id');
        $selectedStudent = null;
        if ($selectedStudentId) {
            $selectedStudent = $students->firstWhere('id', $selectedStudentId);
        }

        return view('admin.fees.create', compact('students', 'paymentTypes', 'selectedStudentId', 'selectedStudent'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_type' => ['nullable', 'string', 'max:50'],
            'payment_date' => ['required', 'date'],
            'remarks' => ['nullable', 'string'],
        ]);
        
        // Default payment type to tuition
        $validated['payment_type'] = $validated['payment_type'] ?? 'tuition';

        // Check permission: Staff can only add fees for students they created
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            $student = Student::findOrFail($validated['student_id']);
            if ($student->created_by !== $user->id) {
                return redirect()->back()
                    ->withErrors(['student_id' => 'You can only add fees for students you registered.'])
                    ->withInput();
            }
        }

        $validated['status'] = 'pending_verification';
        $validated['marked_by'] = Auth::id();

        Fee::create($validated);

        return redirect()->route('admin.fees.index')
            ->with('success', 'Fee entry created successfully. It is now pending verification.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Fee $fee)
    {
        $fee->load(['student.course', 'student.institute', 'markedBy', 'verifiedBy']);

        // Check permission
        $user = Auth::user();
        if (!$user->isSuperAdmin() && $fee->student->institute_id !== $user->institute_id) {
            abort(403, 'You are not authorized to view this fee.');
        }

        return view('admin.fees.show', compact('fee'));
    }

    /**
     * Verify a fee payment (Admin only).
     */
    public function verify(Request $request, Fee $fee)
    {
        // Check permission - Only Super Admin can verify
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Only Admin can verify fee payments.');
        }

        if ($fee->status !== 'pending_verification') {
            return redirect()->back()
                ->with('error', 'Only pending verification fees can be verified.');
        }

        // Validate transaction ID (required for approval)
        $validated = $request->validate([
            'transaction_id' => ['required', 'string', 'max:255'],
        ]);

        $fee->update([
            'status' => 'verified',
            'transaction_id' => $validated['transaction_id'],
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Fee payment verified successfully with Transaction ID: ' . $validated['transaction_id']);
    }

    /**
     * Reject a fee payment (Admin only).
     */
    public function reject(Request $request, Fee $fee)
    {
        // Only Super Admin can reject
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Only Admin can reject fee payments.');
        }

        if ($fee->status !== 'pending_verification') {
            return redirect()->back()
                ->with('error', 'Only pending verification fees can be rejected.');
        }

        $validated = $request->validate([
            'rejection_remarks' => ['required', 'string', 'max:500'],
        ]);

        $fee->update([
            'status' => 'rejected',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'remarks' => ($fee->remarks ? $fee->remarks . "\n\nRejection: " : 'Rejection: ') . $validated['rejection_remarks'],
        ]);

        return redirect()->route('admin.fees.index')
            ->with('success', 'Fee payment rejected.');
    }

    /**
     * Show verification queue (pending fees) - Super Admin only.
     */
    public function verificationQueue(Request $request)
    {
        $user = Auth::user();

        // Only Super Admin can access verification queue
        if (!$user->isSuperAdmin()) {
            abort(403, 'Only Admin can access the verification queue.');
        }

        $query = Fee::with(['student.course', 'student.institute', 'markedBy'])
            ->where('status', 'pending_verification');

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%")
                            ->orWhere('roll_number', 'like', "%{$search}%");
                    });
            });
        }

        $fees = $query->latest('payment_date')->latest()->paginate(15)->withQueryString();

        return view('admin.fees.verification-queue', compact('fees'));
    }
}
