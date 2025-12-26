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
        $student->load(['course', 'course.institute', 'results.subject', 'semesterResults.results.subject']);

        // Get published semester results (new system)
        $publishedSemesterResults = $student->semesterResults()
            ->where('status', 'published')
            ->with(['results.subject'])
            ->orderBy('semester')
            ->get();

        // Get only published individual results (old system - for backward compatibility)
        $publishedResults = $student->results()
            ->where('status', 'published')
            ->whereNull('semester_result_id') // Only show old results that aren't part of semester results
            ->with('subject')
            ->latest('academic_year')
            ->latest('semester')
            ->get();

        return view('student.dashboard', compact('student', 'publishedResults', 'publishedSemesterResults'));
    }
}
