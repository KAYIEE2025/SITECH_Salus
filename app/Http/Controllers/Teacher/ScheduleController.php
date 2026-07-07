<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassSchedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        [$teacher, $schoolYear, $semester, $schoolYears, $semesters, $schedules] = $this->scheduleData($request);

        return view('teacher.schedule.index', compact(
            'teacher',
            'schoolYear',
            'semester',
            'schoolYears',
            'semesters',
            'schedules'
        ));
    }

    public function print(Request $request)
    {
        [$teacher, $schoolYear, $semester, , , $schedules] = $this->scheduleData($request);

        $schoolLogo = asset('images/salus-logo.png');
        $dateGenerated = now()->format('F d, Y g:i A');

        return view('teacher.schedule.print', compact(
            'teacher',
            'schoolYear',
            'semester',
            'schedules',
            'schoolLogo',
            'dateGenerated'
        ));
    }

    private function scheduleData(Request $request): array
    {
        $teacher = auth()->user();

        $baseQuery = ClassSchedule::query()
            ->where('teacher_id', auth()->id())
            ->where('is_active', true);

        $schoolYears = (clone $baseQuery)
            ->select('school_year')
            ->distinct()
            ->orderByDesc('school_year')
            ->pluck('school_year');

        $semesters = (clone $baseQuery)
            ->select('semester')
            ->distinct()
            ->orderBy('semester')
            ->pluck('semester');

        $latestSchedule = (clone $baseQuery)
            ->orderByDesc('school_year')
            ->orderBy('semester')
            ->first();

        $schoolYear = $request->query('school_year', $latestSchedule?->school_year);
        $semester = $request->query('semester', $latestSchedule?->semester);

        $schedules = (clone $baseQuery)
            ->with(['subject:id,code,name', 'section.yearLevel:id,level,name'])
            ->when($schoolYear, fn ($query) => $query->where('school_year', $schoolYear))
            ->when($semester, fn ($query) => $query->where('semester', $semester))
            ->orderBy('time_start')
            ->get();

        return [$teacher, $schoolYear, $semester, $schoolYears, $semesters, $schedules];
    }
}
