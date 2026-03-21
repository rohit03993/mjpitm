<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('institutes', function (Blueprint $table) {
            $table->string('marksheet_header_logo')->nullable()->after('contact_phone');
            $table->string('marksheet_watermark_image')->nullable()->after('marksheet_header_logo');
            $table->string('marksheet_footer_logo_1')->nullable()->after('marksheet_watermark_image');
            $table->string('marksheet_footer_logo_2')->nullable()->after('marksheet_footer_logo_1');
            $table->string('marksheet_footer_logo_3')->nullable()->after('marksheet_footer_logo_2');
        });
    }

    public function down(): void
    {
        Schema::table('institutes', function (Blueprint $table) {
            $table->dropColumn([
                'marksheet_header_logo',
                'marksheet_watermark_image',
                'marksheet_footer_logo_1',
                'marksheet_footer_logo_2',
                'marksheet_footer_logo_3',
            ]);
        });
    }
};

