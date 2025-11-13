<?php

/**
 * Script to update existing courses with fee data
 * Run this after running the migration for fee fields
 * 
 * Usage: php update-course-fees.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Course;
use App\Models\Institute;

echo "Updating courses with fee data...\n\n";

// Get institutes
$techInstitute = Institute::where('domain', 'mjpitm.in')->first();
$paramedicalInstitute = Institute::where('domain', 'mjpips.in')->first();

if (!$techInstitute || !$paramedicalInstitute) {
    echo "Error: Institutes not found. Please run InstituteSeeder first.\n";
    exit(1);
}

// Update Tech Institute Courses
$techCourses = [
    'BCA' => [
        'registration_fee' => 1000.00,
        'entrance_fee' => 500.00,
        'enrollment_fee' => 2000.00,
        'tuition_fee' => 45000.00,
        'caution_money' => 5000.00,
        'hostel_fee_amount' => 20000.00,
        'late_fee' => 500.00,
    ],
    'BBA' => [
        'registration_fee' => 1000.00,
        'entrance_fee' => 500.00,
        'enrollment_fee' => 2000.00,
        'tuition_fee' => 40000.00,
        'caution_money' => 5000.00,
        'hostel_fee_amount' => 20000.00,
        'late_fee' => 500.00,
    ],
    'MCA' => [
        'registration_fee' => 1500.00,
        'entrance_fee' => 750.00,
        'enrollment_fee' => 2500.00,
        'tuition_fee' => 55000.00,
        'caution_money' => 5000.00,
        'hostel_fee_amount' => 20000.00,
        'late_fee' => 750.00,
    ],
];

// Update Paramedical Institute Courses
$paramedicalCourses = [
    'DMLT' => [
        'registration_fee' => 800.00,
        'entrance_fee' => 400.00,
        'enrollment_fee' => 1500.00,
        'tuition_fee' => 35000.00,
        'caution_money' => 3000.00,
        'hostel_fee_amount' => 18000.00,
        'late_fee' => 400.00,
    ],
    'B.Sc Nursing' => [
        'registration_fee' => 1200.00,
        'entrance_fee' => 600.00,
        'enrollment_fee' => 2500.00,
        'tuition_fee' => 60000.00,
        'caution_money' => 6000.00,
        'hostel_fee_amount' => 25000.00,
        'late_fee' => 600.00,
    ],
    'BPT' => [
        'registration_fee' => 1200.00,
        'entrance_fee' => 600.00,
        'enrollment_fee' => 2500.00,
        'tuition_fee' => 58000.00,
        'caution_money' => 6000.00,
        'hostel_fee_amount' => 25000.00,
        'late_fee' => 600.00,
    ],
];

// Update Tech Institute Courses
foreach ($techCourses as $code => $fees) {
    $course = Course::where('code', $code)->where('institute_id', $techInstitute->id)->first();
    if ($course) {
        $course->update($fees);
        echo "✓ Updated {$code} with fees\n";
    } else {
        echo "✗ Course {$code} not found\n";
    }
}

// Update Paramedical Institute Courses
foreach ($paramedicalCourses as $code => $fees) {
    $course = Course::where('code', $code)->where('institute_id', $paramedicalInstitute->id)->first();
    if ($course) {
        $course->update($fees);
        echo "✓ Updated {$code} with fees\n";
    } else {
        echo "✗ Course {$code} not found\n";
    }
}

echo "\nDone! All courses have been updated with fee data.\n";
echo "You can now test the student registration form to see fees auto-populate.\n";

