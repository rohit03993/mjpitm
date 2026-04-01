<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Services\StudentPermanentDeletion;
use Illuminate\Console\Command;

class PurgeTrashedStudents extends Command
{
    protected $signature = 'students:purge-trashed
                            {--dry-run : List soft-deleted students without removing anything}';

    protected $description = 'Permanently delete all soft-deleted students and related fees, results, files, etc.';

    public function handle(): int
    {
        $count = Student::onlyTrashed()->count();

        if ($count === 0) {
            $this->info('No soft-deleted students in the database.');

            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $this->warn("Would permanently purge {$count} soft-deleted student(s) and all related data.");
            Student::onlyTrashed()->orderBy('id')->each(function (Student $student): void {
                $reg = $student->registration_number ?? '—';
                $this->line("  ID {$student->id} — {$student->name} — {$reg} (deleted_at {$student->deleted_at})");
            });

            return self::SUCCESS;
        }

        if (! $this->confirm("Permanently delete {$count} soft-deleted student(s) and ALL related records and files? This cannot be undone.", false)) {
            $this->warn('Aborted.');

            return self::FAILURE;
        }

        $purged = 0;
        Student::onlyTrashed()->orderBy('id')->chunkById(50, function ($students) use (&$purged): void {
            foreach ($students as $student) {
                StudentPermanentDeletion::purge($student);
                $purged++;
                $this->info("Purged student ID {$student->id} ({$purged} done).");
            }
        });

        $this->info("Finished. Purged {$purged} student(s).");

        return self::SUCCESS;
    }
}
