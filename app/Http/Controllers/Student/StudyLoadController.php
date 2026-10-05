<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudyLoad;
use Illuminate\Http\Request;

class StudyLoadController extends Controller
{
    public function index(Request $request)
    {
        $student = Student::where('user_id', auth()->id())->first();

        if (!$student) {
            return view('student.study-load', [
                'student' => null,
                'schedules' => collect(),
                'schoolYears' => collect(),
                'terms' => ['Term 1', 'Term 2', 'Term 3'],
            ]);
        }

        // Get unique school years from student's study loads
        $schoolYears = StudyLoad::where('student_id', $student->id)
            ->select('school_year')
            ->distinct()
            ->orderBy('school_year', 'desc')
            ->pluck('school_year');

        $terms = ['Term 1', 'Term 2', 'Term 3'];

        // Get student's study loads with class schedule relationships
        $query = StudyLoad::where('student_id', $student->id)
            ->with('classSchedule.subject', 'classSchedule.teacher')
            ->when($request->filled('school_year'), fn ($q) =>
                $q->where('school_year', $request->school_year)
            )
            ->when($request->filled('term'), fn ($q) =>
                $q->where('term', $request->term)
            );

        $studyLoads = $query->get();

        // Extract class schedules and sort by day and time
        $schedules = $studyLoads->pluck('classSchedule')
            ->filter()
            ->sortBy(function ($schedule) {
                $dayOrder = ['Monday' => 1, 'Tuesday' => 2, 'Wednesday' => 3, 'Thursday' => 4, 'Friday' => 5, 'Saturday' => 6, 'Sunday' => 7];
                $firstDay = $schedule->days[0] ?? 'Monday';
                $dayPriority = $dayOrder[$firstDay] ?? 8;
                $timePriority = $schedule->time_start ?? '00:00:00';
                return $dayPriority . '-' . $timePriority;
            })
            ->values();

        return view('student.study-load', compact('student', 'schedules', 'schoolYears', 'terms'));
    }

    public function print()
    {
        // Only Registrar users can access print functionality
        if (!auth()->user()->hasRole('registrar')) {
            abort(403, 'Unauthorized access. Only Registrar users can print Study Load.');
        }

        $student = Student::where('user_id', auth()->id())->first();

        if (!$student) {
            return back()->with('error', 'Student profile not found.');
        }

        // Get student's study loads with class schedule relationships
        $studyLoads = StudyLoad::where('student_id', $student->id)
            ->with('classSchedule.subject', 'classSchedule.teacher')
            ->get();

        // Extract class schedules and sort by day and time
        $schedules = $studyLoads->pluck('classSchedule')
            ->filter()
            ->sortBy(function ($schedule) {
                $dayOrder = ['Monday' => 1, 'Tuesday' => 2, 'Wednesday' => 3, 'Thursday' => 4, 'Friday' => 5, 'Saturday' => 6, 'Sunday' => 7];
                $firstDay = $schedule->days[0] ?? 'Monday';
                $dayPriority = $dayOrder[$firstDay] ?? 8;
                $timePriority = $schedule->time_start ?? '00:00:00';
                return $dayPriority . '-' . $timePriority;
            })
            ->values();

        $schoolLogo = asset('images/salus-logo.png');
        $dateGenerated = now()->format('F d, Y - g:i A');

        return view('student.study-load-print', compact(
            'student',
            'schedules',
            'schoolLogo',
            'dateGenerated'
        ));
    }
}
