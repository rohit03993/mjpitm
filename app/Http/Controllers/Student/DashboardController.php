<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the student dashboard.
     */
    public function index()
    {
        $student = Auth::guard('student')->user();
        
        // Load relationships
        $student->load(['course', 'fees', 'results']);

        return view('student.dashboard', compact('student'));
    }
}
