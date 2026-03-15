<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add result_declaration_date and backfill existing semester_results with
     * sensible default dates (result: Feb/July, marksheet issue: March/August).
     */
    public function up(): void
    {
        Schema::table('semester_results', function (Blueprint $table) {
            $table->date('result_declaration_date')->nullable()->after('academic_year');
        });

        $this->backfillExistingRecords();
    }

    /**
     * Backfill result_declaration_date and date_of_issue for existing rows
     * so already-generated results are also updated.
     */
    private function backfillExistingRecords(): void
    {
        $rows = DB::table('semester_results')->get();

        foreach ($rows as $row) {
            $sem = (int) $row->semester;
            $academicYear = $row->academic_year;
            if (!$academicYear || !preg_match('/^(\d{4})-\d{2}$/', $academicYear, $m)) {
                continue;
            }
            $startYear = (int) $m[1];
            $isOddSem = ($sem % 2) === 1;

            $updates = [];

            if (empty($row->result_declaration_date)) {
                $month = $isOddSem ? 2 : 7;
                $lastDay = (int) date('t', strtotime("{$startYear}-{$month}-01"));
                $updates['result_declaration_date'] = sprintf('%04d-%02d-%02d', $startYear, $month, $lastDay);
            }

            if (empty($row->date_of_issue)) {
                $month = $isOddSem ? 3 : 8;
                $updates['date_of_issue'] = sprintf('%04d-%02d-01', $startYear, $month);
            }

            if (!empty($updates)) {
                DB::table('semester_results')->where('id', $row->id)->update($updates);
            }
        }
    }

    public function down(): void
    {
        Schema::table('semester_results', function (Blueprint $table) {
            $table->dropColumn('result_declaration_date');
        });
    }
};
