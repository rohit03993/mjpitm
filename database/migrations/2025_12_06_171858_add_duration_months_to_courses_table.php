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
        Schema::table('courses', function (Blueprint $table) {
            // Add duration_months column
            $table->integer('duration_months')->nullable()->after('duration_years');
        });
        
        // Convert existing duration_years to duration_months
        DB::table('courses')->whereNotNull('duration_years')->update([
            'duration_months' => DB::raw('duration_years * 12')
        ]);
        
        Schema::table('courses', function (Blueprint $table) {
            // Drop duration_years column
            $table->dropColumn('duration_years');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Add duration_years column back
            $table->integer('duration_years')->nullable()->after('duration_months');
        });
        
        // Convert duration_months back to duration_years (round down)
        DB::table('courses')->whereNotNull('duration_months')->update([
            'duration_years' => DB::raw('FLOOR(duration_months / 12)')
        ]);
        
        Schema::table('courses', function (Blueprint $table) {
            // Drop duration_months column
            $table->dropColumn('duration_months');
            // Make duration_years not nullable with default
            $table->integer('duration_years')->default(3)->nullable(false)->change();
        });
    }
};
