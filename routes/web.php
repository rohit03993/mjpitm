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
Route::get('/course/{category}', [LandingPageController::class, 'categoryCourses'])->name('courses.category');

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
    
    // Student semester result download
    Route::get('/student/semester-result/{semesterResult}/download', [\App\Http\Controllers\Admin\SemesterResultController::class, 'studentDownload'])->name('student.semester-result.download');
});

// Staff Dashboard Routes (protected) - for Institute Admin/Staff only (NOT Super Admin)
Route::middleware(['auth'])->group(function () {
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
    
    // Website Registrations Route (MUST come BEFORE resource route to avoid conflicts)
    Route::get('/admin/students/website-registrations', [\App\Http\Controllers\Admin\StudentController::class, 'websiteRegistrations'])->name('admin.students.website-registrations');

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

    // Notification Routes
    Route::get('/admin/notifications/unread', [\App\Http\Controllers\Admin\NotificationController::class, 'getUnread'])->name('admin.notifications.unread');
    Route::post('/admin/notifications/{id}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('admin.notifications.read');
    Route::post('/admin/notifications/read-all', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('admin.notifications.read-all');

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

    // Semester Result Management Routes - Accessible to both Super Admin and Institute Admin
    Route::get('/admin/courses/{course}/semester/{semester}/subjects', [\App\Http\Controllers\Admin\CourseController::class, 'manageSemesterSubjects'])->name('admin.courses.semester.subjects');
    Route::post('/admin/courses/{course}/semester/{semester}/subjects', [\App\Http\Controllers\Admin\CourseController::class, 'storeSemesterSubjects'])->name('admin.courses.semester.subjects.store');
    Route::get('/admin/students/{student}/generate-semester-result', [\App\Http\Controllers\Admin\SemesterResultController::class, 'create'])->name('admin.students.generate-semester-result');
    Route::post('/admin/students/{student}/generate-semester-result', [\App\Http\Controllers\Admin\SemesterResultController::class, 'store'])->name('admin.students.generate-semester-result.store');
    Route::get('/admin/semester-results/{semesterResult}', [\App\Http\Controllers\Admin\SemesterResultController::class, 'show'])->name('admin.semester-results.show');
    Route::post('/admin/semester-results/{semesterResult}/publish', [\App\Http\Controllers\Admin\SemesterResultController::class, 'publish'])->name('admin.semester-results.publish');
    Route::get('/admin/semester-results/{semesterResult}/download', [\App\Http\Controllers\Admin\SemesterResultController::class, 'downloadPdf'])->name('admin.semester-results.download');

    // ID Card generation (for both Super Admin and Staff - controller handles permission)
    Route::get('/admin/view/id-card/{student}', [\App\Http\Controllers\IdCardController::class, 'view'])->name('admin.documents.view.idcard');
    Route::get('/admin/download/id-card/{student}', [\App\Http\Controllers\IdCardController::class, 'download'])->name('admin.documents.download.idcard');
    
    // Registration form view/download (for both Super Admin and Staff)
    Route::get('/admin/view/registration-form/{student}', [\App\Http\Controllers\DocumentsController::class, 'viewRegistrationForm'])->name('admin.documents.view.registration');
    Route::get('/admin/download/registration-form/{student}', [\App\Http\Controllers\DocumentsController::class, 'downloadRegistrationForm'])->name('admin.documents.download.registration');
});

// Super Admin Dashboard Routes (protected)
Route::middleware(['auth', EnsureUserIsSuperAdmin::class])->group(function () {
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
    
    // Category Move Routes
    Route::get('/admin/categories/{category}/move', [\App\Http\Controllers\Admin\CourseCategoryController::class, 'move'])->name('admin.categories.move');
    Route::post('/admin/categories/{category}/move', [\App\Http\Controllers\Admin\CourseCategoryController::class, 'processMove'])->name('admin.categories.move.process');

    // Course Bulk Import Routes - MUST come BEFORE resource route to avoid conflicts
    Route::get('/admin/courses/import', [\App\Http\Controllers\Admin\CourseController::class, 'showImport'])->name('admin.courses.import');
    Route::post('/admin/courses/import/preview', [\App\Http\Controllers\Admin\CourseController::class, 'previewImport'])->name('admin.courses.import.preview');
    Route::post('/admin/courses/import/process', [\App\Http\Controllers\Admin\CourseController::class, 'processImport'])->name('admin.courses.import.process');

    // Bulk Image Upload Routes
    Route::get('/admin/bulk-image-upload', [\App\Http\Controllers\Admin\BulkImageUploadController::class, 'index'])->name('admin.bulk-image-upload');
    Route::post('/admin/bulk-image-upload', [\App\Http\Controllers\Admin\BulkImageUploadController::class, 'upload'])->name('admin.bulk-image-upload.upload');

    // Smart Image Assignment Routes
    Route::get('/admin/smart-image-assignment', [\App\Http\Controllers\Admin\SmartImageController::class, 'index'])->name('admin.smart-image-assignment');
    Route::post('/admin/smart-image-assignment', [\App\Http\Controllers\Admin\SmartImageController::class, 'assign'])->name('admin.smart-image-assignment.assign');
    Route::post('/admin/smart-image-assignment/all', [\App\Http\Controllers\Admin\SmartImageController::class, 'assignAll'])->name('admin.smart-image-assignment.assign-all');

    // Course Management Routes - ONLY Super Admin (must come AFTER import routes)
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

    // Super Admin - Manage Users (with password management)
    Route::get('/superadmin/manage-users', [\App\Http\Controllers\SuperAdmin\ManageUsersController::class, 'index'])->name('superadmin.manage-users.index');
    Route::get('/superadmin/manage-users/user/{user}/view-password', [\App\Http\Controllers\SuperAdmin\ManageUsersController::class, 'viewUserPassword'])->name('superadmin.manage-users.view-user-password');
    Route::get('/superadmin/manage-users/student/{student}/view-password', [\App\Http\Controllers\SuperAdmin\ManageUsersController::class, 'viewStudentPassword'])->name('superadmin.manage-users.view-student-password');
    Route::post('/superadmin/manage-users/user/{user}/generate-password', [\App\Http\Controllers\SuperAdmin\ManageUsersController::class, 'generateUserPassword'])->name('superadmin.manage-users.generate-user-password');
    Route::post('/superadmin/manage-users/student/{student}/generate-password', [\App\Http\Controllers\SuperAdmin\ManageUsersController::class, 'generateStudentPassword'])->name('superadmin.manage-users.generate-student-password');
    Route::post('/superadmin/manage-users/user/{user}/update-password', [\App\Http\Controllers\SuperAdmin\ManageUsersController::class, 'updateUserPassword'])->name('superadmin.manage-users.update-user-password');
    Route::post('/superadmin/manage-users/student/{student}/update-password', [\App\Http\Controllers\SuperAdmin\ManageUsersController::class, 'updateStudentPassword'])->name('superadmin.manage-users.update-student-password');

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
