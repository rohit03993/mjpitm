<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds Contact Us (footer) fields editable by Super Admin.
     */
    public function up(): void
    {
        Schema::table('institutes', function (Blueprint $table) {
            $table->string('contact_address', 500)->nullable()->after('description');
            $table->string('contact_email', 255)->nullable()->after('contact_address');
            $table->string('contact_phone', 50)->nullable()->after('contact_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('institutes', function (Blueprint $table) {
            $table->dropColumn(['contact_address', 'contact_email', 'contact_phone']);
        });
    }
};
