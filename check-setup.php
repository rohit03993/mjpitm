<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Course;
use App\Models\Institute;
use App\Models\User;

echo "=== SETUP VERIFICATION ===" . PHP_EOL . PHP_EOL;

// Check Institutes
echo "1. Institutes: " . Institute::count() . PHP_EOL;
$institutes = Institute::all(['id', 'name', 'domain']);
foreach ($institutes as $inst) {
    echo "   - ID: {$inst->id} - {$inst->name} ({$inst->domain})" . PHP_EOL;
}

echo PHP_EOL;

// Check Courses
echo "2. Courses: " . Course::count() . PHP_EOL;
$courses = Course::all(['id', 'name', 'institute_id']);
if ($courses->count() > 0) {
    foreach ($courses as $course) {
        $institute = Institute::find($course->institute_id);
        echo "   - ID: {$course->id} - {$course->name} (Institute: " . ($institute ? $institute->name : 'N/A') . ")" . PHP_EOL;
    }
} else {
    echo "   ⚠️  No courses found! You need to create courses before registering students." . PHP_EOL;
}

echo PHP_EOL;

// Check Admins
echo "3. Admin Users: " . User::whereIn('role', ['super_admin', 'institute_admin'])->count() . PHP_EOL;
$admins = User::whereIn('role', ['super_admin', 'institute_admin'])->get(['id', 'name', 'email', 'role', 'institute_id']);
foreach ($admins as $admin) {
    $institute = $admin->institute_id ? Institute::find($admin->institute_id) : null;
    echo "   - {$admin->name} ({$admin->email}) - Role: {$admin->role}" . ($institute ? " - Institute: {$institute->name}" : "") . PHP_EOL;
}

echo PHP_EOL;

// Check Storage Link
$storageLink = public_path('storage');
if (file_exists($storageLink) && is_link($storageLink)) {
    echo "4. Storage Link: ✓ Created" . PHP_EOL;
} else {
    echo "4. Storage Link: ✗ Not found. Run: php artisan storage:link" . PHP_EOL;
}

echo PHP_EOL;
echo "=== ACCESS URLS ===" . PHP_EOL;
echo "Admin Dashboard: http://localhost:8000/admin/dashboard" . PHP_EOL;
echo "Student Registration: http://localhost:8000/admin/students/create" . PHP_EOL;
echo "Students List: http://localhost:8000/admin/students" . PHP_EOL;
echo "Login: http://localhost:8000/login" . PHP_EOL;

