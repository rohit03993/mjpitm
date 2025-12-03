<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Subject::with('course.institute');

        // Filter by course if provided
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->input('course_id'));
        }

        // Filter by semester if provided
        if ($request->filled('semester')) {
            $query->where('semester', $request->input('semester'));
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $subjects = $query->latest()->paginate(15)->withQueryString();

        // Get courses for filter dropdown
        $courses = Course::where('status', 'active')
            ->with('institute')
            ->orderBy('name')
            ->get();

        return view('admin.subjects.index', compact('subjects', 'courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get all active courses with their institutes
        $courses = Course::where('status', 'active')
            ->with('institute')
            ->orderBy('name')
            ->get();

        return view('admin.subjects.create', compact('courses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255'],
            'credits' => ['nullable', 'integer', 'min:0', 'max:10'],
            'semester' => ['required', 'integer', 'min:1', 'max:20'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        // Check if subject code already exists for this course
        $existingSubject = Subject::where('course_id', $validated['course_id'])
            ->where('code', $validated['code'])
            ->first();

        if ($existingSubject) {
            return redirect()->back()
                ->withErrors(['code' => 'A subject with this code already exists for this course.'])
                ->withInput();
        }

        Subject::create($validated);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Subject $subject)
    {
        $subject->load(['course.institute', 'results.student']);

        return view('admin.subjects.show', compact('subject'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subject $subject)
    {
        // Get all active courses with their institutes
        $courses = Course::where('status', 'active')
            ->with('institute')
            ->orderBy('name')
            ->get();

        return view('admin.subjects.edit', compact('subject', 'courses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255'],
            'credits' => ['nullable', 'integer', 'min:0', 'max:10'],
            'semester' => ['required', 'integer', 'min:1', 'max:20'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        // Check if subject code already exists for this course (excluding current subject)
        $existingSubject = Subject::where('course_id', $validated['course_id'])
            ->where('code', $validated['code'])
            ->where('id', '!=', $subject->id)
            ->first();

        if ($existingSubject) {
            return redirect()->back()
                ->withErrors(['code' => 'A subject with this code already exists for this course.'])
                ->withInput();
        }

        $subject->update($validated);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject)
    {
        // Check if subject has results
        if ($subject->results()->count() > 0) {
            return redirect()->route('admin.subjects.index')
                ->with('error', 'Cannot delete subject. There are results associated with this subject.');
        }

        $subject->delete();

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject deleted successfully.');
    }
}

