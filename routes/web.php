<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {

            // Super Admin
        Route::prefix('superadmin')->name('superadmin.')->middleware('role:Super Admin')->group(function () {
            Route::get('/dashboard', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('dashboard');
            Route::get('/accounts', [App\Http\Controllers\SuperAdmin\UserController::class, 'index'])->name('accounts');
            Route::post('/accounts', [App\Http\Controllers\SuperAdmin\UserController::class, 'store'])->name('accounts.store');
            Route::get('/accounts/{user}/edit', [App\Http\Controllers\SuperAdmin\UserController::class, 'edit'])->name('accounts.edit');
            Route::put('/accounts/{user}', [App\Http\Controllers\SuperAdmin\UserController::class, 'update'])->name('accounts.update');
            Route::delete('/accounts/{user}', [App\Http\Controllers\SuperAdmin\UserController::class, 'destroy'])->name('accounts.destroy');
            Route::patch('/accounts/{user}/toggle', [App\Http\Controllers\SuperAdmin\UserController::class, 'toggleActive'])->name('accounts.toggle');
            Route::get('/activity-logs', [App\Http\Controllers\SuperAdmin\ActivityLogController::class, 'index'])->name('activity-logs');
            Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
            Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
            Route::put('/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
            Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
            Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
            Route::put('/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
            Route::get('/roles', [App\Http\Controllers\SuperAdmin\RolesController::class, 'index'])->name('roles');
            Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
            Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
            Route::put('/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');

        });

        // Admin
        Route::prefix('admin')->name('admin.')->middleware('role:Admin')->group(function () {
            Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        });

        // Registrar
       Route::prefix('registrar')->name('registrar.')->middleware('role:Registrar')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Registrar\DashboardController::class, 'index'])->name('dashboard');

    // Student Records
    Route::get('/students', [App\Http\Controllers\Registrar\StudentController::class, 'index'])->name('students');
    Route::get('/students/create', [App\Http\Controllers\Registrar\StudentController::class, 'create'])->name('students.create');
    Route::post('/students', [App\Http\Controllers\Registrar\StudentController::class, 'store'])->name('students.store');
    Route::get('/students/{student}/edit', [App\Http\Controllers\Registrar\StudentController::class, 'edit'])->name('students.edit');
    Route::put('/students/{student}', [App\Http\Controllers\Registrar\StudentController::class, 'update'])->name('students.update');

    // Study Load (Section Class Program)
    Route::get('/study-load', [App\Http\Controllers\Registrar\StudyLoadController::class, 'index'])->name('study-load');
    Route::post('/study-load', [App\Http\Controllers\Registrar\StudyLoadController::class, 'store'])->name('study-load.store');
    Route::delete('/study-load/{schedule}', [App\Http\Controllers\Registrar\StudyLoadController::class, 'destroy'])->name('study-load.destroy');
    Route::get('/sections', [App\Http\Controllers\Registrar\SectionController::class, 'index'])->name('sections');
    Route::post('/sections', [App\Http\Controllers\Registrar\SectionController::class, 'store'])->name('sections.store');
    Route::delete('/sections/{section}', [App\Http\Controllers\Registrar\SectionController::class, 'destroy'])->name('sections.destroy');

    // Grade Approval
    Route::get('/grade-approval', [App\Http\Controllers\Registrar\GradeApprovalController::class, 'index'])->name('grade-approval');
    Route::patch('/grade-approval/{finalGrade}/approve', [App\Http\Controllers\Registrar\GradeApprovalController::class, 'approve'])->name('grade-approval.approve');
    Route::patch('/grade-approval/{finalGrade}/reject', [App\Http\Controllers\Registrar\GradeApprovalController::class, 'reject'])->name('grade-approval.reject');

    // Profile
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
});

        // Teacher
        Route::prefix('teacher')->name('teacher.')->middleware('role:Teacher')->group(function () {
            Route::get('/dashboard', [App\Http\Controllers\Teacher\DashboardController::class, 'index'])->name('dashboard');
        });

        // SSG
        Route::prefix('ssg')->name('ssg.')->middleware('role:SSG')->group(function () {
            Route::get('/dashboard', [App\Http\Controllers\SSG\DashboardController::class, 'index'])->name('dashboard');
        });

        // Student
        Route::prefix('student')->name('student.')->middleware('role:Student')->group(function () {
            Route::get('/dashboard', [App\Http\Controllers\Student\DashboardController::class, 'index'])->name('dashboard');
        });

});

require __DIR__.'/auth.php';
