<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Registrar\DashboardController as RegistrarDashboardController;
use App\Http\Controllers\Registrar\GradeApprovalController;
use App\Http\Controllers\Registrar\SectionController;
use App\Http\Controllers\Registrar\StudentController;
use App\Http\Controllers\Registrar\StudyLoadController;
use App\Http\Controllers\SSG\DashboardController as SsgDashboardController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\SuperAdmin\ActivityLogController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\RolesController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();

        return match (true) {
            $user->hasRole('Super Admin') => redirect()->route('superadmin.dashboard'),
            $user->hasRole('Admin') => redirect()->route('admin.dashboard'),
            $user->hasRole('Registrar') => redirect()->route('registrar.dashboard'),
            $user->hasRole('Teacher') => redirect()->route('teacher.dashboard'),
            $user->hasRole('SSG') => redirect()->route('ssg.dashboard'),
            $user->hasRole('Student') => redirect()->route('student.dashboard'),
            default => redirect()->route('login'),
        };
    })->name('dashboard');

    Route::prefix('superadmin')->name('superadmin.')->middleware('role:Super Admin')->group(function () {
        Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/accounts', [UserController::class, 'index'])->name('accounts');
        Route::post('/accounts', [UserController::class, 'store'])->name('accounts.store');
        Route::get('/accounts/{user}/edit', [UserController::class, 'edit'])->name('accounts.edit');
        Route::put('/accounts/{user}', [UserController::class, 'update'])->name('accounts.update');
        Route::delete('/accounts/{user}', [UserController::class, 'destroy'])->name('accounts.destroy');
        Route::patch('/accounts/{user}/toggle', [UserController::class, 'toggleActive'])->name('accounts.toggle');
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs');
        Route::get('/roles', [RolesController::class, 'index'])->name('roles');
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    });

    Route::prefix('admin')->name('admin.')->middleware('role:Admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    });

    Route::prefix('registrar')->name('registrar.')->middleware('role:Registrar')->group(function () {
        Route::get('/dashboard', [RegistrarDashboardController::class, 'index'])->name('dashboard');
        Route::get('/students', [StudentController::class, 'index'])->name('students');
        Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
        Route::post('/students', [StudentController::class, 'store'])->name('students.store');
        Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
        Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
        Route::get('/study-load', [StudyLoadController::class, 'index'])->name('study-load');
        Route::post('/study-load', [StudyLoadController::class, 'store'])->name('study-load.store');
        Route::delete('/study-load/{schedule}', [StudyLoadController::class, 'destroy'])->name('study-load.destroy');
        Route::get('/sections', [SectionController::class, 'index'])->name('sections');
        Route::post('/sections', [SectionController::class, 'store'])->name('sections.store');
        Route::delete('/sections/{section}', [SectionController::class, 'destroy'])->name('sections.destroy');
        Route::get('/grade-approval', [GradeApprovalController::class, 'index'])->name('grade-approval');
        Route::patch('/grade-approval/{finalGrade}/approve', [GradeApprovalController::class, 'approve'])->name('grade-approval.approve');
        Route::patch('/grade-approval/{finalGrade}/reject', [GradeApprovalController::class, 'reject'])->name('grade-approval.reject');
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    });

    Route::prefix('teacher')->name('teacher.')->middleware('role:Teacher')->group(function () {
        Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
    });

    Route::prefix('ssg')->name('ssg.')->middleware('role:SSG')->group(function () {
        Route::get('/dashboard', [SsgDashboardController::class, 'index'])->name('dashboard');
    });

    Route::prefix('student')->name('student.')->middleware('role:Student')->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    });
});

require __DIR__.'/auth.php';
