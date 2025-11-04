<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LandingPageController;
use Illuminate\Support\Facades\Route;

// Landing pages - domain-based (detected by middleware)
Route::get('/', [LandingPageController::class, 'index'])->name('home');
Route::get('/about', [LandingPageController::class, 'about'])->name('about');
Route::get('/courses', [LandingPageController::class, 'courses'])->name('courses');

// Student Login (placeholder - will be implemented properly later)
Route::get('/student/login', function () {
    return view('student.login');
})->name('student.login');

// Admin Dashboard (protected)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Student Dashboard (protected - separate guard)
Route::get('/student/dashboard', function () {
    return view('student.dashboard');
})->middleware(['auth:student'])->name('student.dashboard');

// Admin Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
