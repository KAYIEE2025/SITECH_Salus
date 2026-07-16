<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ActivityLogController as AdminActivityLogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Registrar\DashboardController as RegistrarDashboardController;
use App\Http\Controllers\Registrar\GradeApprovalController;
use App\Http\Controllers\Registrar\SectionController;
use App\Http\Controllers\Registrar\StudentController;
use App\Http\Controllers\Registrar\StudyLoadController;
use App\Http\Controllers\SSG\AttendanceController as SsgAttendanceController;
use App\Http\Controllers\SSG\DashboardController as SsgDashboardController;
use App\Http\Controllers\SSG\EventController as SsgEventController;
use App\Http\Controllers\SSG\FineController as SsgFineController;
use App\Http\Controllers\SSG\ProfileController as SsgProfileController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\GradeViewController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;
use App\Http\Controllers\Student\StudyLoadController as StudentStudyLoadController;
use App\Http\Controllers\Student\AnnouncementController as StudentAnnouncementController;
use App\Http\Controllers\Student\SchoolCalendarController as StudentSchoolCalendarController;
use App\Http\Controllers\Student\SSGEventController as StudentSSGEventController;
use App\Http\Controllers\SuperAdmin\ActivityLogController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\FineRecordsController;
use App\Http\Controllers\SuperAdmin\RoleManagementController;
use App\Http\Controllers\SuperAdmin\RolesController;
use App\Http\Controllers\SuperAdmin\StudentAccountController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\Teacher\AnnouncementController as TeacherAnnouncementController;
use App\Http\Controllers\Teacher\ClassController as TeacherClassController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\GradeManagementController as TeacherGradeManagementController;
use App\Http\Controllers\Teacher\GradeSubmissionController as TeacherGradeSubmissionController;
use App\Http\Controllers\Teacher\ProfileController as TeacherProfileController;
use App\Http\Controllers\Teacher\ScheduleController as TeacherScheduleController;
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
            $user->hasRole('Student') => redirect()->route('student.dashboard'),
            $user->hasRole('SSG') => redirect()->route('ssg.dashboard'),
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
        Route::get('/student-accounts', [StudentAccountController::class, 'index'])->name('student-accounts.index');
        Route::get('/student-accounts/{student}', fn () => redirect()
            ->route('superadmin.student-accounts.index')
            ->with('error', 'Use the Generate button to create a student account.'))
            ->name('student-accounts.show');
        Route::post('/student-accounts/{student}', [StudentAccountController::class, 'store'])->name('student-accounts.store');
        Route::post('/student-accounts', [StudentAccountController::class, 'bulkStore'])->name('student-accounts.bulk-store');
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs');
        Route::get('/activity-logs/pdf', [ActivityLogController::class, 'generatePdf'])->name('activity-logs.pdf');
        Route::get('/fine-records', [FineRecordsController::class, 'index'])->name('fine-records.index');
        Route::post('/fine-records/report', [FineRecordsController::class, 'generateReport'])->name('fine-records.report');
        Route::get('/roles', [RolesController::class, 'index'])->name('roles');
        Route::get('/role-management', [RoleManagementController::class, 'index'])->name('role-management');
        Route::get('/api/user-roles/{user}', [RoleManagementController::class, 'getUserRoles'])->name('api.user-roles');
        Route::put('/role-management/{user}', [RoleManagementController::class, 'update'])->name('role-management.update');
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    });

    Route::prefix('admin')->name('admin.')->middleware('role:Admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::prefix('announcements')->name('announcements.')->group(function () {
            Route::get('/', [AnnouncementController::class, 'index'])->name('index');
            Route::get('/create', [AnnouncementController::class, 'create'])->name('create');
            Route::post('/', [AnnouncementController::class, 'store'])->name('store');
            Route::get('/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('edit');
            Route::put('/{announcement}', [AnnouncementController::class, 'update'])->name('update');
            Route::delete('/{announcement}', [AnnouncementController::class, 'destroy'])->name('destroy');
        });
        Route::prefix('calendar')->name('calendar.')->group(function () {
            Route::get('/', [CalendarController::class, 'index'])->name('index');
            Route::get('/create', [CalendarController::class, 'create'])->name('create');
            Route::post('/', [CalendarController::class, 'store'])->name('store');
            Route::delete('/{event}', [CalendarController::class, 'destroy'])->name('destroy');
        });
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::post('/student-list', [ReportController::class, 'generateStudentList'])->name('student-list');
            Route::post('/grade-summary', [ReportController::class, 'generateGradeSummary'])->name('grade-summary');
        });
        Route::prefix('activity-logs')->name('activity-logs.')->group(function () {
            Route::get('/', [AdminActivityLogController::class, 'index'])->name('index');
        });
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'index'])->name('index');
            Route::put('/', [ProfileController::class, 'update'])->name('update');
            Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
        });
    });

    Route::prefix('registrar')->name('registrar.')->middleware('role:Registrar')->group(function () {
        Route::get('/dashboard', [RegistrarDashboardController::class, 'index'])->name('dashboard');
        Route::get('/students', [StudentController::class, 'index'])->name('students');
        Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
        Route::post('/students', [StudentController::class, 'store'])->name('students.store');
        Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
        Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
        Route::get('/students/{student}/print-class-schedule', [StudentController::class, 'printClassSchedule'])->name('students.print-class-schedule');
        Route::get('/study-load', [StudyLoadController::class, 'index'])->name('study-load');
        Route::post('/study-load', [StudyLoadController::class, 'store'])->name('study-load.store');
        Route::delete('/study-load/{schedule}', [StudyLoadController::class, 'destroy'])->name('study-load.destroy');
        Route::get('/sections', [SectionController::class, 'index'])->name('sections');
        Route::post('/sections', [SectionController::class, 'store'])->name('sections.store');
        Route::delete('/sections/{section}', [SectionController::class, 'destroy'])->name('sections.destroy');
        Route::get('/grade-approval', [GradeApprovalController::class, 'index'])->name('grade-approval');
        Route::get('/grade-approval/{classSchedule}/view', [GradeApprovalController::class, 'view'])->name('grade-approval.view');
        Route::patch('/grade-approval/{classSchedule}/approve-class', [GradeApprovalController::class, 'approveClass'])->name('grade-approval.approve-class');
        Route::patch('/grade-approval/{classSchedule}/reject-class', [GradeApprovalController::class, 'rejectClass'])->name('grade-approval.reject-class');
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    });

    Route::prefix('teacher')->name('teacher.')->middleware('role:Teacher')->group(function () {
        Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
        Route::get('/schedule', [TeacherScheduleController::class, 'index'])->name('schedule.index');
        Route::get('/schedule/print', [TeacherScheduleController::class, 'print'])->name('schedule.print');
        Route::prefix('classes')->name('classes.')->group(function () {
            Route::get('/', [TeacherClassController::class, 'index'])->name('index');
            Route::get('/{classSchedule}/grades', [TeacherClassController::class, 'manageGrades'])->name('grades');
            Route::post('/{classSchedule}/process-import', [TeacherClassController::class, 'processImport'])->name('process-import');
            Route::get('/{classSchedule}/import-summary-preview', [TeacherClassController::class, 'importSummaryPreview'])->name('import-summary-preview');
            Route::post('/{classSchedule}/confirm-import-summary', [TeacherClassController::class, 'confirmImportSummary'])->name('confirm-import-summary');
            Route::post('/{classSchedule}/cancel-import-summary', [TeacherClassController::class, 'cancelImportSummary'])->name('cancel-import-summary');
            Route::post('/{classSchedule}/save-grades', [TeacherClassController::class, 'saveGrades'])->name('save-grades');
            Route::get('/{classSchedule}/grades-save-confirmation', [TeacherClassController::class, 'saveConfirmation'])->name('grades-save-confirmation');
            Route::post('/{classSchedule}/submit-grades', [TeacherClassController::class, 'submitGrades'])->name('submit-grades');
            Route::get('/{classSchedule}/students', [TeacherClassController::class, 'showStudents'])->name('students');
        });
        Route::prefix('grades')->name('grades.')->group(function () {
            Route::get('/', [TeacherGradeManagementController::class, 'index'])->name('index');
            Route::get('/select', [TeacherGradeManagementController::class, 'select'])->name('select');
            Route::get('/sections', [TeacherGradeManagementController::class, 'sections'])->name('sections');
            Route::get('/subjects', [TeacherGradeManagementController::class, 'subjects'])->name('subjects');
            Route::get('/{classSchedule}/upload', [TeacherGradeManagementController::class, 'upload'])->name('upload');
            Route::get('/{classSchedule}/import-preview', [TeacherGradeManagementController::class, 'importPreview'])->name('import-preview');
            Route::post('/process-upload', [TeacherGradeManagementController::class, 'processUpload'])->name('process-upload');
            Route::post('/submit-grades', [TeacherGradeManagementController::class, 'submitGrades'])->name('submit-grades');
            // Old routes (preserved for reference, will be removed in Phase 2)
            // Route::get('/{classSchedule}', [TeacherGradeManagementController::class, 'indexOld'])->name('index-old');
            Route::post('/{classSchedule}/save-manual', [TeacherGradeManagementController::class, 'saveManual'])->name('save-manual');
            Route::post('/{classSchedule}/import', [TeacherGradeManagementController::class, 'import'])->name('import');
            Route::post('/{classSchedule}/update-score', [TeacherGradeManagementController::class, 'updateScore'])->name('update-score');
            Route::post('/{classSchedule}/compute', [TeacherGradeManagementController::class, 'computeGrades'])->name('compute');
            Route::post('/{classSchedule}/submit', [TeacherGradeSubmissionController::class, 'submit'])->name('submit');
            Route::post('/{classSchedule}/resubmit', [TeacherGradeSubmissionController::class, 'resubmit'])->name('resubmit');
        });
        Route::prefix('announcements')->name('announcements.')->group(function () {
            Route::get('/', [TeacherAnnouncementController::class, 'index'])->name('index');
        });
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [TeacherProfileController::class, 'index'])->name('index');
            Route::put('/', [TeacherProfileController::class, 'update'])->name('update');
            Route::put('/password', [TeacherProfileController::class, 'updatePassword'])->name('password');
        });
    });

    Route::prefix('ssg')->name('ssg.')->middleware('role:SSG')->group(function () {
        Route::get('/dashboard', [SsgDashboardController::class, 'index'])->name('dashboard');
        Route::get('/attendance/{event}', [SsgAttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance/scan', [SsgAttendanceController::class, 'scan'])->name('attendance.scan');
        Route::get('/attendance/{event}/list', [SsgAttendanceController::class, 'list'])->name('attendance.list');
        Route::post('/attendance/{event}/extend-time', [SsgAttendanceController::class, 'extendTime'])->name('attendance.extend-time');
        Route::get('/fines', [SsgFineController::class, 'index'])->name('fines.index');
        Route::get('/fines/{student}', [SsgFineController::class, 'show'])->name('fines.show');
        Route::resource('events', SsgEventController::class);
        Route::get('/events/{event}/status', [SsgEventController::class, 'getStatus'])->name('events.status');
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [SsgProfileController::class, 'index'])->name('index');
            Route::put('/', [SsgProfileController::class, 'update'])->name('update');
            Route::put('/password', [SsgProfileController::class, 'updatePassword'])->name('password');
        });
    });

    Route::prefix('student')->name('student.')->middleware('role:Student')->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [StudentProfileController::class, 'index'])->name('profile.index');
        Route::post('/profile/update-contact', [StudentProfileController::class, 'updateContact'])->name('profile.update-contact');
        Route::post('/profile/update-password', [StudentProfileController::class, 'updatePassword'])->name('profile.update-password');
        Route::get('/study-load', [StudentStudyLoadController::class, 'index'])->name('study-load.index');
        Route::get('/study-load/print', [StudentStudyLoadController::class, 'print'])->name('study-load.print');
        Route::get('/grades', [GradeViewController::class, 'index'])->name('grades.index');
        Route::get('/announcements', [StudentAnnouncementController::class, 'index'])->name('announcements.index');
        Route::get('/school-calendar', [StudentSchoolCalendarController::class, 'index'])->name('school-calendar.index');
        Route::get('/ssg-events', [StudentSSGEventController::class, 'index'])->name('ssg-events.index');
    });
});

require __DIR__.'/auth.php';
