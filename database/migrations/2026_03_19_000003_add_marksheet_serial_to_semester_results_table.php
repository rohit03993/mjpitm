<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Dedicated certificate serial for marksheet "Sr. No." (independent of primary key).
     * Existing rows keep the same visible number as before (serial = id).
     */
    public function up(): void
    {
        Schema::table('semester_results', function (Blueprint $table) {
            $table->unsignedInteger('marksheet_serial')->nullable()->after('id');
        });

        DB::table('semester_results')->update([
            'marksheet_serial' => DB::raw('id'),
        ]);

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE semester_results MODIFY marksheet_serial INT UNSIGNED NOT NULL');
        }

        Schema::table('semester_results', function (Blueprint $table) {
            $table->unique('marksheet_serial');
        });
    }

    public function down(): void
    {
        Schema::table('semester_results', function (Blueprint $table) {
            $table->dropUnique(['marksheet_serial']);
            $table->dropColumn('marksheet_serial');
        });
    }
};
