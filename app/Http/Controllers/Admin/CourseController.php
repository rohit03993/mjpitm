<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Institute;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // For all admins, load all courses with institute relationships
        $courses = Course::with('institute')
            ->withCount('students')
            ->latest()
            ->paginate(15);
        
        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get all active institutes for the dropdown
        $institutes = Institute::where('status', 'active')->get();
        
        return view('admin.courses.create', compact('institutes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Get institute ID - All admins can select institute
        $instituteId = $request->input('institute_id') ?? session('current_institute_id');
        
        // If no institute selected, try to use user's institute (for Institute Admin as fallback)
        if (!$instituteId && $user->institute_id) {
            $instituteId = $user->institute_id;
        }
        
        // Validate the request
        $validated = $request->validate([
            'institute_id' => ['required', 'exists:institutes,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:courses,code'],
            'duration_years' => ['required', 'integer', 'min:1', 'max:10'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
            
            // Fee fields
            'registration_fee' => ['nullable', 'numeric', 'min:0'],
            'entrance_fee' => ['nullable', 'numeric', 'min:0'],
            'enrollment_fee' => ['nullable', 'numeric', 'min:0'],
            'tuition_fee' => ['nullable', 'numeric', 'min:0'],
            'caution_money' => ['nullable', 'numeric', 'min:0'],
            'hostel_fee_amount' => ['nullable', 'numeric', 'min:0'],
            'late_fee' => ['nullable', 'numeric', 'min:0'],
        ]);
        
        // Create the course
        $course = Course::create($validated);
        
        return redirect()->route('admin.courses.index')
            ->with('success', 'Course created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        $course->load(['institute', 'students', 'subjects']);
        
        return view('admin.courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        // Get all active institutes for the dropdown
        $institutes = Institute::where('status', 'active')->get();
        
        return view('admin.courses.edit', compact('course', 'institutes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        // Validate the request
        $validated = $request->validate([
            'institute_id' => ['required', 'exists:institutes,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:courses,code,' . $course->id],
            'duration_years' => ['required', 'integer', 'min:1', 'max:10'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
            
            // Fee fields
            'registration_fee' => ['nullable', 'numeric', 'min:0'],
            'entrance_fee' => ['nullable', 'numeric', 'min:0'],
            'enrollment_fee' => ['nullable', 'numeric', 'min:0'],
            'tuition_fee' => ['nullable', 'numeric', 'min:0'],
            'caution_money' => ['nullable', 'numeric', 'min:0'],
            'hostel_fee_amount' => ['nullable', 'numeric', 'min:0'],
            'late_fee' => ['nullable', 'numeric', 'min:0'],
        ]);
        
        // Update the course
        $course->update($validated);
        
        return redirect()->route('admin.courses.index')
            ->with('success', 'Course updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        // Check if course has students
        if ($course->students()->count() > 0) {
            return redirect()->route('admin.courses.index')
                ->with('error', 'Cannot delete course. There are students enrolled in this course.');
        }
        
        $course->delete();
        
        return redirect()->route('admin.courses.index')
            ->with('success', 'Course deleted successfully.');
    }
}
