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
        // Must have: status = 'published', published_at IS NOT NULL, and verified_at IS NOT NULL
        $publishedSemesterResults = $student->semesterResults()
            ->where('status', 'published')
            ->whereNotNull('published_at') // Must have published_at timestamp
            ->whereNotNull('verified_at') // Must have verified_at timestamp (publish sets both)
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
