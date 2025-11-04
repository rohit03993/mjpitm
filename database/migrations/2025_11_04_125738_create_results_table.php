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
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->string('exam_type'); // midterm, final, assignment, etc.
            $table->string('semester');
            $table->string('academic_year');
            $table->decimal('marks_obtained', 5, 2);
            $table->decimal('total_marks', 5, 2)->default(100);
            $table->decimal('percentage', 5, 2)->nullable();
            $table->string('grade')->nullable();
            $table->enum('status', ['pending_verification', 'published', 'rejected'])->default('pending_verification');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
