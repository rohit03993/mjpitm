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
        Schema::table('fees', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['student_id']);
            // Make student_id nullable
            $table->foreignId('student_id')->nullable()->change();
            // Re-add foreign key with nullable support
            $table->foreign('student_id')->references('id')->on('students')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fees', function (Blueprint $table) {
            // Drop the nullable foreign key
            $table->dropForeign(['student_id']);
            // Make student_id required again
            $table->foreignId('student_id')->nullable(false)->change();
            // Re-add required foreign key
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }
};
