<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Institute;
use Illuminate\Validation\Rule;

class InstituteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $institutes = Institute::withCount(['students', 'courses', 'admins'])
            ->latest()
            ->paginate(15);

        return view('superadmin.institutes.index', compact('institutes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('superadmin.institutes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'institute_code' => ['nullable', 'string', 'max:10', 'regex:/^[A-Z0-9]+$/'],
            'domain' => ['required', 'string', 'max:255', 'unique:institutes,domain'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        Institute::create($validated);

        return redirect()->route('superadmin.institutes.index')
            ->with('success', 'Institute created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Institute $institute)
    {
        $institute->loadCount(['students', 'courses', 'admins']);
        $institute->load(['courses', 'admins']);

        return view('superadmin.institutes.show', compact('institute'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Institute $institute)
    {
        return view('superadmin.institutes.edit', compact('institute'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Institute $institute)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'institute_code' => ['nullable', 'string', 'max:10', 'regex:/^[A-Z0-9]+$/'],
            'domain' => ['required', 'string', 'max:255', Rule::unique('institutes', 'domain')->ignore($institute->id)],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $institute->update($validated);

        return redirect()->route('superadmin.institutes.index')
            ->with('success', 'Institute updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Institute $institute)
    {
        // Check if institute has students or courses
        if ($institute->students()->count() > 0 || $institute->courses()->count() > 0) {
            return redirect()->route('superadmin.institutes.index')
                ->with('error', 'Cannot delete institute. There are students or courses associated with this institute.');
        }

        $institute->delete();

        return redirect()->route('superadmin.institutes.index')
            ->with('success', 'Institute deleted successfully.');
    }
}

