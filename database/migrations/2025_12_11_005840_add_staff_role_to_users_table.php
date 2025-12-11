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
        // Modify the enum to include 'staff' role
        // For MySQL/MariaDB, we need to use raw SQL to modify enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'institute_admin', 'staff', 'student') DEFAULT 'institute_admin'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum (without 'staff')
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'institute_admin', 'student') DEFAULT 'institute_admin'");
    }
};
