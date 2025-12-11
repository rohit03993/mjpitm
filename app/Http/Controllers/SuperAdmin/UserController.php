<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Institute;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of admins.
     */
    public function index()
    {
        $admins = User::whereIn('role', ['super_admin', 'institute_admin', 'staff'])
            ->with('institute')
            ->latest()
            ->paginate(15);

        return view('superadmin.users.index', compact('admins'));
    }

    /**
     * Show the form for creating a new admin.
     */
    public function create()
    {
        $institutes = Institute::where('status', 'active')->get(['id', 'name']);

        return view('superadmin.users.create', compact('institutes'));
    }

    /**
     * Store a newly created admin in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(['institute_admin', 'staff'])], // Super Admin cannot be created
            'institute_id' => ['nullable', 'exists:institutes,id'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'institute_id' => $validated['institute_id'] ?? null,
            'status' => $validated['status'],
        ]);
        
        // Also store encrypted plain password for Super Admin viewing
        $user->setPlainPassword($validated['password']);
        $user->save();

        return redirect()->route('superadmin.users.index')
            ->with('success', 'Admin created successfully.');
    }

    /**
     * Show the form for editing the specified admin.
     */
    public function edit(User $user)
    {
        $adminsOnly = ['super_admin', 'institute_admin', 'staff'];
        if (!in_array($user->role, $adminsOnly, true)) {
            abort(404);
        }

        $institutes = Institute::where('status', 'active')->get(['id', 'name']);

        return view('superadmin.users.edit', [
            'admin' => $user,
            'institutes' => $institutes,
        ]);
    }

    /**
     * Update the specified admin in storage.
     */
    public function update(Request $request, User $user)
    {
        $adminsOnly = ['super_admin', 'institute_admin', 'staff'];
        if (!in_array($user->role, $adminsOnly, true)) {
            abort(404);
        }

        // Prevent changing Super Admin role
        $allowedRoles = $user->isSuperAdmin() 
            ? ['super_admin'] // If already super admin, keep it
            : ['institute_admin', 'staff']; // Otherwise, can only be these roles

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in($allowedRoles)],
            'institute_id' => ['nullable', 'exists:institutes,id'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        // Only update role if user is not super admin (super admin role cannot be changed)
        if (!$user->isSuperAdmin()) {
            $user->role = $validated['role'];
        }
        $user->institute_id = $validated['institute_id'] ?? null;
        $user->status = $validated['status'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
            // Also store encrypted plain password for Super Admin viewing
            $user->setPlainPassword($validated['password']);
        }

        $user->save();

        return redirect()->route('superadmin.users.index')
            ->with('success', 'Admin updated successfully.');
    }
}


