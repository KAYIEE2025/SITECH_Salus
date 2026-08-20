<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\FinalGrade;
use App\Models\StudyLoad;
use App\Models\SsgEventAttendance;
use App\Models\SsgEvent;
use App\Models\Announcement;
use App\Models\AnnouncementView;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $student = Student::where('user_id', auth()->id())->first();

        if (!$student) {
            return view('student.dashboard', [
                'student' => null,
                'totalSubjects' => 0,
                'approvedGrades' => 0,
                'outstandingFineBalance' => 0,
                'upcomingSsgEvents' => collect(),
                'latestAnnouncement' => null,
            ]);
        }

        // Total Subjects Enrolled
        $totalSubjects = StudyLoad::where('student_id', $student->id)->count();

        // Approved Grades
        $approvedGrades = FinalGrade::where('student_id', $student->id)
            ->where('status', 'approved')->count();

        // Total Outstanding Fine Balance
        $outstandingFineBalance = $student->ssgAttendances()
            ->where('is_present', false)
            ->sum('actual_fine');

        // Upcoming SSG Events
        $upcomingSsgEvents = SsgEvent::where('event_date', '>=', Carbon::now())
            ->orderBy('event_date')
            ->take(5)
            ->get();

        // Get the newest announcement for popup modal (only unseen announcements)
        $seenAnnouncementIds = AnnouncementView::where('user_id', auth()->id())
            ->pluck('announcement_id')
            ->toArray();

        $latestAnnouncement = Announcement::with('poster')
            ->where('is_active', true)
            ->whereNotIn('id', $seenAnnouncementIds)
            ->where(function ($q) use ($student) {
                // Show announcements targeted to all users
                $q->where('target_type', 'all')
                  // Show announcements targeted to student's year level
                  ->orWhere(function ($subQuery) use ($student) {
                      $subQuery->where('target_type', 'year_level')
                               ->where('target_id', $student->year_level_id);
                  })
                  // Show announcements targeted to student's section
                  ->orWhere(function ($subQuery) use ($student) {
                      $subQuery->where('target_type', 'section')
                               ->where('target_id', $student->section_id);
                  });
            })
            ->orderByDesc('created_at')
            ->first();

        return view('student.dashboard', compact(
            'student',
            'totalSubjects',
            'approvedGrades',
            'outstandingFineBalance',
            'upcomingSsgEvents',
            'latestAnnouncement'
        ));
    }
}