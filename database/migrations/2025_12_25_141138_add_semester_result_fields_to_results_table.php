<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->foreignId('semester_result_id')->nullable()->after('subject_id')->constrained('semester_results')->onDelete('cascade');
            $table->decimal('theory_marks_obtained', 5, 2)->nullable()->after('marks_obtained');
            $table->decimal('practical_marks_obtained', 5, 2)->nullable()->after('theory_marks_obtained');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropForeign(['semester_result_id']);
            $table->dropColumn(['semester_result_id', 'theory_marks_obtained', 'practical_marks_obtained']);
        });
    }
};
