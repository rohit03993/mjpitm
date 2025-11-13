<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Course;

echo "Total Courses: " . Course::count() . PHP_EOL . PHP_EOL;

$courses = Course::all(['id', 'name', 'institute_id']);

if ($courses->count() > 0) {
    echo "Courses List:" . PHP_EOL;
    foreach ($courses as $course) {
        echo "ID: {$course->id} - {$course->name} (Institute ID: {$course->institute_id})" . PHP_EOL;
    }
} else {
    echo "No courses found. You need to create courses before registering students." . PHP_EOL;
}

