<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CourseCategory;
use App\Models\Institute;
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
            'description' => ['nullable', 'string'],
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
            'description' => ['nullable', 'string'],
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

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Course category updated successfully.');
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

