<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fee;
use App\Models\Student;
use App\Services\StudentAuditLogger;
use Illuminate\Support\Facades\Auth;

class FeeController extends Controller
{
    private function resolveInstituteIdForScopedUser($user): int
    {
        if ($user->isSuperAdmin()) {
            return 0;
        }

        $instituteId = $user->institute_id ?: session('current_institute_id');
        if (!$instituteId) {
            abort(403, 'Institute is not assigned for this account.');
        }

        return (int) $instituteId;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $instituteId = $this->resolveInstituteIdForScopedUser($user);

        $query = Fee::with(['student', 'markedBy', 'verifiedBy']);
        if (!$user->isSuperAdmin()) {
            $query->where(function ($q) use ($instituteId, $user) {
                $q->whereHas('student', function ($studentQuery) use ($instituteId) {
                    $studentQuery->where('institute_id', $instituteId);
                })->orWhere(function ($independentFeeQuery) use ($user) {
                    $independentFeeQuery
                        ->whereNull('student_id')
                        ->where('marked_by', $user->id);
                });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Search by amount
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('amount', 'like', "%{$search}%");
        }

        $fees = $query->latest('payment_date')
            ->latest()
            ->paginate(resolve_per_page($request->query('per_page')))
            ->withQueryString();

        $statuses = [
            'pending_verification' => 'Pending Verification',
            'verified' => 'Verified',
            'rejected' => 'Rejected',
        ];

        $paymentTypes = [
            'tuition' => 'Tuition Fee',
        ];

        return view('admin.fees.index', compact('fees', 'statuses', 'paymentTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // No student information needed
        return view('admin.fees.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $instituteId = $this->resolveInstituteIdForScopedUser($user);

        $validated = $request->validate([
            'student_id' => ['nullable', 'exists:students,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_type' => ['nullable', 'string', 'max:50'],
            'payment_mode' => ['required', 'in:online,offline'],
            'payment_date' => ['required', 'date'],
            'remarks' => ['nullable', 'string'],
        ]);
        
        // Default payment type to tuition
        $validated['payment_type'] = $validated['payment_type'] ?? 'tuition';

        $validated['status'] = 'pending_verification';
        $validated['marked_by'] = $user->id;

        if (!$user->isSuperAdmin() && !empty($validated['student_id'])) {
            $studentBelongsToInstitute = Student::where('id', $validated['student_id'])
                ->where('institute_id', $instituteId)
                ->exists();
            if (!$studentBelongsToInstitute) {
                return redirect()->back()
                    ->withErrors(['student_id' => 'You can only add fees for students in your institute.'])
                    ->withInput();
            }
        }

        $fee = Fee::create($validated);
        if ($fee->student_id) {
            $student = Student::find($fee->student_id);
            if ($student) {
                StudentAuditLogger::logRelatedCreated($student, 'fee', $fee->only([
                    'id',
                    'amount',
                    'payment_type',
                    'payment_mode',
                    'semester',
                    'status',
                    'payment_date',
                    'remarks',
                    'approved_by_name',
                ]), $fee->id);
            }
        }

        return redirect()->route('admin.fees.index')
            ->with('success', 'Fee entry created successfully. It is now pending verification.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Fee $fee)
    {
        // Load relationships (student is optional since fees are independent)
        $fee->load(['student', 'markedBy', 'verifiedBy']);

        $user = Auth::user();
        $instituteId = $this->resolveInstituteIdForScopedUser($user);
        if (
            !$user->isSuperAdmin() &&
            (
                ($fee->student_id && (!$fee->student || (int) $fee->student->institute_id !== $instituteId)) ||
                (!$fee->student_id && (int) $fee->marked_by !== (int) $user->id)
            )
        ) {
            abort(403, 'You are not authorized to view this fee record.');
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

        // Validate approved_by_name (required for approval)
        $validated = $request->validate([
            'approved_by_name' => ['required', 'string', 'max:255'],
        ]);

        $before = $fee->only(['status', 'approved_by_name', 'verified_by', 'verified_at', 'remarks']);
        $fee->update([
            'status' => 'verified',
            'approved_by_name' => $validated['approved_by_name'],
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);
        if ($fee->student) {
            StudentAuditLogger::logRelatedUpdated($fee->student, 'fee', $before, $fee->only([
                'status',
                'approved_by_name',
                'verified_by',
                'verified_at',
                'remarks',
            ]), $fee->id);
        }

        return redirect()->back()
            ->with('success', 'Fee payment verified successfully. Approved by: ' . $validated['approved_by_name']);
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

        $before = $fee->only(['status', 'verified_by', 'verified_at', 'remarks']);
        $fee->update([
            'status' => 'rejected',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'remarks' => ($fee->remarks ? $fee->remarks . "\n\nRejection: " : 'Rejection: ') . $validated['rejection_remarks'],
        ]);
        if ($fee->student) {
            StudentAuditLogger::logRelatedUpdated($fee->student, 'fee', $before, $fee->only([
                'status',
                'verified_by',
                'verified_at',
                'remarks',
            ]), $fee->id);
        }

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

        $query = Fee::with(['student', 'markedBy'])
            ->where('status', 'pending_verification');

        // Search by amount or payment mode (fees are independent, no student search)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('amount', 'like', "%{$search}%")
                    ->orWhere('payment_mode', 'like', "%{$search}%")
                    ->orWhere('approved_by_name', 'like', "%{$search}%");
            });
        }

        $fees = $query->latest('payment_date')
            ->latest()
            ->paginate(resolve_per_page($request->query('per_page')))
            ->withQueryString();

        return view('admin.fees.verification-queue', compact('fees'));
    }
}
