<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\Auth\StudentAuthController;
use App\Http\Controllers\Auth\SuperAdminAuthController;
use App\Http\Controllers\Auth\StaffAuthController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\UserController as SuperAdminUserController;
use App\Http\Middleware\EnsureUserIsSuperAdmin;
use Illuminate\Support\Facades\Route;

// Landing pages - domain-based (detected by middleware)
Route::get('/', [LandingPageController::class, 'index'])->name('home');
Route::get('/about', [LandingPageController::class, 'about'])->name('about');
Route::get('/courses', [LandingPageController::class, 'courses'])->name('courses');

// Public Registration Form Routes (for general access)
Route::get('/registration-form', function() {
    return view('documents.registration-form', ['formExists' => false]);
})->name('registration.form');

// Combined Login choice page (Super Admin / Staff / Student)
Route::get('/login-options', function () {
    return view('auth.login-options');
})->name('login.options');

// Super Admin Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/superadmin/login', [SuperAdminAuthController::class, 'showLoginForm'])->name('superadmin.login');
    Route::post('/superadmin/login', [SuperAdminAuthController::class, 'login']);
});

// Staff Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/staff/login', [StaffAuthController::class, 'showLoginForm'])->name('staff.login');
    Route::post('/staff/login', [StaffAuthController::class, 'login']);
});

// Student Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/student/login', [StudentAuthController::class, 'showLoginForm'])->name('student.login');
    Route::post('/student/login', [StudentAuthController::class, 'login']);
    Route::get('/student/forgot-password', [StudentAuthController::class, 'showPasswordRequestForm'])->name('student.password.request');
    Route::post('/student/forgot-password', [StudentAuthController::class, 'sendPasswordResetLink'])->name('student.password.email');
});

// Student Dashboard (protected - separate guard)
Route::middleware(['auth:student'])->group(function () {
    Route::get('/student/dashboard', [StudentDashboardController::class, 'index'])->name('student.dashboard');
    Route::post('/student/logout', [StudentAuthController::class, 'logout'])->name('student.logout');
    
    // Protected document downloads (for students - their own form)
    Route::get('/student/download/registration-form', function() {
        $student = auth()->guard('student')->user();
        return app(\App\Http\Controllers\DocumentsController::class)->downloadRegistrationForm($student->id);
    })->name('student.documents.download.registration');
});

// Staff Dashboard Routes (protected) - for Institute Admin/Staff only (NOT Super Admin)
Route::middleware(['auth', 'verified'])->group(function () {
    // Staff dashboard - only accessible to non-super-admin users
    // The controller will redirect super admin to super admin dashboard
    Route::get('/staff/dashboard', [StaffDashboardController::class, 'index'])->name('staff.dashboard');
    Route::post('/staff/logout', [StaffAuthController::class, 'logout'])->name('staff.logout');
    
    // Protected document downloads (for staff)
    Route::get('/staff/download/registration-form/{student}', [\App\Http\Controllers\DocumentsController::class, 'downloadRegistrationForm'])->name('staff.documents.download.registration');
    
    // Legacy admin dashboard route - redirects staff to staff dashboard
    Route::get('/dashboard', function () {
        if (auth()->user()->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard');
        }
        return redirect()->route('staff.dashboard');
    })->name('dashboard');
    
    Route::get('/admin/dashboard', function () {
        if (auth()->user()->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard');
        }
        return redirect()->route('staff.dashboard');
    })->name('admin.dashboard');
    
    // Student Management Routes
    // Staff: can list, create, store, and view students they created.
    // Super Admin: can also edit status / roll no (enforced in controller).
    Route::resource('admin/students', \App\Http\Controllers\Admin\StudentController::class)->names([
        'index' => 'admin.students.index',
        'create' => 'admin.students.create',
        'store' => 'admin.students.store',
        'show' => 'admin.students.show',
        'edit' => 'admin.students.edit',
        'update' => 'admin.students.update',
        'destroy' => 'admin.students.destroy',
    ]);
});

// Super Admin Dashboard Routes (protected)
Route::middleware(['auth', 'verified', EnsureUserIsSuperAdmin::class])->group(function () {
    Route::get('/superadmin/dashboard', [SuperAdminDashboardController::class, 'index'])->name('superadmin.dashboard');
    Route::post('/superadmin/logout', [SuperAdminAuthController::class, 'logout'])->name('superadmin.logout');
    
    // Protected document views and downloads (for admin)
    Route::get('/admin/view/registration-form/{student}', [\App\Http\Controllers\DocumentsController::class, 'viewRegistrationForm'])->name('admin.documents.view.registration');
    Route::get('/admin/download/registration-form/{student}', [\App\Http\Controllers\DocumentsController::class, 'downloadRegistrationForm'])->name('admin.documents.download.registration');

    // Course Management Routes - ONLY Super Admin
    Route::resource('admin/courses', \App\Http\Controllers\Admin\CourseController::class)->names([
        'index' => 'admin.courses.index',
        'create' => 'admin.courses.create',
        'store' => 'admin.courses.store',
        'show' => 'admin.courses.show',
        'edit' => 'admin.courses.edit',
        'update' => 'admin.courses.update',
        'destroy' => 'admin.courses.destroy',
    ]);

    // Super Admin - Admin Management
    Route::resource('superadmin/users', SuperAdminUserController::class)->only([
        'index',
        'create',
        'store',
        'edit',
        'update',
    ])->names([
        'index' => 'superadmin.users.index',
        'create' => 'superadmin.users.create',
        'store' => 'superadmin.users.store',
        'edit' => 'superadmin.users.edit',
        'update' => 'superadmin.users.update',
    ]);
});

// Admin Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
