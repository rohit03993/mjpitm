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
        Schema::table('courses', function (Blueprint $table) {
            // Fee fields for courses
            $table->decimal('registration_fee', 10, 2)->nullable()->after('description');
            $table->decimal('entrance_fee', 10, 2)->nullable()->after('registration_fee');
            $table->decimal('enrollment_fee', 10, 2)->nullable()->after('entrance_fee');
            $table->decimal('tuition_fee', 10, 2)->nullable()->after('enrollment_fee');
            $table->decimal('caution_money', 10, 2)->nullable()->after('tuition_fee');
            $table->decimal('hostel_fee_amount', 10, 2)->nullable()->after('caution_money');
            $table->decimal('late_fee', 10, 2)->nullable()->after('hostel_fee_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'registration_fee',
                'entrance_fee',
                'enrollment_fee',
                'tuition_fee',
                'caution_money',
                'hostel_fee_amount',
                'late_fee',
            ]);
        });
    }
};
