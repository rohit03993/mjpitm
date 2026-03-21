<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Renumber marksheet_serial to 1..N in id order so Sr. No. display (serial + offset)
     * starts at 00000151 for the earliest result and counts up for all existing rows.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('semester_results', 'marksheet_serial')) {
            return;
        }

        $ids = DB::table('semester_results')->orderBy('id')->pluck('id');
        if ($ids->isEmpty()) {
            return;
        }

        // Two-phase: avoid unique(marksheet_serial) clashes while reassigning
        DB::table('semester_results')->update([
            'marksheet_serial' => DB::raw('900000000 + id'),
        ]);

        $n = 1;
        foreach ($ids as $id) {
            DB::table('semester_results')->where('id', $id)->update(['marksheet_serial' => $n]);
            $n++;
        }
    }

    /**
     * Restore serial = id (same rule as original 000003 backfill).
     */
    public function down(): void
    {
        if (! Schema::hasColumn('semester_results', 'marksheet_serial')) {
            return;
        }

        DB::table('semester_results')->update([
            'marksheet_serial' => DB::raw('id'),
        ]);
    }
};
