<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Course;
use App\Models\Institute;
use App\Models\Fee;

class DashboardController extends Controller
{
    /**
     * Display the staff dashboard.
     * Staff can only see students they have added.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Redirect super admin and staff to super admin dashboard (they use Admin Login)
        if ($user->canAccessAdminLogin()) {
            return redirect()->route('superadmin.dashboard');
        }

        // Staff can only see students they created
        $myStudents = Student::where('created_by', $user->id)
            ->with(['institute', 'course'])
            ->latest()
            ->get();

        $myStudentIds = $myStudents->pluck('id');

        // Statistics for staff's own students
        $totalStudents = $myStudents->count();
        $activeStudents = $myStudents->where('status', 'active')->count();
        $pendingStudents = $myStudents->where('status', 'pending')->count();
        
        // Group students by institute
        $studentsByInstitute = $myStudents->groupBy('institute_id');
        
        // Get institute details
        $institutes = [];
        foreach ($studentsByInstitute as $instituteId => $students) {
            $institute = Institute::find($instituteId);
            if ($institute) {
                $institutes[] = [
                    'institute' => $institute,
                    'students_count' => $students->count(),
                    'active_count' => $students->where('status', 'active')->count(),
                ];
            }
        }

        // Recent students (last 10)
        $recentStudents = $myStudents->take(10);

        // Total VERIFIED fees collected from students I registered
        $totalFeesCollected = Fee::whereIn('student_id', $myStudentIds)
            ->where('status', 'verified')
            ->sum('amount');
        
        // Pending fees (waiting for admin approval)
        $pendingFees = Fee::whereIn('student_id', $myStudentIds)
            ->where('status', 'pending_verification')
            ->sum('amount');

        return view('staff.dashboard', compact(
            'user',
            'totalStudents',
            'activeStudents',
            'pendingStudents',
            'institutes',
            'recentStudents',
            'totalFeesCollected',
            'pendingFees'
        ));
    }
}

