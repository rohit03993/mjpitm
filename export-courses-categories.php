<?php

/**
 * Export Courses and Categories to SQL file
 * 
 * This script exports all courses and categories from your local database
 * so you can import them on the server.
 * 
 * Usage: php export-courses-categories.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

echo "🚀 Starting export of courses and categories...\n\n";

// Get all categories
$categories = DB::table('course_categories')->get();
echo "📁 Found " . $categories->count() . " categories\n";

// Get all courses
$courses = DB::table('courses')->get();
echo "📚 Found " . $courses->count() . " courses\n\n";

// Create SQL export
$sql = "-- Courses and Categories Export\n";
$sql .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n";
$sql .= "-- Total Categories: " . $categories->count() . "\n";
$sql .= "-- Total Courses: " . $courses->count() . "\n\n";

// Export categories
if ($categories->count() > 0) {
    $sql .= "-- ============================================\n";
    $sql .= "-- COURSE CATEGORIES\n";
    $sql .= "-- ============================================\n\n";
    
    foreach ($categories as $category) {
        $sql .= "INSERT INTO `course_categories` (`id`, `institute_id`, `name`, `code`, `description`, `image`, `display_order`, `status`, `created_at`, `updated_at`) VALUES ";
        $sql .= "(";
        $sql .= $category->id . ", ";
        $sql .= $category->institute_id . ", ";
        $sql .= "'" . addslashes($category->name) . "', ";
        $sql .= ($category->code ? "'" . addslashes($category->code) . "'" : "NULL") . ", ";
        $sql .= ($category->description ? "'" . addslashes($category->description) . "'" : "NULL") . ", ";
        $sql .= ($category->image ? "'" . addslashes($category->image) . "'" : "NULL") . ", ";
        $sql .= ($category->display_order ?? 0) . ", ";
        $sql .= "'" . $category->status . "', ";
        $sql .= "'" . $category->created_at . "', ";
        $sql .= "'" . $category->updated_at . "'";
        $sql .= ") ON DUPLICATE KEY UPDATE ";
        $sql .= "`name`=VALUES(`name`), ";
        $sql .= "`code`=VALUES(`code`), ";
        $sql .= "`description`=VALUES(`description`), ";
        $sql .= "`image`=VALUES(`image`), ";
        $sql .= "`display_order`=VALUES(`display_order`), ";
        $sql .= "`status`=VALUES(`status`), ";
        $sql .= "`updated_at`=VALUES(`updated_at`);\n";
    }
    $sql .= "\n";
}

// Export courses
if ($courses->count() > 0) {
    $sql .= "-- ============================================\n";
    $sql .= "-- COURSES\n";
    $sql .= "-- ============================================\n\n";
    
    foreach ($courses as $course) {
        $sql .= "INSERT INTO `courses` (`id`, `institute_id`, `category_id`, `name`, `code`, `duration_months`, `description`, `image`, `status`, `registration_fee`, `entrance_fee`, `enrollment_fee`, `tuition_fee`, `caution_money`, `hostel_fee_amount`, `late_fee`, `created_at`, `updated_at`) VALUES ";
        $sql .= "(";
        $sql .= $course->id . ", ";
        $sql .= $course->institute_id . ", ";
        $sql .= ($course->category_id ? $course->category_id : "NULL") . ", ";
        $sql .= "'" . addslashes($course->name) . "', ";
        $sql .= ($course->code ? "'" . addslashes($course->code) . "'" : "NULL") . ", ";
        $sql .= ($course->duration_months ?? 0) . ", ";
        $sql .= ($course->description ? "'" . addslashes($course->description) . "'" : "NULL") . ", ";
        $sql .= ($course->image ? "'" . addslashes($course->image) . "'" : "NULL") . ", ";
        $sql .= "'" . $course->status . "', ";
        $sql .= ($course->registration_fee ?? 0) . ", ";
        $sql .= ($course->entrance_fee ?? 0) . ", ";
        $sql .= ($course->enrollment_fee ?? 0) . ", ";
        $sql .= ($course->tuition_fee ?? 0) . ", ";
        $sql .= ($course->caution_money ?? 0) . ", ";
        $sql .= ($course->hostel_fee_amount ?? 0) . ", ";
        $sql .= ($course->late_fee ?? 0) . ", ";
                    $sql .= "'" . $course->created_at . "', ";
        $sql .= "'" . $course->updated_at . "'";
        $sql .= ") ON DUPLICATE KEY UPDATE ";
        $sql .= "`name`=VALUES(`name`), ";
        $sql .= "`code`=VALUES(`code`), ";
        $sql .= "`category_id`=VALUES(`category_id`), ";
        $sql .= "`duration_months`=VALUES(`duration_months`), ";
        $sql .= "`description`=VALUES(`description`), ";
        $sql .= "`image`=VALUES(`image`), ";
        $sql .= "`status`=VALUES(`status`), ";
        $sql .= "`registration_fee`=VALUES(`registration_fee`), ";
        $sql .= "`entrance_fee`=VALUES(`entrance_fee`), ";
        $sql .= "`enrollment_fee`=VALUES(`enrollment_fee`), ";
        $sql .= "`tuition_fee`=VALUES(`tuition_fee`), ";
        $sql .= "`caution_money`=VALUES(`caution_money`), ";
        $sql .= "`hostel_fee_amount`=VALUES(`hostel_fee_amount`), ";
        $sql .= "`late_fee`=VALUES(`late_fee`), ";
        $sql .= "`updated_at`=VALUES(`updated_at`);\n";
    }
    $sql .= "\n";
}

// Save to file
$filename = 'courses-categories-export-' . date('Y-m-d_His') . '.sql';
$filepath = __DIR__ . '/' . $filename;
File::put($filepath, $sql);

echo "✅ Export completed!\n";
echo "📄 File saved: {$filename}\n";
echo "📁 Location: " . realpath($filepath) . "\n\n";
echo "📋 Next steps:\n";
echo "   1. Copy this SQL file to your server\n";
echo "   2. Copy image folders to server:\n";
echo "      - storage/app/public/categories/\n";
echo "      - storage/app/public/courses/\n";
echo "   3. On the server, run: mysql -u [username] -p [database_name] < {$filename}\n";
echo "   4. Or import via phpMyAdmin/MySQL Workbench\n";
echo "   5. Make sure to run migrations first: php artisan migrate\n";
echo "   6. Run: php artisan storage:link (to link storage for images)\n\n";
echo "⚠️  IMPORTANT: Excel import does NOT handle images automatically.\n";
echo "   Images must be:\n";
echo "   - Exported with database (this script includes image paths)\n";
echo "   - OR assigned using Smart Image Assignment feature\n";
echo "   - OR uploaded manually via edit forms\n\n";

