<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // Allow both Super Admin and Staff to access Super Admin routes
        if (!auth()->check() || (!$user->isSuperAdmin() && !$user->isStaff())) {
            abort(403, 'Unauthorized. Admin or Staff access required.');
        }

        return $next($request);
    }
}
