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
        
        // Load relationships (avoid loading all legacy Result rows for counts — use semester results for status)
        $student->load(['course', 'course.institute', 'semesterResults.results.subject']);

        // Per-semester status card: online result vs marksheet issue (not subject row counts)
        $semesterResultsOverview = $student->semesterResults()
            ->with(['results:id,semester_result_id,status'])
            ->orderBy('semester')
            ->get();

        // Get published semester results (new system)
        // Uses the trulyPublished scope to ensure consistency across the application
        $publishedSemesterResults = $student->semesterResults()
            ->trulyPublished()
            ->with([
                'results' => function ($q) {
                    $q->where('status', 'published')
                        ->with('subject')
                        ->orderBy('subject_id');
                },
            ])
            ->orderBy('semester')
            ->get();

        return view('student.dashboard', compact(
            'student',
            'publishedSemesterResults',
            'semesterResultsOverview'
        ));
    }
}
