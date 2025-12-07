<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update all courses to set registration_fee to 1000 if it's null or 0
        DB::table('courses')
            ->whereNull('registration_fee')
            ->orWhere('registration_fee', 0)
            ->update(['registration_fee' => 1000.00]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert registration fees to null (we can't know original values)
        DB::table('courses')
            ->where('registration_fee', 1000.00)
            ->update(['registration_fee' => null]);
    }
};
