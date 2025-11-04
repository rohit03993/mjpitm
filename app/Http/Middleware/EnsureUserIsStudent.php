<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsStudent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('student')->check()) {
            return redirect()->route('student.login');
        }

        // Check if student's institute matches the current domain's institute
        $student = Auth::guard('student')->user();
        $instituteId = session('current_institute_id');
        
        if ($instituteId && $student->institute_id != $instituteId) {
            abort(403, 'You do not have access to this institute.');
        }

        return $next($request);
    }
}
