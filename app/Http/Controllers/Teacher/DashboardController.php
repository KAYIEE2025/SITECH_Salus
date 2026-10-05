<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AnnouncementView;
use App\Models\ClassSchedule;
use App\Models\FinalGrade;
use App\Models\StudyLoad;
use App\Models\Section;
use App\Models\Student;

class DashboardController extends Controller
{
    public function index()
    {
        $teacherId = auth()->id();

        // Total assigned classes
        $totalClasses = ClassSchedule::where('teacher_id', $teacherId)->count();

        // Total assigned students (unique students across all classes + advisory sections)
        $classIds = ClassSchedule::where('teacher_id', $teacherId)->pluck('id');
        $assignedStudentIds = StudyLoad::whereIn('class_schedule_id', $classIds)
            ->pluck('student_id')
            ->unique()
            ->toArray();

        // Advisory section students
        $advisorySectionIds = Section::where('adviser_id', $teacherId)->pluck('id');
        $advisoryStudentIds = Student::whereIn('section_id', $advisorySectionIds)
            ->pluck('id')
            ->unique()
            ->toArray();

        // Combine and deduplicate
        $allStudentIds = array_unique(array_merge($assignedStudentIds, $advisoryStudentIds));
        $totalStudents = count($allStudentIds);

        // Pending grade submissions (draft status)
        $pendingSubmissions = FinalGrade::whereIn('class_schedule_id', $classIds)
            ->where('status', 'draft')
            ->distinct('class_schedule_id')
            ->count('class_schedule_id');

        // Get the newest announcement for popup modal (only unseen announcements)
        $seenAnnouncementIds = AnnouncementView::where('user_id', auth()->id())
            ->pluck('announcement_id')
            ->toArray();

        $latestAnnouncement = Announcement::where('is_active', true)
            ->whereNotIn('id', $seenAnnouncementIds)
            ->latest()
            ->first();

        return view('teacher.dashboard', compact(
            'totalClasses',
            'totalStudents',
            'pendingSubmissions',
            'latestAnnouncement'
        ));
    }
}