<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Models\SemesterResult;

class CheckStudentResults extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:check-results {student_id : The ID of the student to check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check what semester results exist for a student and their status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $studentId = $this->argument('student_id');
        
        $student = Student::find($studentId);
        
        if (!$student) {
            $this->error("Student with ID {$studentId} not found.");
            return Command::FAILURE;
        }

        $this->info("=== Student Information ===");
        $this->info("ID: {$student->id}");
        $this->info("Name: {$student->name}");
        $this->info("Status: {$student->status}");
        $session = $student->session ? $student->session : 'NOT SET';
        $this->info("Session: {$session}");
        $this->newLine();

        // Get ALL semester results (no filtering)
        $allResults = SemesterResult::where('student_id', $studentId)->get();
        
        $this->info("=== ALL Semester Results in Database ===");
        $this->info("Total Results Found: " . $allResults->count());
        $this->newLine();

        if ($allResults->count() === 0) {
            $this->warn("No semester results found in database for this student.");
            return Command::SUCCESS;
        }

        $tableData = [];
        foreach ($allResults as $result) {
            $tableData[] = [
                'ID' => $result->id,
                'Semester' => $result->semester,
                'Status' => $result->status,
                'Published At' => $result->published_at ? $result->published_at->format('Y-m-d H:i:s') : 'NULL',
                'Verified At' => $result->verified_at ? $result->verified_at->format('Y-m-d H:i:s') : 'NULL',
                'Created At' => $result->created_at->format('Y-m-d H:i:s'),
            ];
        }

        $this->table(
            ['ID', 'Semester', 'Status', 'Published At', 'Verified At', 'Created At'],
            $tableData
        );

        $this->newLine();
        $this->info("=== Query Check (What Should Be Shown) ===");
        
        // Run the same query as in StudentController
        $publishedResults = SemesterResult::where('student_id', $studentId)
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->whereNotNull('verified_at')
            ->where('published_at', '<=', now())
            ->where('verified_at', '<=', now())
            ->get();

        $this->info("Results that SHOULD be displayed: " . $publishedResults->count());
        
        if ($publishedResults->count() > 0) {
            $this->warn("⚠️  These results are being shown because they have:");
            foreach ($publishedResults as $result) {
                $this->line("  - Semester {$result->semester}: status='{$result->status}', published_at='{$result->published_at}', verified_at='{$result->verified_at}'");
            }
        } else {
            $this->info("✅ No results should be displayed (query returns empty).");
        }

        $this->newLine();
        $this->info("=== Analysis ===");
        
        $draftResults = $allResults->where('status', 'draft')->count();
        $publishedResultsCount = $allResults->where('status', 'published')->count();
        $pendingResults = $allResults->where('status', 'pending_verification')->count();
        
        $this->info("Draft Results: {$draftResults}");
        $this->info("Published Results: {$publishedResultsCount}");
        $this->info("Pending Verification: {$pendingResults}");
        
        if ($publishedResultsCount > 0 && $publishedResults->count() > 0) {
            $this->newLine();
            $this->warn("⚠️  ISSUE FOUND:");
            $this->warn("   There are {$publishedResultsCount} results with status='published'.");
            $this->warn("   These results have published_at and verified_at timestamps set.");
            $this->warn("   This is why they are showing on the page.");
            $this->newLine();
            $this->info("To fix this, you can:");
            $this->info("1. Change their status back to 'draft' in the database");
            $this->info("2. Or delete them if they were created by mistake");
        }

        return Command::SUCCESS;
    }
}

