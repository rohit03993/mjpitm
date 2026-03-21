<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Must run after 2026_03_19_000001 (marksheet template fields 1–3).
 * Older filename 2026_02_16_* ran before 000001 alphabetically and failed on `after(marksheet_footer_logo_3)`.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('institutes') || Schema::hasColumn('institutes', 'marksheet_footer_logo_4')) {
            return;
        }

        if (Schema::hasColumn('institutes', 'marksheet_footer_logo_3')) {
            Schema::table('institutes', function (Blueprint $table) {
                $table->string('marksheet_footer_logo_4')->nullable()->after('marksheet_footer_logo_3');
            });
        } elseif (Schema::hasColumn('institutes', 'marksheet_watermark_image')) {
            Schema::table('institutes', function (Blueprint $table) {
                $table->string('marksheet_footer_logo_4')->nullable()->after('marksheet_watermark_image');
            });
        } else {
            Schema::table('institutes', function (Blueprint $table) {
                $table->string('marksheet_footer_logo_4')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('institutes') || ! Schema::hasColumn('institutes', 'marksheet_footer_logo_4')) {
            return;
        }
        Schema::table('institutes', function (Blueprint $table) {
            $table->dropColumn('marksheet_footer_logo_4');
        });
    }
};
