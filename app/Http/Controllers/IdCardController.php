<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class IdCardController extends Controller
{
    /**
     * View ID Card in browser (HTML preview)
     */
    public function view(Student $student)
    {
        // Check if student is active and has roll number
        if ($student->status !== 'active' || empty($student->roll_number)) {
            return redirect()->back()
                ->with('error', 'ID Card can only be generated for active students with a roll number.');
        }

        // Check permission - Super Admin can view all, Staff can view students they created
        $user = Auth::user();
        if (!$user->isSuperAdmin() && $student->created_by !== $user->id) {
            abort(403, 'You are not authorized to view this student\'s ID card.');
        }

        // Load relationships
        $student->load(['institute', 'course']);

        return view('pdf.id-card-preview', compact('student'));
    }

    /**
     * Download ID Card as PDF
     */
    public function download(Student $student)
    {
        // Check if student is active and has roll number
        if ($student->status !== 'active' || empty($student->roll_number)) {
            return redirect()->back()
                ->with('error', 'ID Card can only be generated for active students with a roll number.');
        }

        // Check permission - Super Admin can view all, Staff can view students they created
        $user = Auth::user();
        if (!$user->isSuperAdmin() && $student->created_by !== $user->id) {
            abort(403, 'You are not authorized to download this student\'s ID card.');
        }

        $student->load(['institute', 'course']);

        $pdf = Pdf::loadView('pdf.id-card', compact('student'));
        $pdf->setPaper([0, 0, 243, 153], 'portrait'); // Credit card size: 85.6mm x 53.98mm (in points)

        $filename = 'ID-Card-' . $student->roll_number . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Student views their own ID Card (preview)
     */
    public function studentPreview()
    {
        $student = Auth::guard('student')->user();

        // Check if student is active and has roll number
        if ($student->status !== 'active' || empty($student->roll_number)) {
            return redirect()->back()
                ->with('error', 'Your ID Card is not available yet. Please wait for admin approval.');
        }

        $student->load(['institute', 'course']);

        return view('pdf.id-card-preview', compact('student'));
    }

    /**
     * Student downloads their own ID Card
     */
    public function studentDownload()
    {
        $student = Auth::guard('student')->user();

        // Check if student is active and has roll number
        if ($student->status !== 'active' || empty($student->roll_number)) {
            return redirect()->back()
                ->with('error', 'Your ID Card is not available yet. Please wait for admin approval.');
        }

        $student->load(['institute', 'course']);

        $pdf = Pdf::loadView('pdf.id-card', compact('student'));
        $pdf->setPaper([0, 0, 243, 153], 'portrait'); // Credit card size

        $filename = 'ID-Card-' . $student->roll_number . '.pdf';

        return $pdf->download($filename);
    }
}

