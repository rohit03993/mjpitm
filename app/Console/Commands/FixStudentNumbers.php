<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Services\RollNumberGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FixStudentNumbers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:fix-numbers {--dry-run : Show what would be changed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix registration and roll numbers for existing students to match their session year';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        $this->info('Starting to fix student numbers...');
        $this->newLine();

        // Get all students
        $students = Student::with(['institute', 'course.category'])->get();
        $totalStudents = $students->count();

        $this->info("Found {$totalStudents} students to process");
        $this->newLine();

        $stats = [
            'processed' => 0,
            'skipped_no_session' => 0,
            'registration_fixed' => 0,
            'roll_number_fixed' => 0,
            'errors' => 0,
        ];

        $bar = $this->output->createProgressBar($totalStudents);
        $bar->start();

        foreach ($students as $student) {
            try {
                // Skip if student has no session
                if (empty($student->session)) {
                    $stats['skipped_no_session']++;
                    $bar->advance();
                    continue;
                }

                // Extract year from session (e.g., "2022-23" -> "2022")
                $sessionParts = explode('-', $student->session);
                $sessionYear = $sessionParts[0] ?? null;

                if (!$sessionYear || !is_numeric($sessionYear) || strlen($sessionYear) !== 4) {
                    $stats['skipped_no_session']++;
                    $bar->advance();
                    continue;
                }

                $changed = false;
                $changes = [];

                // Check and fix registration number
                if ($student->registration_number) {
                    $regParts = explode('-', $student->registration_number);
                    $regYear = $regParts[1] ?? null;

                    // If registration number year doesn't match session year, regenerate it
                    if ($regYear !== $sessionYear) {
                        $newRegNumber = $this->generateRegistrationNumberForYear(
                            $student->institute_id,
                            $sessionYear,
                            $student->id
                        );

                        if (!$dryRun) {
                            $student->registration_number = $newRegNumber;
                            $student->save();
                        }

                        $changes[] = "Registration: {$student->registration_number} → {$newRegNumber}";
                        $stats['registration_fixed']++;
                        $changed = true;
                    }
                }

                // Check and fix roll number (only for active students with roll numbers)
                if ($student->status === 'active' && $student->roll_number) {
                    // Extract year from roll number (format: MJPITM202610253157 -> 2026)
                    $rollYear = $this->extractYearFromRollNumber($student->roll_number);

                    // If roll number year doesn't match session year, regenerate it
                    if ($rollYear && $rollYear !== $sessionYear) {
                        try {
                            // Reload student with relationships
                            $student->load(['institute', 'course.category']);

                            // Check prerequisites
                            if (empty($student->institute->institute_code)) {
                                throw new \Exception('Institute code not set');
                            }

                            if (!$student->course || !$student->course->category || empty($student->course->category->roll_number_code)) {
                                throw new \Exception('Category roll number code not set');
                            }

                            $newRollNumber = RollNumberGenerator::generateForYear($student, $sessionYear);

                            if (!$dryRun) {
                                $student->roll_number = $newRollNumber;
                                $student->save();
                            }

                            $changes[] = "Roll Number: {$student->roll_number} → {$newRollNumber}";
                            $stats['roll_number_fixed']++;
                            $changed = true;
                        } catch (\Exception $e) {
                            $this->newLine();
                            $this->error("Error fixing roll number for student ID {$student->id}: {$e->getMessage()}");
                            $stats['errors']++;
                        }
                    }
                }

                if ($changed) {
                    $stats['processed']++;
                    if ($this->getOutput()->isVerbose()) {
                        $this->newLine();
                        $this->info("Student ID {$student->id} ({$student->name}):");
                        foreach ($changes as $change) {
                            $this->line("  - {$change}");
                        }
                    }
                }

            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Error processing student ID {$student->id}: {$e->getMessage()}");
                $stats['errors']++;
                Log::error('FixStudentNumbers error', [
                    'student_id' => $student->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Display summary
        $this->info('📊 Summary:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Students', (string)$totalStudents],
                ['Processed (with changes)', (string)$stats['processed']],
                ['Registration Numbers Fixed', (string)$stats['registration_fixed']],
                ['Roll Numbers Fixed', (string)$stats['roll_number_fixed']],
                ['Skipped (No Session)', (string)$stats['skipped_no_session']],
                ['Errors', (string)$stats['errors']],
            ]
        );

        if ($dryRun) {
            $this->newLine();
            $this->warn('⚠️  This was a DRY RUN. No changes were made.');
            $this->info('Run without --dry-run to apply changes.');
        } else {
            $this->newLine();
            $this->info('✅ All changes have been applied successfully!');
        }

        return Command::SUCCESS;
    }

    /**
     * Generate registration number for a specific year
     */
    protected function generateRegistrationNumberForYear(int $instituteId, string $year, int $excludeStudentId = null): string
    {
        $prefix = 'REG';

        // Get the last registration number for this institute and year
        $query = Student::where('institute_id', $instituteId)
            ->where('registration_number', 'like', $prefix . '-' . $year . '%');

        // Exclude current student from check
        if ($excludeStudentId) {
            $query->where('id', '!=', $excludeStudentId);
        }

        $lastStudent = $query->orderBy('registration_number', 'desc')->first();

        if ($lastStudent && $lastStudent->registration_number) {
            // Extract the sequence number from the last registration number
            // Format: REG-2022-00001 -> extract 00001
            $parts = explode('-', $lastStudent->registration_number);
            if (count($parts) >= 3) {
                $lastNumber = (int) $parts[2]; // Get the sequence part
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
        } else {
            $newNumber = 1;
        }

        // Ensure uniqueness by checking if the number already exists
        $maxAttempts = 100; // Safety limit
        $attempts = 0;

        do {
            $sequencePadded = str_pad($newNumber, 5, '0', STR_PAD_LEFT);
            $registrationNumber = "{$prefix}-{$year}-{$sequencePadded}";

            // Check if this registration number already exists (excluding current student)
            $existsQuery = Student::where('registration_number', $registrationNumber);
            if ($excludeStudentId) {
                $existsQuery->where('id', '!=', $excludeStudentId);
            }
            $exists = $existsQuery->exists();

            if (!$exists) {
                return $registrationNumber;
            }

            $newNumber++;
            $attempts++;
        } while ($attempts < $maxAttempts);

        // Fallback: use timestamp-based number if we can't find a unique sequence
        $timestamp = time();
        return "{$prefix}-{$year}-" . substr($timestamp, -5);
    }

    /**
     * Extract year from roll number
     * Format: MJPITM202610253157 -> 2026
     */
    protected function extractYearFromRollNumber(string $rollNumber): ?string
    {
        // Try to extract 4-digit year from roll number
        // Pattern: Any text followed by 4 digits (year)
        if (preg_match('/(\d{4})/', $rollNumber, $matches)) {
            $year = $matches[1];
            // Validate it's a reasonable year (2000-2099)
            if ($year >= 2000 && $year <= 2099) {
                return $year;
            }
        }
        return null;
    }
}

