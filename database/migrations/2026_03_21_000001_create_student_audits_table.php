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
        Schema::create('student_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('event', 50); // created, updated, deleted
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->json('changes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'created_at']);
            $table->index('event');
            $table->index('actor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_audits');
    }
};
