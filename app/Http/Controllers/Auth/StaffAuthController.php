<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class StaffAuthController extends Controller
{
    /**
     * Display the staff login form.
     */
    public function showLoginForm(): View
    {
        return view('auth.staff-login');
    }

    /**
     * Handle an incoming staff authentication request.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Logout any existing sessions (student or super admin)
        Auth::guard('student')->logout();
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Attempt to authenticate as admin/staff
        if (!Auth::guard('web')->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('The provided credentials do not match our records.'),
            ]);
        }

        $user = Auth::guard('web')->user();

        // Check if user is NOT admin (institute admin only - Staff and Super Admin use Admin Login)
        if ($user->canAccessAdminLogin()) {
            Auth::guard('web')->logout();
            throw ValidationException::withMessages([
                'email' => __('Admin/Staff should use Admin Login.'),
            ]);
        }

        $request->session()->regenerate();

        // Redirect to staff dashboard
        return redirect()->intended('/staff/dashboard');
    }

    /**
     * Destroy an authenticated staff session.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.options');
    }
}

