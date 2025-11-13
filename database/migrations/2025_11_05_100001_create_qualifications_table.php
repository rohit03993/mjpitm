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
        Schema::create('qualifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->enum('examination', ['secondary', 'sr_secondary', 'graduation', 'post_graduation', 'other'])->default('secondary');
            $table->string('year_of_passing')->nullable();
            $table->string('board_university')->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->string('cgpa')->nullable();
            $table->text('subjects')->nullable(); // Store subjects as JSON or text
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qualifications');
    }
};

