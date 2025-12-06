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

        // Get all fees (no student filtering since student_id can be null)
        $query = Fee::with(['student', 'markedBy', 'verifiedBy']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Search by amount
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('amount', 'like', "%{$search}%");
        }

        $fees = $query->latest('payment_date')->latest()->paginate(15)->withQueryString();

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

        // Validate approved_by_name (required for approval)
        $validated = $request->validate([
            'approved_by_name' => ['required', 'string', 'max:255'],
        ]);

        $fee->update([
            'status' => 'verified',
            'approved_by_name' => $validated['approved_by_name'],
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

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
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('roll_number', 'like', "%{$search}%");
                });
            });
        }

        $fees = $query->latest('payment_date')->latest()->paginate(15)->withQueryString();

        return view('admin.fees.verification-queue', compact('fees'));
    }
}
