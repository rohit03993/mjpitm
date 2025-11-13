<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class StudentAuthController extends Controller
{
    /**
     * Display the student login form.
     */
    public function showLoginForm(): View
    {
        return view('student.login');
    }

    /**
     * Handle an incoming student authentication request.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'roll_number' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Find student by roll number
        $student = Student::where('roll_number', $request->roll_number)
            ->where('status', 'active')
            ->first();

        // Check if student exists and password matches
        if (!$student || !Hash::check($request->password, $student->password)) {
            throw ValidationException::withMessages([
                'roll_number' => __('The provided credentials do not match our records.'),
            ]);
        }

        // Check if student's institute matches the current domain's institute
        $instituteId = session('current_institute_id');
        if ($instituteId && $student->institute_id != $instituteId) {
            throw ValidationException::withMessages([
                'roll_number' => __('You do not have access to this institute.'),
            ]);
        }

        // Log in the student
        Auth::guard('student')->login($student, $request->boolean('remember'));

        $request->session()->regenerate();

        return redirect()->intended(route('student.dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated student session.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('student')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('student.login');
    }

    /**
     * Display the password reset request form for students.
     */
    public function showPasswordRequestForm(): View
    {
        return view('student.forgot-password');
    }

    /**
     * Handle a password reset link request for students.
     */
    public function sendPasswordResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'roll_number' => ['required', 'string', 'exists:students,roll_number'],
        ]);

        // TODO: Implement password reset email functionality
        // For now, just return with a message
        return back()->with('status', 'Password reset functionality will be implemented soon.');
    }
}
