<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Result;
use App\Models\SemesterResult;

class SyncResultAcademicYears extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'results:sync-academic-years 
                            {--dry-run : Show what would be updated without making changes}
                            {--student-id= : Sync only for a specific student ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync academic_year in Result records to match their parent SemesterResult records. This fixes any data inconsistency where session was changed but only SemesterResult was updated.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $studentId = $this->option('student-id');

        $this->info('Starting academic_year synchronization...');
        $this->newLine();

        // Build query for results that have semester_result_id
        $query = Result::whereNotNull('semester_result_id')
            ->with('semesterResult');

        if ($studentId) {
            $query->where('student_id', $studentId);
            $this->info("Filtering for student ID: {$studentId}");
        }

        $results = $query->get();
        $totalResults = $results->count();

        if ($totalResults === 0) {
            $this->warn('No results found to sync.');
            return Command::SUCCESS;
        }

        $this->info("Found {$totalResults} result records to check.");
        $this->newLine();

        $updated = 0;
        $skipped = 0;
        $errors = 0;

        $bar = $this->output->createProgressBar($totalResults);
        $bar->start();

        foreach ($results as $result) {
            try {
                // Get the parent SemesterResult
                $semesterResult = $result->semesterResult;

                if (!$semesterResult) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                // Check if academic_year differs
                if ($result->academic_year !== $semesterResult->academic_year) {
                    if ($dryRun) {
                        $this->newLine();
                        $this->line("Would update Result ID {$result->id}:");
                        $this->line("  Current: {$result->academic_year}");
                        $this->line("  New: {$semesterResult->academic_year}");
                    } else {
                        // Update the Result record to match SemesterResult
                        $result->update(['academic_year' => $semesterResult->academic_year]);
                    }
                    $updated++;
                } else {
                    $skipped++;
                }
            } catch (\Exception $e) {
                $errors++;
                $this->newLine();
                $this->error("Error processing Result ID {$result->id}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        if ($dryRun) {
            $this->info("DRY RUN COMPLETE");
            $this->info("Would update: {$updated} records");
            $this->info("Would skip: {$skipped} records (already in sync)");
        } else {
            $this->info("SYNC COMPLETE");
            $this->info("Updated: {$updated} records");
            $this->info("Skipped: {$skipped} records (already in sync)");
        }

        if ($errors > 0) {
            $this->warn("Errors encountered: {$errors}");
        }

        $this->newLine();
        $this->info('Academic year synchronization finished.');

        return Command::SUCCESS;
    }
}
