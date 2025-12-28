<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CourseCategory;
use App\Models\Institute;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CourseCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CourseCategory::with('institute')
            ->withCount('courses');

        // Filter by institute if provided
        if ($request->filled('institute_id')) {
            $query->where('institute_id', $request->input('institute_id'));
        }

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('roll_number_code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $categories = $query->orderBy('institute_id')
            ->orderBy('display_order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        // Get institutes for filter dropdown
        $institutes = Institute::where('status', 'active')->get(['id', 'name']);

        return view('admin.categories.index', compact('categories', 'institutes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get all active institutes for the dropdown
        $institutes = Institute::where('status', 'active')->get();

        return view('admin.categories.create', compact('institutes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'institute_id' => ['required', 'exists:institutes,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'roll_number_code' => ['nullable', 'string', 'max:3', 'regex:/^[0-9]{2,3}$/'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        // Check if category name already exists for this institute
        $existingCategory = CourseCategory::where('institute_id', $validated['institute_id'])
            ->where('name', $validated['name'])
            ->first();

        if ($existingCategory) {
            return redirect()->back()
                ->withErrors(['name' => 'A category with this name already exists for this institute.'])
                ->withInput();
        }

        // Set default display_order if not provided
        if (empty($validated['display_order'])) {
            $maxOrder = CourseCategory::where('institute_id', $validated['institute_id'])->max('display_order');
            $validated['display_order'] = ($maxOrder ?? 0) + 1;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        CourseCategory::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Course category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CourseCategory $category)
    {
        $category->load(['institute', 'courses']);

        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CourseCategory $category)
    {
        // Get all active institutes for the dropdown
        $institutes = Institute::where('status', 'active')->get();

        return view('admin.categories.edit', compact('category', 'institutes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CourseCategory $category)
    {
        $validated = $request->validate([
            'institute_id' => ['required', 'exists:institutes,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'roll_number_code' => ['nullable', 'string', 'max:3', 'regex:/^[0-9]{2,3}$/'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        // Check if category name already exists for this institute (excluding current)
        $existingCategory = CourseCategory::where('institute_id', $validated['institute_id'])
            ->where('name', $validated['name'])
            ->where('id', '!=', $category->id)
            ->first();

        if ($existingCategory) {
            return redirect()->back()
                ->withErrors(['name' => 'A category with this name already exists for this institute.'])
                ->withInput();
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Course category updated successfully.');
    }

    /**
     * Show the form for moving a category to another institute.
     */
    public function move(CourseCategory $category)
    {
        $category->load(['institute', 'courses']);
        
        // Get all active institutes except the current one
        $institutes = Institute::where('status', 'active')
            ->where('id', '!=', $category->institute_id)
            ->get();
        
        // Count students enrolled in courses of this category
        $studentsCount = Student::whereIn('course_id', $category->courses->pluck('id'))->count();
        
        return view('admin.categories.move', compact('category', 'institutes', 'studentsCount'));
    }

    /**
     * Process moving a category to another institute.
     */
    public function processMove(Request $request, CourseCategory $category)
    {
        $validated = $request->validate([
            'target_institute_id' => ['required', 'exists:institutes,id', 'different:current_institute_id'],
        ], [
            'target_institute_id.different' => 'The target institute must be different from the current institute.',
        ]);

        $targetInstituteId = $validated['target_institute_id'];
        
        // Check if category name already exists in target institute
        $existingCategory = CourseCategory::where('institute_id', $targetInstituteId)
            ->where('name', $category->name)
            ->first();

        if ($existingCategory) {
            return redirect()->back()
                ->withErrors(['target_institute_id' => "A category with the name '{$category->name}' already exists in the target institute. Please rename the category first or choose a different institute."])
                ->withInput();
        }

        // Load relationships
        $category->load(['courses']);
        $courses = $category->courses;
        $courseIds = $courses->pluck('id');

        // Count students that will be affected
        $studentsCount = Student::whereIn('course_id', $courseIds)->count();
        
        // Store old institute ID for logging
        $oldInstituteId = $category->institute_id;

        // Use database transaction to ensure data consistency
        DB::beginTransaction();
        
        try {
            // 1. Update category's institute_id
            $category->update(['institute_id' => $targetInstituteId]);

            // 2. Update all courses in this category to new institute
            $category->courses()->update(['institute_id' => $targetInstituteId]);

            // 3. Update all students enrolled in those courses to new institute
            Student::whereIn('course_id', $courseIds)
                ->update(['institute_id' => $targetInstituteId]);

            DB::commit();

            Log::info('Category moved successfully', [
                'category_id' => $category->id,
                'category_name' => $category->name,
                'old_institute_id' => $oldInstituteId,
                'new_institute_id' => $targetInstituteId,
                'courses_moved' => $courses->count(),
                'students_moved' => $studentsCount,
            ]);

            return redirect()->route('admin.categories.index')
                ->with('success', "Category '{$category->name}' and {$courses->count()} course(s) with {$studentsCount} student(s) have been successfully moved to the new institute.");

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error moving category', [
                'category_id' => $category->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'Failed to move category: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseCategory $category)
    {
        // Check if category has courses
        if ($category->courses()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Cannot delete category. There are courses assigned to this category. Please reassign or remove the courses first.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Course category deleted successfully.');
    }
}

