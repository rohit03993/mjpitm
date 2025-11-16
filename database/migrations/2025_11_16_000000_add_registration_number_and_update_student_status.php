<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // New registration number used at the time of initial admission
            $table->string('registration_number')->nullable()->unique()->after('id');
        });

        // Make roll_number nullable (it will be assigned later by Super Admin)
        DB::statement("ALTER TABLE students MODIFY roll_number VARCHAR(255) NULL");

        // Extend status enum to include 'pending' and 'rejected', default 'pending'
        DB::statement("ALTER TABLE students MODIFY status ENUM('pending','active','inactive','rejected') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert status enum to original definition
        DB::statement("ALTER TABLE students MODIFY status ENUM('active','inactive') DEFAULT 'active'");

        // Make roll_number required again
        DB::statement("ALTER TABLE students MODIFY roll_number VARCHAR(255) NOT NULL");

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('registration_number');
        });
    }
};


