<?php

namespace App\Http\Controllers\Registrar;

use App\Helpers\SchoolYearHelper;
use App\Http\Controllers\Controller;
use App\Models\ClassSchedule;
use App\Models\Subject;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ClassScheduleController extends Controller
{

    public function index()
    {
        $schedules = ClassSchedule::with(['subject', 'teacher', 'section'])
            ->latest()->paginate(15);
        $subjects  = Subject::where('is_active', true)->get();
        $sections  = Section::with('yearLevel')->get();
        $teachers  = User::role('Teacher')->get();
        $activeSchoolYear = SchoolYearHelper::getActiveSchoolYear();

        return view('registrar.schedules.index', compact('schedules', 'subjects', 'sections', 'teachers', 'activeSchoolYear'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'section_id' => 'required|exists:sections,id',
            'room'       => 'required|string|max:50',
            'days'       => 'required|array|min:1',
            'time_start' => 'required',
            'time_end'   => 'required|after:time_start',
            'school_year'=> 'required|string',
        ]);

        // ── Conflict: Teacher already has a class at this time/day ──
        $teacherConflict = ClassSchedule::where('teacher_id', $request->teacher_id)
            ->where('school_year', $request->school_year)
            ->where(function ($q) use ($request) {
                $q->where('time_start', '<', $request->time_end)
                  ->where('time_end', '>', $request->time_start);
            })
            ->get()
            ->filter(function ($schedule) use ($request) {
                return !empty(array_intersect($schedule->days, $request->days));
            });

        if ($teacherConflict->isNotEmpty()) {
            $conflict = $teacherConflict->first();
            $conflictDays = implode(', ', $conflict->days ?? []);
            $conflictTime = Carbon::parse($conflict->time_start)->format('g:i A') . ' - ' . Carbon::parse($conflict->time_end)->format('g:i A');
            $conflictDetails = $conflict->subject->name ?? 'Unknown subject';
            
            return back()->withInput()
                ->with('error', "Teacher conflict: The selected teacher already has a schedule for {$conflictDetails} on {$conflictDays} from {$conflictTime} in the same school year.");
        }

        // ── Conflict: Room already taken ──
        $roomConflict = ClassSchedule::where('room', $request->room)
            ->where('school_year', $request->school_year)
            ->where(function ($q) use ($request) {
                $q->where('time_start', '<', $request->time_end)
                  ->where('time_end', '>', $request->time_start);
            })
            ->get()
            ->filter(function ($schedule) use ($request) {
                return !empty(array_intersect($schedule->days, $request->days));
            });

        if ($roomConflict->isNotEmpty()) {
            $conflict = $roomConflict->first();
            $conflictDays = implode(', ', $conflict->days ?? []);
            $conflictTime = Carbon::parse($conflict->time_start)->format('g:i A') . ' - ' . Carbon::parse($conflict->time_end)->format('g:i A');
            $conflictDetails = $conflict->subject->name ?? 'Unknown subject';
            
            return back()->withInput()
                ->with('error', "Room conflict: Room {$request->room} is already assigned to {$conflictDetails} on {$conflictDays} from {$conflictTime} in the same school year.");
        }

        $schedule = ClassSchedule::create($request->only([
            'subject_id',
            'teacher_id',
            'section_id',
            'room',
            'days',
            'time_start',
            'time_end',
            'school_year',
        ]));

        activity()
            ->causedBy(auth()->user())
            ->performedOn($schedule)
            ->log('Created class schedule for ' . $schedule->subject->name);

        return back()->with('success', 'Class schedule created successfully.');
    }

    public function destroy(ClassSchedule $schedule)
    {
        // Capture information before deletion for logging
        $subjectName = $schedule->subject->name ?? 'Unknown';
        $sectionName = $schedule->section->name ?? 'Unknown';
        $teacherName = $schedule->teacher->name ?? 'Unknown';
        $schoolYear = $schedule->school_year;

        $schedule->delete();

        activity()
            ->causedBy(auth()->user())
            ->log('Deleted class schedule: ' . $subjectName . ' taught by ' . $teacherName . ' in ' . $sectionName . ' (' . $schoolYear . ')');

        return back()->with('success', 'Class schedule deleted.');
    }
}