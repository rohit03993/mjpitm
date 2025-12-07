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
        Schema::create('registration_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('institute_id')->constrained()->onDelete('cascade');
            $table->enum('registration_type', ['website', 'guest']); // website = self-registered, guest = admin-registered
            $table->foreignId('read_by')->nullable()->constrained('users')->onDelete('set null'); // Which admin viewed it
            $table->timestamp('read_at')->nullable(); // When it was viewed
            $table->timestamps();

            // Index for faster queries
            $table->index(['institute_id', 'read_at']);
            $table->index(['read_by', 'read_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registration_notifications');
    }
};

