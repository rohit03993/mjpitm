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
        Schema::table('students', function (Blueprint $table) {
            $table->string('certificate_class_10th')->nullable()->after('aadhar_back');
            $table->string('certificate_class_12th')->nullable()->after('certificate_class_10th');
            $table->string('certificate_graduation')->nullable()->after('certificate_class_12th');
            $table->string('certificate_others')->nullable()->after('certificate_graduation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['certificate_class_10th', 'certificate_class_12th', 'certificate_graduation', 'certificate_others']);
        });
    }
};
