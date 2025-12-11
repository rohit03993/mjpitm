<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class SuperAdminAuthController extends Controller
{
    /**
     * Display the super admin login form.
     */
    public function showLoginForm(): View
    {
        return view('auth.superadmin-login');
    }

    /**
     * Handle an incoming super admin authentication request.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Logout any existing sessions (student or staff)
        Auth::guard('student')->logout();
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Attempt to authenticate as admin
        if (!Auth::guard('web')->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('The provided credentials do not match our records.'),
            ]);
        }

        $user = Auth::guard('web')->user();

        // Check if user can access admin login (super admin or staff)
        if (!$user->canAccessAdminLogin()) {
            Auth::guard('web')->logout();
            throw ValidationException::withMessages([
                'email' => __('You do not have Admin access. Please use Guest Login.'),
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended('/superadmin/dashboard');
    }

    /**
     * Destroy an authenticated super admin session.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.options');
    }
}

