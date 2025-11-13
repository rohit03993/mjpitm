<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Course;
use App\Models\Fee;
use App\Models\Result;

class DashboardController extends Controller
{
    /**
     * Display the institute admin dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // For super admin, use session institute_id; for institute admin, use their institute_id
        if ($user->isSuperAdmin()) {
            $instituteId = session('current_institute_id');
        } else {
            $instituteId = $user->institute_id ?? session('current_institute_id');
        }
        
        // If no institute ID found, redirect to home
        if (!$instituteId) {
            return redirect()->route('home')->with('error', 'No institute selected.');
        }

        // Get statistics for the institute
        $totalStudents = Student::where('institute_id', $instituteId)->count();
        $activeStudents = Student::where('institute_id', $instituteId)->where('status', 'active')->count();
        $totalCourses = Course::where('institute_id', $instituteId)->count();
        $activeCourses = Course::where('institute_id', $instituteId)->where('status', 'active')->count();
        
        // Fee statistics
        $totalFees = Fee::whereHas('student', function($query) use ($instituteId) {
            $query->where('institute_id', $instituteId);
        })->count();
        $verifiedFees = Fee::whereHas('student', function($query) use ($instituteId) {
            $query->where('institute_id', $instituteId);
        })->where('status', 'verified')->count();
        $pendingFees = Fee::whereHas('student', function($query) use ($instituteId) {
            $query->where('institute_id', $instituteId);
        })->where('status', 'pending_verification')->count();
        
        // Result statistics
        $totalResults = Result::whereHas('student', function($query) use ($instituteId) {
            $query->where('institute_id', $instituteId);
        })->count();
        $publishedResults = Result::whereHas('student', function($query) use ($instituteId) {
            $query->where('institute_id', $instituteId);
        })->where('status', 'published')->count();
        $pendingResults = Result::whereHas('student', function($query) use ($instituteId) {
            $query->where('institute_id', $instituteId);
        })->where('status', 'pending_verification')->count();

        // Recent students
        $recentStudents = Student::where('institute_id', $instituteId)
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalStudents',
            'activeStudents',
            'totalCourses',
            'activeCourses',
            'totalFees',
            'verifiedFees',
            'pendingFees',
            'totalResults',
            'publishedResults',
            'pendingResults',
            'recentStudents'
        ));
    }
}
