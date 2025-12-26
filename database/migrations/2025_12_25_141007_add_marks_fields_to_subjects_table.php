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
        Schema::table('subjects', function (Blueprint $table) {
            $table->decimal('theory_marks', 5, 2)->nullable()->after('credits');
            $table->decimal('practical_marks', 5, 2)->nullable()->after('theory_marks');
            $table->decimal('total_marks', 5, 2)->nullable()->after('practical_marks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn(['theory_marks', 'practical_marks', 'total_marks']);
        });
    }
};
