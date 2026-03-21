<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DocumentsController extends Controller
{
    /**
     * View the student registration form PDF in browser
     */
    public function viewRegistrationForm($studentId = null)
    {
        try {
            if (! $studentId) {
                return redirect()->back()
                    ->with('error', 'Student ID is required to generate registration form.');
            }

            $student = Student::with(['institute', 'course', 'qualifications', 'creator'])->findOrFail($studentId);
            $this->assertCanAccessRegistrationPdf($student);

            return $this->makeRegistrationPdf($student)->stream($this->registrationFileName($student));
        } catch (ModelNotFoundException $e) {
            throw $e;
        } catch (HttpException $e) {
            throw $e;
        } catch (\Throwable $e) {
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
            if (! $studentId) {
                return redirect()->back()
                    ->with('error', 'Student ID is required to generate registration form.');
            }

            $student = Student::with(['institute', 'course', 'qualifications', 'creator'])->findOrFail($studentId);
            $this->assertCanAccessRegistrationPdf($student);

            return $this->makeRegistrationPdf($student)->download($this->registrationFileName($student));
        } catch (ModelNotFoundException $e) {
            throw $e;
        } catch (HttpException $e) {
            throw $e;
        } catch (\Throwable $e) {
            \Log::error('Registration form generation error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while generating the registration form. Please try again or contact the administrator.');
        }
    }

    /**
     * Student guard: own record only. Web guard: same rules as {@see StudentController::show}.
     */
    private function assertCanAccessRegistrationPdf(Student $student): void
    {
        if (auth()->guard('student')->check()) {
            if ((int) $student->id !== (int) auth()->guard('student')->id()) {
                abort(403, 'You are not authorized to access this registration form.');
            }

            return;
        }

        $user = auth()->guard('web')->user();
        if (! $user instanceof User) {
            abort(403, 'You are not authorized to access this registration form.');
        }

        if (! $user->canViewStudentRecord($student)) {
            abort(403, 'You are not authorized to access this registration form.');
        }
    }

    private function makeRegistrationPdf(Student $student)
    {
        $pdf = Pdf::loadView('pdf.registration-form', compact('student'));
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('enable-local-file-access', true);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', false);

        return $pdf;
    }

    private function registrationFileName(Student $student): string
    {
        return 'Registration-Form-' . ($student->registration_number ?? $student->id) . '.pdf';
    }
}
