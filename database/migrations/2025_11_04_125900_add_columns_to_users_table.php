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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['super_admin', 'institute_admin', 'student'])->default('institute_admin');
            $table->foreignId('institute_id')->nullable()->constrained()->onDelete('set null');
            $table->string('center')->nullable(); // Agra, Bhopal, Delhi, etc.
            $table->enum('status', ['active', 'inactive'])->default('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['institute_id']);
            $table->dropColumn(['role', 'institute_id', 'center', 'status']);
        });
    }
};
