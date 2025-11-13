<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Institute;
use App\Models\User;
use App\Models\Student;
use App\Models\Course;

class DashboardController extends Controller
{
    /**
     * Display the super admin dashboard.
     */
    public function index()
    {
        // Get statistics across all institutes
        $totalInstitutes = Institute::where('status', 'active')->count();
        $totalUsers = User::where('role', '!=', 'student')->count();
        $totalStudents = Student::count();
        $totalCourses = Course::count();

        // Get all institutes
        $institutes = Institute::withCount(['students', 'courses'])->get();

        // Recent users
        $recentUsers = User::where('role', '!=', 'student')
            ->latest()
            ->take(5)
            ->get();

        return view('superadmin.dashboard', compact(
            'totalInstitutes',
            'totalUsers',
            'totalStudents',
            'totalCourses',
            'institutes',
            'recentUsers'
        ));
    }
}
