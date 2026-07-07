<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\FinalGrade;
use App\Models\StudyLoad;
use App\Models\SsgEventAttendance;
use App\Models\SsgEvent;
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

        return view('student.dashboard', compact(
            'student',
            'totalSubjects',
            'approvedGrades',
            'outstandingFineBalance',
            'upcomingSsgEvents'
        ));
    }
}