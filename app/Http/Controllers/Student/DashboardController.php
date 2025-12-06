<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the student dashboard.
     */
    public function index()
    {
        $student = Auth::guard('student')->user();
        
        // Load relationships
        $student->load(['course', 'course.institute', 'results.subject']);

        // Get only published results (students can only see published results)
        $publishedResults = $student->results()
            ->where('status', 'published')
            ->with('subject')
            ->latest('academic_year')
            ->latest('semester')
            ->get();

        return view('student.dashboard', compact('student', 'publishedResults'));
    }
}
