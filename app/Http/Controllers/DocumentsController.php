<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentsController extends Controller
{
    /**
     * View the student registration form PDF in browser
     */
    public function viewRegistrationForm($studentId = null)
    {
        try {
            // If student ID is provided, generate form for that student
            if ($studentId) {
                $student = Student::with(['institute', 'course', 'qualifications', 'creator'])->findOrFail($studentId);
                
                // Check if user has permission to view this student
                $user = auth()->user();
                if (!$user->isSuperAdmin() && $student->created_by !== $user->id) {
                    return redirect()->back()
                        ->with('error', 'You do not have permission to view this student\'s registration form.');
                }
                
                // Generate PDF from student data with optimized settings
                $pdf = Pdf::loadView('pdf.registration-form', compact('student'));
                $pdf->setPaper('A4', 'portrait');
                $pdf->setOption('enable-local-file-access', true);
                $pdf->setOption('isHtml5ParserEnabled', true);
                $pdf->setOption('isRemoteEnabled', false);
                
                $fileName = 'Registration-Form-' . ($student->registration_number ?? $student->id) . '.pdf';
                
                // Stream PDF to browser (view first)
                return $pdf->stream($fileName);
            }
            
            // If no student ID, redirect back with error
            return redirect()->back()
                ->with('error', 'Student ID is required to generate registration form.');
                
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Registration form generation error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'An error occurred while generating the registration form. Please try again or contact the administrator.');
        }
    }

    /**
     * Download the student registration form PDF
     */
    public function downloadRegistrationForm($studentId = null)
    {
        try {
            // If student ID is provided, generate form for that student
            if ($studentId) {
                $student = Student::with(['institute', 'course', 'qualifications', 'creator'])->findOrFail($studentId);
                
                // Check if user has permission to view this student
                $user = auth()->user();
                if (!$user->isSuperAdmin() && $student->created_by !== $user->id) {
                    return redirect()->back()
                        ->with('error', 'You do not have permission to download this student\'s registration form.');
                }
                
                // Generate PDF from student data with optimized settings
                $pdf = Pdf::loadView('pdf.registration-form', compact('student'));
                $pdf->setPaper('A4', 'portrait');
                $pdf->setOption('enable-local-file-access', true);
                $pdf->setOption('isHtml5ParserEnabled', true);
                $pdf->setOption('isRemoteEnabled', false);
                
                $fileName = 'Registration-Form-' . ($student->registration_number ?? $student->id) . '.pdf';
                
                return $pdf->download($fileName);
            }
            
            // If no student ID, redirect back with error
            return redirect()->back()
                ->with('error', 'Student ID is required to generate registration form.');
                
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Registration form generation error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'An error occurred while generating the registration form. Please try again or contact the administrator.');
        }
    }

}

