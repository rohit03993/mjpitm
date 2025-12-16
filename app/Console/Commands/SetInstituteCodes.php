<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Institute;

class SetInstituteCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'institutes:set-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set institute codes for existing institutes based on their domain';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting institute codes...');

        // Map domains to institute codes
        $domainToCode = [
            'mjpitm.in' => 'MJPITM',
            'mjpips.in' => 'MJPIPS',
        ];

        $updated = 0;
        $skipped = 0;

        foreach ($domainToCode as $domain => $code) {
            $institute = Institute::where('domain', $domain)->first();
            
            if ($institute) {
                if (empty($institute->institute_code)) {
                    $institute->institute_code = $code;
                    $institute->save();
                    $this->info("✓ Set code '{$code}' for {$institute->name}");
                    $updated++;
                } else {
                    $this->line("⊘ {$institute->name} already has code '{$institute->institute_code}' - skipped");
                    $skipped++;
                }
            } else {
                $this->warn("⚠ Institute with domain '{$domain}' not found");
            }
        }

        // Check for any other institutes without codes
        $institutesWithoutCodes = Institute::whereNull('institute_code')
            ->orWhere('institute_code', '')
            ->get();

        if ($institutesWithoutCodes->count() > 0) {
            $this->newLine();
            $this->warn('The following institutes still need codes:');
            foreach ($institutesWithoutCodes as $institute) {
                $this->line("  - {$institute->name} (domain: {$institute->domain})");
            }
            $this->info('Please set these manually in the Super Admin panel.');
        }

        $this->newLine();
        $this->info("Completed! Updated: {$updated}, Skipped: {$skipped}");

        return Command::SUCCESS;
    }
}
