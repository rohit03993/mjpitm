<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Institute;
use App\Models\CourseCategory;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // For all admins, load all courses with institute and category relationships
        $courses = Course::with(['institute', 'category'])
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
        
        // Get all active categories with their institutes for the dropdown
        $categories = CourseCategory::where('status', 'active')
            ->with('institute')
            ->orderBy('institute_id')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();
        
        // Pass categories as JSON for JavaScript filtering
        $categoriesJson = $categories->map(function($category) {
            return [
                'id' => $category->id,
                'institute_id' => $category->institute_id,
                'name' => $category->name,
            ];
        })->toJson();
        
        return view('admin.courses.create', compact('institutes', 'categories', 'categoriesJson'));
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
            'category_id' => ['nullable', 'exists:course_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:courses,code'],
            'duration_value' => ['required', 'numeric', 'min:0.1'],
            'duration_type' => ['required', 'in:months,years'],
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
        
        // Convert duration to months
        if ($validated['duration_type'] === 'years') {
            $validated['duration_months'] = (int)($validated['duration_value'] * 12);
        } else {
            $validated['duration_months'] = (int)$validated['duration_value'];
        }
        
        // Remove temporary fields
        unset($validated['duration_value'], $validated['duration_type']);
        
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
        
        // Get all active categories with their institutes for the dropdown
        $categories = CourseCategory::where('status', 'active')
            ->with('institute')
            ->orderBy('institute_id')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();
        
        // Pass categories as JSON for JavaScript filtering
        $categoriesJson = $categories->map(function($category) {
            return [
                'id' => $category->id,
                'institute_id' => $category->institute_id,
                'name' => $category->name,
            ];
        })->toJson();
        
        // Calculate duration value and type for the form
        $durationMonths = $course->duration_months ?? 0;
        $durationValue = $durationMonths;
        $durationType = 'months';
        
        // If duration is a multiple of 12, show as years
        if ($durationMonths > 0 && $durationMonths % 12 == 0) {
            $durationValue = $durationMonths / 12;
            $durationType = 'years';
        }
        
        return view('admin.courses.edit', compact('course', 'institutes', 'categories', 'categoriesJson', 'durationValue', 'durationType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        // Validate the request
        $validated = $request->validate([
            'institute_id' => ['required', 'exists:institutes,id'],
            'category_id' => ['nullable', 'exists:course_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:courses,code,' . $course->id],
            'duration_value' => ['required', 'numeric', 'min:0.1'],
            'duration_type' => ['required', 'in:months,years'],
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
        
        // Convert duration to months
        if ($validated['duration_type'] === 'years') {
            $validated['duration_months'] = (int)($validated['duration_value'] * 12);
        } else {
            $validated['duration_months'] = (int)$validated['duration_value'];
        }
        
        // Remove temporary fields
        unset($validated['duration_value'], $validated['duration_type']);
        
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
