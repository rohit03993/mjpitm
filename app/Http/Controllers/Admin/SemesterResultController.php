<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\SemesterResult;
use App\Models\Result;
use App\Models\Subject;
use App\Services\StudentAuditLogger;
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
        $maxSemesters = $student->course->max_semesters ?? 99;

        // Get all semesters that have subjects defined for this course, capped by course duration
        $availableSemesters = Subject::where('course_id', $student->course_id)
            ->where('status', 'active')
            ->where('semester', '<=', $maxSemesters)
            ->distinct()
            ->pluck('semester')
            ->sort()
            ->values();

        // If no subjects exist at all, check if there are published results
        if ($availableSemesters->isEmpty()) {
            $publishedResults = SemesterResult::where('student_id', $student->id)
                ->trulyPublished()
                ->count();
            
            if ($publishedResults > 0) {
                return redirect()->route('admin.students.show', $student)
                    ->with('error', 'No active subjects are currently configured for this course. However, published results exist. Please add subjects for the next semester before generating new results.');
            }
            
            return redirect()->route('admin.students.show', $student)
                ->with('error', 'No subjects have been added for this course. Please add subjects first before generating results.');
        }

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
            // Use trulyPublished scope to ensure consistency
            $publishedResult = SemesterResult::where('student_id', $student->id)
                ->where('semester', $sem)
                ->trulyPublished()
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
            // Fallback: next semester may have subjects even if not in distinct list (e.g. just added).
            // Use max of (published semesters, available semesters) + 1 and check for subjects.
            $publishedSemesters = SemesterResult::where('student_id', $student->id)
                ->where('status', 'published')
                ->whereNotNull('published_at')
                ->whereNotNull('verified_at')
                ->whereNotNull('verified_by')
                ->whereHas('results', function ($query) {
                    $query->where('status', 'published');
                })
                ->pluck('semester')
                ->map(fn ($s) => (int) $s)
                ->toArray();
            $maxPublished = !empty($publishedSemesters) ? (int) max($publishedSemesters) : 0;
            $maxAvailable = (int) ($availableSemesters->max() ?? 0);
            $nextSemesterNumber = max($maxPublished, $maxAvailable) + 1;

            if ($nextSemesterNumber <= $maxSemesters) {
                $subjectsForNext = Subject::where('course_id', $student->course_id)
                    ->where('status', 'active')
                    ->where(function ($q) use ($nextSemesterNumber) {
                        $q->where('semester', $nextSemesterNumber)
                            ->orWhere('semester', (string) $nextSemesterNumber);
                    })
                    ->orderBy('name')
                    ->get();
                if ($subjectsForNext->isNotEmpty()) {
                    $nextSemester = $nextSemesterNumber;
                    $subjects = $subjectsForNext;
                }
            }

            if (!$nextSemester) {
                // Course has limited semesters: next would exceed course duration — show clear completion message
                if ($nextSemesterNumber > $maxSemesters) {
                    $yearLabel = $maxSemesters === 1 ? '1 semester' : ($maxSemesters === 2 ? '1 year' : (($maxSemesters / 2) . ' years'));
                    $message = "This course is for {$yearLabel} only (max {$maxSemesters} semester(s)). All results for this course have been published.";
                    return redirect()->route('admin.students.show', $student)
                        ->with('error', $message);
                }
                $message = 'All semesters with subjects have been published for this student. ';
                if (!empty($publishedSemesters)) {
                    $message .= 'Published semesters: ' . implode(', ', $publishedSemesters) . '. ';
                }
                $message .= "Please add subjects for Semester {$nextSemesterNumber} (or a higher semester) to generate more results.";
                return redirect()->route('admin.students.show', $student)
                    ->with('error', $message);
            }
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
            // Check if there are published results for context
            $publishedCount = SemesterResult::where('student_id', $student->id)
                ->where('status', 'published')
                ->count();
            
            $message = "No subjects found for Semester {$nextSemester}.";
            if ($publishedCount > 0) {
                $message .= " Published results exist, but subjects for the next semester need to be added.";
            } else {
                $message .= " Please add subjects first.";
            }
            
            return redirect()->route('admin.students.show', $student)
                ->with('error', $message);
        }

        // Get academic year from student's session and semester (multi-semester logic)
        // Sem 1–2 = first academic year (session as-is), Sem 3–4 = second year, Sem 5–6 = third year, etc.
        if (empty($student->session)) {
            return redirect()->route('admin.students.show', $student)
                ->with('error', 'Student session is not set. Please update the student profile with a valid session.');
        }
        
        $academicYear = $this->getAcademicYearForSemester($student->session, $nextSemester);

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

        $maxSemesters = $student->course->max_semesters ?? 99;
        $validated = $request->validate([
            'semester' => ['required', 'integer', 'min:1', 'max:' . $maxSemesters],
            'academic_year' => ['nullable', 'string', 'max:255'],
            'subjects' => ['required', 'array', 'min:1'],
            'subjects.*.subject_id' => ['required', 'exists:subjects,id'],
            'subjects.*.theory_marks_obtained' => ['required', 'numeric', 'min:0'],
            'subjects.*.practical_marks_obtained' => ['required', 'numeric', 'min:0'],
        ]);

        // Override academic_year with computed value from session + semester (multi-semester logic)
        if (empty($student->session)) {
            return redirect()->back()
                ->withErrors(['academic_year' => 'Student session is not set. Please update the student profile with a valid session.'])
                ->withInput();
        }
        $computedAcademicYear = $this->getAcademicYearForSemester($student->session, $validated['semester']);
        if ($request->filled('academic_year') && $request->input('academic_year') !== $computedAcademicYear) {
            return redirect()->back()
                ->withErrors(['academic_year' => 'Academic year must match the value derived from student session and semester.'])
                ->withInput();
        }
        $validated['academic_year'] = $computedAcademicYear;

        // Check if semester result already exists
        $existingResult = SemesterResult::where('student_id', $student->id)
            ->where('semester', $validated['semester'])
            ->first();

        if ($existingResult) {
            // If it's truly published and has actual marks, don't allow overwriting
            if ($existingResult->isTrulyPublished() && $existingResult->total_marks_obtained > 0) {
                return redirect()->back()
                    ->withErrors(['semester' => 'Result for this semester is already published with marks.'])
                    ->withInput();
            }
            
            // If it's published (but not truly published) with zero marks or draft, delete it first (we'll create a new one)
            // This allows regenerating results that were auto-published with zero marks
            if ($existingResult->status === 'published' && !$existingResult->isTrulyPublished() && ($existingResult->total_marks_obtained == 0 || $existingResult->total_marks_obtained === null)) {
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

            // Create semester result (marksheet_serial is global monotonic, not the DB row id)
            $semesterResult = SemesterResult::create([
                'marksheet_serial' => SemesterResult::nextMarksheetSerial(),
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
            StudentAuditLogger::logRelatedCreated($student, 'semester_result', $semesterResult->only([
                'id',
                'semester',
                'academic_year',
                'total_subjects',
                'total_marks_obtained',
                'total_max_marks',
                'overall_percentage',
                'status',
            ]), $semesterResult->id);

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
            StudentAuditLogger::logRelatedCreated($student, 'result', [
                'bulk_create_for_semester_result_id' => $semesterResult->id,
                'total_rows' => count($validated['subjects']),
            ]);

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
        $semesterResult->load(['student.course', 'student.institute', 'results.subject', 'enteredBy', 'verifiedBy']);

        $student = $semesterResult->student;
        if (!$student) {
            abort(404, 'Student record not found for this result.');
        }
        if (!$semesterResult->course) {
            abort(404, 'Course not found for this result.');
        }

        $user = Auth::user();
        // Check permission - match the same logic as StudentController@show
        if (!$user->isSuperAdmin()) {
            $instituteId = session('current_institute_id');
            if ($student->created_by !== $user->id && ($student->created_by !== null || $student->institute_id != $instituteId)) {
                abort(403, 'You are not authorized to view this result.');
            }
        }

        return view('admin.semester-results.show', compact('semesterResult'));
    }

    /**
     * Show form to set result declaration date. Super Admin only.
     */
    public function showPublishForm(SemesterResult $semesterResult)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Only Super Admin can publish results.');
        }

        if ($semesterResult->status === 'published') {
            return redirect()->route('admin.semester-results.show', $semesterResult)
                ->with('error', 'This result is already published.');
        }

        $sem = (int) $semesterResult->semester;
        $isOddSem = ($sem % 2) === 1;
        // Calendar year for result: academic year start + 1 (e.g. session 2026-27, sem 1 → result Feb 2027)
        $defaultYear = $this->getResultCalendarYear($semesterResult);
        $defaultResultMonth = $isOddSem ? 2 : 7;
        $defaultResultDate = sprintf('%04d-%02d-15', $defaultYear, $defaultResultMonth);

        return view('admin.semester-results.publish-form', compact(
            'semesterResult',
            'defaultResultDate',
            'isOddSem'
        ));
    }

    /**
     * Publish result only (with result declaration date). Super Admin only.
     */
    public function publish(Request $request, SemesterResult $semesterResult)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Only Super Admin can publish results.');
        }

        if ($semesterResult->status === 'published') {
            return redirect()->route('admin.semester-results.show', $semesterResult)
                ->with('error', 'This result is already published.');
        }

        $sem = (int) $semesterResult->semester;
        $isOddSem = ($sem % 2) === 1;
        $validResultMonths = $isOddSem ? [2] : [7];

        $validated = $request->validate([
            'result_declaration_date' => ['required', 'date'],
        ]);

        $resultDate = \Carbon\Carbon::parse($validated['result_declaration_date']);
        if (!in_array((int) $resultDate->month, $validResultMonths, true)) {
            return redirect()->back()
                ->withErrors(['result_declaration_date' => 'Result declaration date must be in ' . ($isOddSem ? 'February' : 'July') . ' for this semester.'])
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $before = $semesterResult->only([
                'result_declaration_date',
                'status',
                'verified_by',
                'verified_at',
                'published_at',
            ]);
            $semesterResult->update([
                'result_declaration_date' => $resultDate->format('Y-m-d'),
                'status' => 'published',
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'published_at' => now(),
            ]);
            if ($semesterResult->student) {
                StudentAuditLogger::logRelatedUpdated($semesterResult->student, 'semester_result', $before, $semesterResult->only([
                    'result_declaration_date',
                    'status',
                    'verified_by',
                    'verified_at',
                    'published_at',
                ]), $semesterResult->id);
            }

            $semesterResult->results()->update([
                'status' => 'published',
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'published_at' => now(),
            ]);
            if ($semesterResult->student) {
                StudentAuditLogger::logRelatedUpdated($semesterResult->student, 'result', [
                    'bulk_status' => 'pending_verification',
                ], [
                    'bulk_status' => 'published',
                    'semester_result_id' => $semesterResult->id,
                ]);
            }

            $student = $semesterResult->student;
            if ($student->current_semester < $semesterResult->semester) {
                $student->update(['current_semester' => $semesterResult->semester]);
            }

            DB::commit();

            return redirect()->route('admin.semester-results.show', $semesterResult)
                ->with('success', 'Result published. You can issue the marksheet later from this page.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'An error occurred while publishing the result: ' . $e->getMessage());
        }
    }

    /**
     * Show form to set marksheet issue date. Super Admin only.
     */
    public function showIssueMarksheetForm(SemesterResult $semesterResult)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Only Super Admin can issue and print marksheet.');
        }

        if ($semesterResult->status !== 'published') {
            return redirect()->route('admin.semester-results.show', $semesterResult)
                ->with('error', 'Publish the result first, then issue the marksheet.');
        }

        $sem = (int) $semesterResult->semester;
        $isOddSem = ($sem % 2) === 1;
        // Calendar year for marksheet: same as result (e.g. sem 1 → marksheet Mar 2027)
        $defaultYear = $this->getResultCalendarYear($semesterResult);
        $defaultIssueMonth = $isOddSem ? 3 : 8;
        $defaultIssueDate = sprintf('%04d-%02d-15', $defaultYear, $defaultIssueMonth);

        return view('admin.semester-results.issue-marksheet-form', compact(
            'semesterResult',
            'defaultIssueDate',
            'isOddSem'
        ));
    }

    /**
     * Issue marksheet: set date of issue and generate PDF. Super Admin only.
     */
    public function issueMarksheet(Request $request, SemesterResult $semesterResult)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Only Super Admin can issue and print marksheet.');
        }

        if ($semesterResult->status !== 'published') {
            return redirect()->route('admin.semester-results.show', $semesterResult)
                ->with('error', 'Publish the result first, then issue the marksheet.');
        }

        $sem = (int) $semesterResult->semester;
        $isOddSem = ($sem % 2) === 1;
        $validIssueMonths = $isOddSem ? [3] : [8];

        $validated = $request->validate([
            'date_of_issue' => ['required', 'date'],
        ]);

        $issueDate = \Carbon\Carbon::parse($validated['date_of_issue']);
        if (!in_array((int) $issueDate->month, $validIssueMonths, true)) {
            return redirect()->back()
                ->withErrors(['date_of_issue' => 'Marksheet issue date must be in ' . ($isOddSem ? 'March' : 'August') . ' for this semester.'])
                ->withInput();
        }

        try {
            $before = $semesterResult->only(['date_of_issue', 'pdf_path']);
            $semesterResult->update(['date_of_issue' => $issueDate->format('Y-m-d')]);

            $pdf = $this->generatePdf($semesterResult);
            $pdfPath = 'results/' . $semesterResult->student_id . '/' . $semesterResult->id . '.pdf';
            \Storage::disk('public')->put($pdfPath, $pdf->output());
            $semesterResult->update(['pdf_path' => $pdfPath]);
            if ($semesterResult->student) {
                StudentAuditLogger::logRelatedUpdated($semesterResult->student, 'semester_result', $before, $semesterResult->only([
                    'date_of_issue',
                    'pdf_path',
                ]), $semesterResult->id);
            }

            return redirect()->route('admin.semester-results.show', $semesterResult)
                ->with('success', 'Marksheet issued. You can view and download the PDF.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while generating the marksheet: ' . $e->getMessage());
        }
    }

    /**
     * View marksheet PDF (printed format). Super Admin only; not visible to Institute Admin or Student.
     */
    public function viewPdf(SemesterResult $semesterResult)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Only Super Admin can view and print the marksheet.');
        }

        if (!$semesterResult->date_of_issue) {
            return redirect()->route('admin.semester-results.issue-marksheet-form', $semesterResult->id)
                ->with('error', 'Issue the marksheet first (set issue date) to view the PDF.');
        }

        $semesterResult->load(['student.institute', 'student.course', 'results.subject', 'enteredBy', 'verifiedBy']);
        return view('pdf.semester-result-preview', compact('semesterResult'));
    }

    /**
     * Download marksheet PDF. Super Admin only; not visible to Institute Admin or Student.
     */
    public function downloadPdf(SemesterResult $semesterResult)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Only Super Admin can view and print the marksheet.');
        }

        if (!$semesterResult->date_of_issue) {
            return redirect()->route('admin.semester-results.issue-marksheet-form', $semesterResult->id)
                ->with('error', 'Issue the marksheet first (set issue date) to download the PDF.');
        }

        // Always regenerate so layout/template changes apply (previously we served a stale file from last issue).
        $pdf = $this->generatePdf($semesterResult);
        $pdfPath = 'results/' . $semesterResult->student_id . '/' . $semesterResult->id . '.pdf';
        \Storage::disk('public')->put($pdfPath, $pdf->output());
        $semesterResult->update(['pdf_path' => $pdfPath]);

        $downloadName = 'Semester-' . $semesterResult->semester . '-Result-' . $semesterResult->student->roll_number . '.pdf';
        return response()->download(storage_path('app/public/' . $pdfPath), $downloadName);
    }

    /**
     * Marksheet PDF is for Super Admin only. Students see the online result; printed marksheet is collected from the office.
     */
    public function studentView(SemesterResult $semesterResult)
    {
        abort(403, 'The marksheet is issued by the institute only. Your result is published and visible above; the printed marksheet can be collected from the office.');
    }

    /**
     * Marksheet PDF is for Super Admin only. Students see the online result; printed marksheet is collected from the office.
     */
    public function studentDownload(SemesterResult $semesterResult)
    {
        abort(403, 'The marksheet is issued by the institute only. Your result is published and visible above; the printed marksheet can be collected from the office.');
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

    /**
     * Compute academic year for a given semester based on student session.
     * Assumes 2 semesters per academic year:
     *   Sem 1–2 → first year (session as-is, e.g. 2025-26)
     *   Sem 3–4 → second year (e.g. 2026-27)
     *   Sem 5–6 → third year (e.g. 2027-28)
     *
     * @param string $session Student session (e.g. "2025-26")
     * @param int $semester Semester number (1, 2, 3, ...)
     * @return string Academic year (e.g. "2025-26", "2026-27")
     */
    /**
     * Calendar year when result/marksheet falls (e.g. session 2026-27, sem 1 → 2027; sem 3 → 2028).
     */
    private function getResultCalendarYear(SemesterResult $semesterResult): int
    {
        if (preg_match('/^(\d{4})/', (string) $semesterResult->academic_year, $m)) {
            return (int) $m[1] + 1;
        }
        $student = $semesterResult->student;
        if ($student && $student->session) {
            $academicYear = $this->getAcademicYearForSemester($student->session, (int) $semesterResult->semester);
            if (preg_match('/^(\d{4})/', $academicYear, $m)) {
                return (int) $m[1] + 1;
            }
        }
        return (int) date('Y');
    }

    private function getAcademicYearForSemester(string $session, int $semester): string
    {
        $semester = max(1, (int) $semester);
        $yearIndex = (int) ceil($semester / 2) - 1; // 0 for 1,2; 1 for 3,4; 2 for 5,6

        if ($yearIndex === 0) {
            return $session;
        }

        $parts = explode('-', trim($session));
        $startYear = isset($parts[0]) && is_numeric($parts[0]) ? (int) $parts[0] : (int) date('Y');
        $endShort = isset($parts[1]) && is_numeric($parts[1]) ? (int) $parts[1] : ($startYear % 100) + 1;

        $newStart = $startYear + $yearIndex;
        $newEnd = $endShort + $yearIndex;

        return $newStart . '-' . str_pad((string) $newEnd, 2, '0', STR_PAD_LEFT);
    }
}
