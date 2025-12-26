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
        Schema::create('semester_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->integer('semester');
            $table->string('academic_year');
            $table->integer('total_subjects')->default(0);
            $table->decimal('total_marks_obtained', 8, 2)->default(0);
            $table->decimal('total_max_marks', 8, 2)->default(0);
            $table->decimal('overall_percentage', 5, 2)->nullable();
            $table->string('overall_grade')->nullable();
            $table->enum('status', ['draft', 'pending_verification', 'published', 'rejected'])->default('draft');
            $table->foreignId('entered_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->text('remarks')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamps();
            
            // Index for faster queries
            $table->index(['student_id', 'semester']);
            $table->index(['course_id', 'semester']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semester_results');
    }
};
