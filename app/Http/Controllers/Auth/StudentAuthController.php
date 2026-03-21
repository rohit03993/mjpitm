<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Logout any existing sessions (admin or staff)
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $identifier = $request->identifier;

        // Find student by registration number or roll number
        $student = Student::where('registration_number', $identifier)
            ->orWhere('roll_number', $identifier)
            ->first();

        // Check if student exists and password matches
        if (!$student || !Hash::check($request->password, $student->password)) {
            throw ValidationException::withMessages([
                'identifier' => __('The provided credentials do not match our records.'),
            ]);
        }

        // Institute context: set by DetectInstitute from the request host (mjpitm.in, mjpips.in, etc.).
        // If the host is an IP or unknown domain, session may be empty — bind to the student's institute
        // after password check so login still works on VPS/raw URLs.
        $sessionInstituteId = session('current_institute_id');
        if ($sessionInstituteId !== null && (int) $sessionInstituteId !== (int) $student->institute_id) {
            throw ValidationException::withMessages([
                'identifier' => __('You do not have access to this institute.'),
            ]);
        }
        session(['current_institute_id' => $student->institute_id]);

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

        return redirect()->route('login.options');
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
     * Generates a one-time token (60 min), stores in cache. Redirects to a page that shows the reset link.
     * No email required; student uses the link to set a new password. No existing data is overwritten except password on reset.
     */
    public function sendPasswordResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'identifier' => ['required', 'string'],
        ]);

        $student = Student::where('registration_number', $request->identifier)
            ->orWhere('roll_number', $request->identifier)
            ->first();

        if (!$student) {
            return redirect()->route('student.password.reset.sent')
                ->with('status', 'If an account exists with that identifier, a reset link has been prepared.');
        }

        $token = Str::random(64);
        Cache::put('student_reset_' . $token, $student->id, now()->addMinutes(60));
        $resetUrl = route('student.password.reset', ['token' => $token]);

        return redirect()->route('student.password.reset.sent')
            ->with('reset_link', $resetUrl)
            ->with('status', 'Use the link below within 60 minutes to set a new password. Do not share this link.');
    }

    /**
     * Show the "reset link sent" page (displays the one-time link).
     */
    public function showResetLinkSent(): View
    {
        return view('student.reset-password-sent');
    }

    /**
     * Show the password reset form (with token in URL).
     */
    public function showResetForm(Request $request, string $token): View|RedirectResponse
    {
        $studentId = Cache::get('student_reset_' . $token);
        if (!$studentId) {
            return redirect()->route('student.login')
                ->with('error', 'This reset link has expired or is invalid. Please request a new one.');
        }
        return view('student.reset-password', ['token' => $token]);
    }

    /**
     * Reset the student's password using the token.
     * Only updates password; no other data is changed or deleted.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $studentId = Cache::get('student_reset_' . $request->token);
        if (!$studentId) {
            return back()->withErrors(['token' => 'This reset link has expired or is invalid. Please request a new one.']);
        }

        $student = Student::find($studentId);
        if (!$student) {
            return back()->withErrors(['token' => 'Invalid reset link.']);
        }

        $plain = $request->password;
        $student->update([
            'password' => Hash::make($plain),
            'password_plain_encrypted' => encrypt($plain),
        ]);
        Cache::forget('student_reset_' . $request->token);

        return redirect()->route('student.login')
            ->with('status', 'Your password has been reset. You can now log in with your new password.');
    }
}
