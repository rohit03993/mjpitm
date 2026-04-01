<?php

namespace App\Services;

use App\Models\RegistrationNotification;
use App\Models\SemesterResult;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StudentPermanentDeletion
{
    /**
     * Hard-delete one student and all related DB records, then remove public storage files.
     * $student may be soft-deleted; load with Student::withTrashed() if needed.
     */
    public static function purge(Student $student): void
    {
        $id = $student->id;

        $documentPaths = array_values(array_filter([
            $student->photo,
            $student->signature,
            $student->aadhar_front,
            $student->aadhar_back,
            $student->certificate_class_10th,
            $student->certificate_class_12th,
            $student->certificate_graduation,
            $student->certificate_others,
        ]));

        $resultPdfPaths = SemesterResult::where('student_id', $id)
            ->whereNotNull('pdf_path')
            ->pluck('pdf_path')
            ->all();

        $resultDirectory = 'results/' . $id;

        DB::transaction(function () use ($student): void {
            $student->results()->delete();
            $student->semesterResults()->delete();
            $student->fees()->delete();
            $student->qualifications()->delete();
            $student->audits()->delete();
            RegistrationNotification::where('student_id', $student->id)->delete();

            $student->forceDelete();
        });

        foreach (array_merge($documentPaths, $resultPdfPaths) as $path) {
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
        if (Storage::disk('public')->exists($resultDirectory)) {
            Storage::disk('public')->deleteDirectory($resultDirectory);
        }
    }
}
