<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\ClassSchedule;
use App\Models\FinalGrade;
use App\Models\StudyLoad;

class DashboardController extends Controller
{
    public function index()
    {
        $teacherId = auth()->id();

        // Total assigned classes
        $totalClasses = ClassSchedule::where('teacher_id', $teacherId)->count();

        // Total assigned students (unique students across all classes)
        $classIds = ClassSchedule::where('teacher_id', $teacherId)->pluck('id');
        $totalStudents = StudyLoad::whereIn('class_schedule_id', $classIds)
            ->distinct('student_id')
            ->count('student_id');

        // Pending grade submissions (draft status)
        $pendingSubmissions = FinalGrade::whereIn('class_schedule_id', $classIds)
            ->where('status', 'draft')
            ->distinct('class_schedule_id')
            ->count('class_schedule_id');

        // Latest announcements
        $announcements = Announcement::latest()->take(5)->get();

        return view('teacher.dashboard', compact(
            'totalClasses',
            'totalStudents',
            'pendingSubmissions',
            'announcements'
        ));
    }
}