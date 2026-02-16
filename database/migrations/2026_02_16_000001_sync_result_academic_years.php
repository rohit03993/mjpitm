<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration syncs academic_year in Result records to match their parent SemesterResult records.
     * This fixes any data inconsistency where student session was changed but only SemesterResult was updated.
     */
    public function up(): void
    {
        // Update all Result records to match their parent SemesterResult academic_year
        // Only update records where academic_year differs (to avoid unnecessary updates)
        DB::statement("
            UPDATE results r
            INNER JOIN semester_results sr ON r.semester_result_id = sr.id
            SET r.academic_year = sr.academic_year
            WHERE r.academic_year != sr.academic_year
            AND r.semester_result_id IS NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     *
     * Cannot reverse: original results.academic_year values are not stored.
     * This is a one-way data fix migration.
     */
    public function down(): void
    {
        // No-op: one-way sync; original values unknown
    }
};
