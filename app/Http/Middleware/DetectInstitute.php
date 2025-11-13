<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Institute;

class DetectInstitute
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        
        // Map local domains to production domains for matching
        $domainMap = [
            'mjpitm.local' => 'mjpitm.in',
            'mjpips.local' => 'mjpips.in',
            'www.mjpitm.local' => 'mjpitm.in',
            'www.mjpips.local' => 'mjpips.in',
        ];
        
        // For localhost/testing - allow query parameter to select institute
        if ($host === 'localhost' || $host === '127.0.0.1' || str_contains($host, '.local')) {
            // Check if institute_id is passed in query string for testing
            if ($request->has('institute_id')) {
                $institute = Institute::find($request->get('institute_id'));
                if ($institute) {
                    $request->attributes->set('institute_id', $institute->id);
                    $request->attributes->set('institute', $institute);
                    session(['current_institute_id' => $institute->id]);
                }
            } elseif (isset($domainMap[$host])) {
                // Map .local domain to production domain and find institute
                $productionDomain = $domainMap[$host];
                $institute = Institute::where('domain', $productionDomain)
                    ->where('status', 'active')
                    ->first();
                
                if ($institute) {
                    $request->attributes->set('institute_id', $institute->id);
                    $request->attributes->set('institute', $institute);
                    session(['current_institute_id' => $institute->id]);
                }
            } else {
                // Default to Tech Institute (ID 1) for localhost if not specified
                $institute = Institute::find(1);
                if ($institute) {
                    $request->attributes->set('institute_id', $institute->id);
                    $request->attributes->set('institute', $institute);
                    session(['current_institute_id' => $institute->id]);
                }
            }
        } else {
            // Production: Remove 'www.' prefix if present
            $domain = preg_replace('/^www\./', '', $host);
            
            // Find institute by domain
            $institute = Institute::where('domain', $domain)
                ->orWhere('domain', 'www.' . $domain)
                ->where('status', 'active')
                ->first();
            
            if ($institute) {
                // Set institute in request and session
                $request->attributes->set('institute_id', $institute->id);
                $request->attributes->set('institute', $institute);
                session(['current_institute_id' => $institute->id]);
            } else {
                // For admin panels or unknown domains, allow without institute
                if (!session()->has('current_institute_id') && $request->is('admin/*')) {
                    // Admin can select institute manually
                    session(['current_institute_id' => null]);
                }
            }
        }
        
        return $next($request);
    }
}
