<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\SemesterResult;
use App\Models\Result;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class SemesterResultController extends Controller
{
    /**
     * Show form to generate semester result for a student
     */
    public function create(Student $student)
    {
        $user = Auth::user();
        
        // Only Super Admin can generate results
        if (!$user->isSuperAdmin()) {
            abort(403, 'Only Super Admin can generate results.');
        }

        $student->load(['course', 'institute']);

        // First check if there are ANY subjects for this course
        $totalSubjects = Subject::where('course_id', $student->course_id)
            ->where('status', 'active')
            ->count();

        if ($totalSubjects === 0) {
            return redirect()->route('admin.students.show', $student)
                ->with('error', 'No subjects have been added for this course. Please add subjects first before generating results.');
        }

        // Get all semesters that have subjects defined for this course
        $availableSemesters = Subject::where('course_id', $student->course_id)
            ->where('status', 'active')
            ->distinct()
            ->pluck('semester')
            ->sort()
            ->values();

        // Find next semester to generate
        // Priority: 1) Draft results (can be edited), 2) Published results with zero marks (can be regenerated), 3) Unpublished semesters
        $nextSemester = null;
        $existingDraftResult = null;
        
        foreach ($availableSemesters as $sem) {
            // First check if there's a draft result for this semester (can be edited)
            $draftResult = SemesterResult::where('student_id', $student->id)
                ->where('semester', $sem)
                ->where('status', 'draft')
                ->first();
            
            if ($draftResult) {
                $nextSemester = $sem;
                $existingDraftResult = $draftResult;
                break;
            }
            
            // Check if there's a published result with zero marks (can be regenerated)
            $publishedResult = SemesterResult::where('student_id', $student->id)
                ->where('semester', $sem)
                ->where('status', 'published')
                ->first();
            
            if ($publishedResult) {
                // Check if the published result has actual marks (not all zeros)
                // If total_marks_obtained is 0 or null, allow regenerating
                if ($publishedResult->total_marks_obtained == 0 || $publishedResult->total_marks_obtained === null) {
                    $nextSemester = $sem;
                    break;
                }
                // If it has marks, skip this semester
                continue;
            }
            
            // No result exists for this semester, use it
            $nextSemester = $sem;
            break;
        }

        if (!$nextSemester) {
            return redirect()->route('admin.students.show', $student)
                ->with('error', 'All available semesters have been published for this student.');
        }
        
        // If there's a draft result, redirect to edit it instead
        if ($existingDraftResult) {
            return redirect()->route('admin.semester-results.show', $existingDraftResult)
                ->with('info', 'A draft result already exists for this semester. You can review and publish it, or delete it to create a new one.');
        }

        // Get subjects for the next semester
        $subjects = Subject::where('course_id', $student->course_id)
            ->where('semester', $nextSemester)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        if ($subjects->isEmpty()) {
            return redirect()->route('admin.students.show', $student)
                ->with('error', "No subjects found for Semester {$nextSemester}. Please add subjects first.");
        }

        // Get academic year from student's session (format: YYYY-YY)
        // ALWAYS use student's session - it's required, so it should exist
        if (empty($student->session)) {
            return redirect()->route('admin.students.show', $student)
                ->with('error', 'Student session is not set. Please update the student profile with a valid session.');
        }
        
        $academicYear = $student->session;

        return view('admin.semester-results.create', compact('student', 'nextSemester', 'subjects', 'academicYear'));
    }

    /**
     * Store semester result
     */
    public function store(Request $request, Student $student)
    {
        $user = Auth::user();
        
        // Only Super Admin can generate results
        if (!$user->isSuperAdmin()) {
            abort(403, 'Only Super Admin can generate results.');
        }

        $validated = $request->validate([
            'semester' => ['required', 'integer', 'min:1'],
            'academic_year' => ['required', 'string', 'max:255'],
            'subjects' => ['required', 'array', 'min:1'],
            'subjects.*.subject_id' => ['required', 'exists:subjects,id'],
            'subjects.*.theory_marks_obtained' => ['required', 'numeric', 'min:0'],
            'subjects.*.practical_marks_obtained' => ['required', 'numeric', 'min:0'],
        ]);

        // Check if semester result already exists
        $existingResult = SemesterResult::where('student_id', $student->id)
            ->where('semester', $validated['semester'])
            ->first();

        if ($existingResult) {
            // If it's published and has actual marks, don't allow overwriting
            if ($existingResult->status === 'published' && $existingResult->total_marks_obtained > 0) {
                return redirect()->back()
                    ->withErrors(['semester' => 'Result for this semester is already published with marks.'])
                    ->withInput();
            }
            
            // If it's published with zero marks or draft, delete it first (we'll create a new one)
            // This allows regenerating results that were auto-published with zero marks
            if ($existingResult->status === 'published' && ($existingResult->total_marks_obtained == 0 || $existingResult->total_marks_obtained === null)) {
                // Delete associated results first
                $existingResult->results()->delete();
                // Delete PDF if exists
                if ($existingResult->pdf_path && \Storage::disk('public')->exists($existingResult->pdf_path)) {
                    \Storage::disk('public')->delete($existingResult->pdf_path);
                }
                // Delete the semester result
                $existingResult->delete();
            } elseif ($existingResult->status === 'draft') {
                // Delete draft result to create a fresh one
                $existingResult->results()->delete();
                $existingResult->delete();
            }
        }

        // Get subjects to validate marks
        $subjects = Subject::whereIn('id', collect($validated['subjects'])->pluck('subject_id'))
            ->get()
            ->keyBy('id');

        DB::beginTransaction();
        try {
            // Calculate totals
            $totalMarksObtained = 0;
            $totalMaxMarks = 0;
            $totalSubjects = count($validated['subjects']);

            // Validate marks and calculate totals
            foreach ($validated['subjects'] as $index => $subjectData) {
                $subject = $subjects[$subjectData['subject_id']];
                $theoryObtained = $subjectData['theory_marks_obtained'];
                $practicalObtained = $subjectData['practical_marks_obtained'];

                // Validate marks don't exceed maximum
                if ($theoryObtained > $subject->theory_marks) {
                    DB::rollBack();
                    return redirect()->back()
                        ->withErrors(["subjects.{$index}.theory_marks_obtained" => "Theory marks cannot exceed maximum of {$subject->theory_marks} for {$subject->name}."])
                        ->withInput();
                }

                if ($practicalObtained > $subject->practical_marks) {
                    DB::rollBack();
                    return redirect()->back()
                        ->withErrors(["subjects.{$index}.practical_marks_obtained" => "Practical marks cannot exceed maximum of {$subject->practical_marks} for {$subject->name}."])
                        ->withInput();
                }

                $marksObtained = $theoryObtained + $practicalObtained;
                $totalMarksObtained += $marksObtained;
                $totalMaxMarks += $subject->total_marks;
            }

            // Create semester result
            $semesterResult = SemesterResult::create([
                'student_id' => $student->id,
                'course_id' => $student->course_id,
                'semester' => $validated['semester'],
                'academic_year' => $validated['academic_year'],
                'total_subjects' => $totalSubjects,
                'total_marks_obtained' => $totalMarksObtained,
                'total_max_marks' => $totalMaxMarks,
                'status' => 'draft',
                'entered_by' => Auth::id(),
            ]);

            // Calculate overall percentage and grade
            $semesterResult->calculateOverall();
            $semesterResult->save();

            // Create individual result records
            foreach ($validated['subjects'] as $subjectData) {
                $subject = $subjects[$subjectData['subject_id']];
                $theoryObtained = $subjectData['theory_marks_obtained'];
                $practicalObtained = $subjectData['practical_marks_obtained'];
                $marksObtained = $theoryObtained + $practicalObtained;

                Result::create([
                    'student_id' => $student->id,
                    'subject_id' => $subject->id,
                    'semester_result_id' => $semesterResult->id,
                    'exam_type' => 'final',
                    'semester' => (string)$validated['semester'],
                    'academic_year' => $validated['academic_year'],
                    'theory_marks_obtained' => $theoryObtained,
                    'practical_marks_obtained' => $practicalObtained,
                    'marks_obtained' => $marksObtained,
                    'total_marks' => $subject->total_marks,
                    'status' => 'pending_verification',
                    'uploaded_by' => Auth::id(),
                ]);
            }

            DB::commit();

            return redirect()->route('admin.semester-results.show', $semesterResult)
                ->with('success', 'Semester result created successfully. Please review and publish.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while saving the result. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Show semester result details
     */
    public function show(SemesterResult $semesterResult)
    {
        $user = Auth::user();
        
        // Check permission - match the same logic as StudentController@show
        if (!$user->isSuperAdmin()) {
            $student = $semesterResult->student;
            $instituteId = session('current_institute_id');
            
            // Institute Admin can view if:
            // 1. They created the student, OR
            // 2. Student is from their institute (website registration - created_by is null)
            if ($student->created_by !== $user->id && ($student->created_by !== null || $student->institute_id != $instituteId)) {
                abort(403, 'You are not authorized to view this result.');
            }
        }

        $semesterResult->load(['student.course', 'student.institute', 'results.subject', 'enteredBy', 'verifiedBy']);

        return view('admin.semester-results.show', compact('semesterResult'));
    }

    /**
     * Publish semester result and generate PDF
     */
    public function publish(SemesterResult $semesterResult)
    {
        $user = Auth::user();
        
        // Check permission - match the same logic as StudentController@show
        if (!$user->isSuperAdmin()) {
            $student = $semesterResult->student;
            $instituteId = session('current_institute_id');
            
            // Institute Admin can publish if:
            // 1. They created the student, OR
            // 2. Student is from their institute (website registration - created_by is null)
            if ($student->created_by !== $user->id && ($student->created_by !== null || $student->institute_id != $instituteId)) {
                abort(403, 'You are not authorized to publish this result.');
            }
        }

        if ($semesterResult->status === 'published') {
            return redirect()->back()
                ->with('error', 'This result is already published.');
        }

        DB::beginTransaction();
        try {
            // Update status
            $semesterResult->update([
                'status' => 'published',
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'published_at' => now(),
            ]);

            // Update individual results status
            $semesterResult->results()->update([
                'status' => 'published',
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'published_at' => now(),
            ]);

            // Generate PDF
            $pdf = $this->generatePdf($semesterResult);
            
            // Save PDF path
            $pdfPath = 'results/' . $semesterResult->student_id . '/' . $semesterResult->id . '.pdf';
            \Storage::disk('public')->put($pdfPath, $pdf->output());
            
            $semesterResult->update(['pdf_path' => $pdfPath]);

            // Update student's current semester if this is the next semester
            $student = $semesterResult->student;
            if ($student->current_semester < $semesterResult->semester) {
                $student->update(['current_semester' => $semesterResult->semester]);
            }

            DB::commit();

            return redirect()->route('admin.semester-results.show', $semesterResult)
                ->with('success', 'Result published successfully and PDF generated.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'An error occurred while publishing the result: ' . $e->getMessage());
        }
    }

    /**
     * View PDF in browser (HTML preview)
     */
    public function viewPdf(SemesterResult $semesterResult)
    {
        $user = Auth::user();
        
        // Check permission - match the same logic as StudentController@show
        if (!$user->isSuperAdmin()) {
            $student = $semesterResult->student;
            $instituteId = session('current_institute_id');
            
            // Institute Admin can view if:
            // 1. They created the student, OR
            // 2. Student is from their institute (website registration - created_by is null)
            if ($student->created_by !== $user->id && ($student->created_by !== null || $student->institute_id != $instituteId)) {
                abort(403, 'You are not authorized to view this result.');
            }
        }

        $semesterResult->load(['student.institute', 'student.course', 'results.subject', 'enteredBy', 'verifiedBy']);

        return view('pdf.semester-result-preview', compact('semesterResult'));
    }

    /**
     * Download PDF (Admin)
     */
    public function downloadPdf(SemesterResult $semesterResult)
    {
        $user = Auth::user();
        
        // Check permission - match the same logic as StudentController@show
        if (!$user->isSuperAdmin()) {
            $student = $semesterResult->student;
            $instituteId = session('current_institute_id');
            
            // Institute Admin can download if:
            // 1. They created the student, OR
            // 2. Student is from their institute (website registration - created_by is null)
            if ($student->created_by !== $user->id && ($student->created_by !== null || $student->institute_id != $instituteId)) {
                abort(403, 'You are not authorized to download this result.');
            }
        }

        if (!$semesterResult->pdf_path || !\Storage::disk('public')->exists($semesterResult->pdf_path)) {
            // Generate PDF if it doesn't exist
            $pdf = $this->generatePdf($semesterResult);
            return $pdf->download('Semester-' . $semesterResult->semester . '-Result-' . $semesterResult->student->roll_number . '.pdf');
        }

        return \Storage::disk('public')->download($semesterResult->pdf_path);
    }

    /**
     * View PDF (Student - their own results only)
     */
    public function studentView(SemesterResult $semesterResult)
    {
        $student = Auth::guard('student')->user();
        
        // Check if this result belongs to the logged-in student
        if ($semesterResult->student_id !== $student->id) {
            abort(403, 'You are not authorized to view this result.');
        }

        // Check if result is published
        if ($semesterResult->status !== 'published') {
            abort(403, 'This result is not yet published.');
        }

        $semesterResult->load(['student.institute', 'student.course', 'results.subject', 'enteredBy', 'verifiedBy']);

        return view('pdf.semester-result-preview', compact('semesterResult'));
    }

    /**
     * Download PDF (Student - their own results only)
     */
    public function studentDownload(SemesterResult $semesterResult)
    {
        $student = Auth::guard('student')->user();
        
        // Check if this result belongs to the logged-in student
        if ($semesterResult->student_id !== $student->id) {
            abort(403, 'You are not authorized to download this result.');
        }

        // Check if result is published
        if ($semesterResult->status !== 'published') {
            abort(403, 'This result is not yet published.');
        }

        if (!$semesterResult->pdf_path || !\Storage::disk('public')->exists($semesterResult->pdf_path)) {
            // Generate PDF if it doesn't exist
            $pdf = $this->generatePdf($semesterResult);
            return $pdf->download('Semester-' . $semesterResult->semester . '-Result-' . $semesterResult->student->roll_number . '.pdf');
        }

        return \Storage::disk('public')->download($semesterResult->pdf_path);
    }

    /**
     * Generate PDF for semester result
     */
    private function generatePdf(SemesterResult $semesterResult)
    {
        $semesterResult->load(['student.course', 'student.institute', 'results.subject']);
        
        $pdf = Pdf::loadView('pdf.semester-result', compact('semesterResult'));
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('enable-local-file-access', true);
        
        return $pdf;
    }
}
