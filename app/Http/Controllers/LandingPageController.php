<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Institute;

class LandingPageController extends Controller
{
    /**
     * Display the landing page based on domain
     */
    public function index(Request $request)
    {
        $institute = $request->attributes->get('institute');
        $instituteId = session('current_institute_id');

        // If no institute detected (admin access or localhost), show default
        if (!$institute) {
            return view('welcome');
        }

        // Map institute to view
        $instituteViews = [
            1 => 'institutes.tech.home',        // Tech Institute (mjpitm.in)
            2 => 'institutes.paramedical.home', // Paramedical Institute (mjpips.in)
        ];

        $viewName = $instituteViews[$instituteId] ?? 'welcome';

        return view($viewName, [
            'institute' => $institute,
        ]);
    }

    /**
     * Display about page for the institute
     */
    public function about(Request $request)
    {
        $institute = $request->attributes->get('institute');
        $instituteId = session('current_institute_id');

        if (!$institute) {
            return redirect('/');
        }

        $instituteViews = [
            1 => 'institutes.tech.about',
            2 => 'institutes.paramedical.about',
        ];

        $viewName = $instituteViews[$instituteId] ?? 'welcome';

        return view($viewName, [
            'institute' => $institute,
        ]);
    }

    /**
     * Display courses page for the institute
     */
    public function courses(Request $request)
    {
        $institute = $request->attributes->get('institute');
        $instituteId = session('current_institute_id');

        if (!$institute) {
            return redirect('/');
        }

        $courses = \App\Models\Course::where('institute_id', $instituteId)
            ->where('status', 'active')
            ->get();

        $instituteViews = [
            1 => 'institutes.tech.courses',
            2 => 'institutes.paramedical.courses',
        ];

        $viewName = $instituteViews[$instituteId] ?? 'welcome';

        return view($viewName, [
            'institute' => $institute,
            'courses' => $courses,
        ]);
    }
}
