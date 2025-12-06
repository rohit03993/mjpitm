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
Route::get('/registration-form', [\App\Http\Controllers\PublicRegistrationController::class, 'create'])->name('public.registration');
Route::post('/registration-form', [\App\Http\Controllers\PublicRegistrationController::class, 'store'])->name('public.registration.store');
Route::get('/registration-success/{student}', [\App\Http\Controllers\PublicRegistrationController::class, 'success'])->name('public.registration.success');

// Combined Login choice page (Super Admin / Staff / Student)
Route::get('/login-options', function () {
    return view('auth.login-options');
})->name('login.options');

// Super Admin Authentication Routes (with rate limiting: 5 attempts per minute)
Route::middleware(['guest', 'throttle:5,1'])->group(function () {
    Route::get('/superadmin/login', [SuperAdminAuthController::class, 'showLoginForm'])->name('superadmin.login');
    Route::post('/superadmin/login', [SuperAdminAuthController::class, 'login']);
});

// Staff Authentication Routes (with rate limiting: 5 attempts per minute)
Route::middleware(['guest', 'throttle:5,1'])->group(function () {
    Route::get('/staff/login', [StaffAuthController::class, 'showLoginForm'])->name('staff.login');
    Route::post('/staff/login', [StaffAuthController::class, 'login']);
});

// Student Authentication Routes (with rate limiting: 5 attempts per minute)
Route::middleware(['guest', 'throttle:5,1'])->group(function () {
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
    
    // Student ID Card preview and download (for students - their own card)
    Route::get('/student/view/id-card', [\App\Http\Controllers\IdCardController::class, 'studentPreview'])->name('student.documents.view.idcard');
    Route::get('/student/download/id-card', [\App\Http\Controllers\IdCardController::class, 'studentDownload'])->name('student.documents.download.idcard');
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

    // Fee Management Routes - Accessible to both Super Admin and Institute Admin
    Route::resource('admin/fees', \App\Http\Controllers\Admin\FeeController::class)->names([
        'index' => 'admin.fees.index',
        'create' => 'admin.fees.create',
        'store' => 'admin.fees.store',
        'show' => 'admin.fees.show',
    ]);
    Route::get('/admin/fees/verification-queue', [\App\Http\Controllers\Admin\FeeController::class, 'verificationQueue'])->name('admin.fees.verification-queue');
    Route::post('/admin/fees/{fee}/verify', [\App\Http\Controllers\Admin\FeeController::class, 'verify'])->name('admin.fees.verify');
    Route::post('/admin/fees/{fee}/reject', [\App\Http\Controllers\Admin\FeeController::class, 'reject'])->name('admin.fees.reject');

    // Result Management Routes - Accessible to both Super Admin and Institute Admin
    Route::resource('admin/results', \App\Http\Controllers\Admin\ResultController::class)->names([
        'index' => 'admin.results.index',
        'create' => 'admin.results.create',
        'store' => 'admin.results.store',
        'show' => 'admin.results.show',
    ]);
    Route::get('/admin/results/verification-queue', [\App\Http\Controllers\Admin\ResultController::class, 'verificationQueue'])->name('admin.results.verification-queue');
    Route::post('/admin/results/{result}/verify', [\App\Http\Controllers\Admin\ResultController::class, 'verify'])->name('admin.results.verify');
    Route::post('/admin/results/{result}/reject', [\App\Http\Controllers\Admin\ResultController::class, 'reject'])->name('admin.results.reject');
    Route::post('/admin/results/{result}/publish', [\App\Http\Controllers\Admin\ResultController::class, 'publish'])->name('admin.results.publish');

    // ID Card generation (for both Super Admin and Staff - controller handles permission)
    Route::get('/admin/view/id-card/{student}', [\App\Http\Controllers\IdCardController::class, 'view'])->name('admin.documents.view.idcard');
    Route::get('/admin/download/id-card/{student}', [\App\Http\Controllers\IdCardController::class, 'download'])->name('admin.documents.download.idcard');
    
    // Registration form view/download (for both Super Admin and Staff)
    Route::get('/admin/view/registration-form/{student}', [\App\Http\Controllers\DocumentsController::class, 'viewRegistrationForm'])->name('admin.documents.view.registration');
    Route::get('/admin/download/registration-form/{student}', [\App\Http\Controllers\DocumentsController::class, 'downloadRegistrationForm'])->name('admin.documents.download.registration');
});

// Super Admin Dashboard Routes (protected)
Route::middleware(['auth', 'verified', EnsureUserIsSuperAdmin::class])->group(function () {
    Route::get('/superadmin/dashboard', [SuperAdminDashboardController::class, 'index'])->name('superadmin.dashboard');
    Route::post('/superadmin/logout', [SuperAdminAuthController::class, 'logout'])->name('superadmin.logout');

    // Course Category Management Routes - ONLY Super Admin
    Route::resource('admin/categories', \App\Http\Controllers\Admin\CourseCategoryController::class)->names([
        'index' => 'admin.categories.index',
        'create' => 'admin.categories.create',
        'store' => 'admin.categories.store',
        'show' => 'admin.categories.show',
        'edit' => 'admin.categories.edit',
        'update' => 'admin.categories.update',
        'destroy' => 'admin.categories.destroy',
    ]);

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

    // Subject Management Routes - ONLY Super Admin
    Route::resource('admin/subjects', \App\Http\Controllers\Admin\SubjectController::class)->names([
        'index' => 'admin.subjects.index',
        'create' => 'admin.subjects.create',
        'store' => 'admin.subjects.store',
        'show' => 'admin.subjects.show',
        'edit' => 'admin.subjects.edit',
        'update' => 'admin.subjects.update',
        'destroy' => 'admin.subjects.destroy',
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

    // Super Admin - Institute Management
    Route::resource('superadmin/institutes', \App\Http\Controllers\SuperAdmin\InstituteController::class)->names([
        'index' => 'superadmin.institutes.index',
        'create' => 'superadmin.institutes.create',
        'store' => 'superadmin.institutes.store',
        'show' => 'superadmin.institutes.show',
        'edit' => 'superadmin.institutes.edit',
        'update' => 'superadmin.institutes.update',
        'destroy' => 'superadmin.institutes.destroy',
    ]);

    // Super Admin - System Reset (Danger Zone)
    Route::get('/superadmin/system-reset', [\App\Http\Controllers\SuperAdmin\SystemResetController::class, 'index'])->name('superadmin.system-reset');
    Route::post('/superadmin/system-reset/all', [\App\Http\Controllers\SuperAdmin\SystemResetController::class, 'reset'])->name('superadmin.system-reset.all');
    Route::post('/superadmin/system-reset/students', [\App\Http\Controllers\SuperAdmin\SystemResetController::class, 'resetStudents'])->name('superadmin.system-reset.students');
    Route::post('/superadmin/system-reset/guests', [\App\Http\Controllers\SuperAdmin\SystemResetController::class, 'resetGuests'])->name('superadmin.system-reset.guests');
});

// Admin Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
