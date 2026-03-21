<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Single-row counter for marksheet_serial allocation (lockForUpdate-safe, no race with cache+transaction).
     */
    public function up(): void
    {
        Schema::create('marksheet_serial_sequences', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->primary();
            $table->unsignedBigInteger('last_value')->default(0);
            $table->timestamps();
        });

        $max = (int) (DB::table('semester_results')->max('marksheet_serial') ?? 0);

        DB::table('marksheet_serial_sequences')->insert([
            'id' => 1,
            'last_value' => $max,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('marksheet_serial_sequences');
    }
};
