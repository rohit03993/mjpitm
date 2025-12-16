<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CourseCategory;

class GenerateCategoryRollNumberCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'categories:generate-roll-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate roll_number_code for categories that don\'t have one';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating roll number codes for categories...');

        // Get all categories grouped by institute
        $institutes = \App\Models\Institute::all();

        $totalUpdated = 0;
        $totalSkipped = 0;

        foreach ($institutes as $institute) {
            $this->line("\nProcessing Institute: {$institute->name}");
            
            // Get all categories for this institute
            $categories = CourseCategory::where('institute_id', $institute->id)
                ->orderBy('id')
                ->get();

            $codeCounter = 1;

            foreach ($categories as $category) {
                if (empty($category->roll_number_code)) {
                    // Generate code: 01, 02, 03, etc.
                    $newCode = str_pad($codeCounter, 2, '0', STR_PAD_LEFT);
                    
                    // Make sure this code doesn't already exist for this institute
                    while (CourseCategory::where('institute_id', $institute->id)
                        ->where('roll_number_code', $newCode)
                        ->where('id', '!=', $category->id)
                        ->exists()) {
                        $codeCounter++;
                        $newCode = str_pad($codeCounter, 2, '0', STR_PAD_LEFT);
                    }

                    $category->roll_number_code = $newCode;
                    $category->save();
                    
                    $this->info("  ✓ Set code '{$newCode}' for: {$category->name}");
                    $totalUpdated++;
                    $codeCounter++;
                } else {
                    $this->line("  ⊘ {$category->name} already has code '{$category->roll_number_code}' - skipped");
                    $totalSkipped++;
                    // Update counter to be higher than existing code
                    $existingCode = (int)$category->roll_number_code;
                    if ($existingCode >= $codeCounter) {
                        $codeCounter = $existingCode + 1;
                    }
                }
            }
        }

        $this->newLine();
        $this->info("Completed! Updated: {$totalUpdated}, Skipped: {$totalSkipped}");

        return Command::SUCCESS;
    }
}
