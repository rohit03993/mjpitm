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
        Schema::table('fees', function (Blueprint $table) {
            // Add payment_mode column
            $table->enum('payment_mode', ['online', 'offline'])->default('offline')->after('payment_type');
            // Remove transaction_id column
            $table->dropColumn('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fees', function (Blueprint $table) {
            // Restore transaction_id
            $table->string('transaction_id')->nullable()->after('payment_date');
            // Remove payment_mode
            $table->dropColumn('payment_mode');
        });
    }
};
