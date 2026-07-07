<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\ClassSchedule;
use App\Models\StudyLoad;
use App\Models\Section;
use App\Models\Subject;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;

class StudyLoadController extends Controller
{
    public function index()
{
    $sections   = Section::with('yearLevel')->get();
    $teachers   = User::role('Teacher')->get();
    $selected   = null;
    $schedules  = collect();

    if (request('section_id')) {
        $selected  = Section::with('yearLevel')->find(request('section_id'));
        $schedules = ClassSchedule::with(['subject', 'teacher'])
            ->where('section_id', request('section_id'))
            ->where('school_year', request('school_year', '2025-2026'))
            ->where('semester', request('semester', '1st'))
            ->orderBy('time_start')
            ->get();
    }

    return view('registrar.study-load.index', compact(
        'sections', 'teachers', 'selected', 'schedules'
    ));
}

public function store(Request $request)
{
    $request->validate([
        'section_id'   => 'required|exists:sections,id',
        'subject_code' => 'required|string|max:20',
        'subject_name' => 'required|string|max:100',
        'teacher_id'   => 'required|exists:users,id',
        'room'         => 'required|string|max:50',
        'days'         => 'required|array|min:1',
        'time_start'   => 'required',
        'time_end'     => 'required',
        'date_start'   => 'nullable|date',
        'date_end'     => 'nullable|date|after_or_equal:date_start',
        'semester'     => 'required|in:1st,2nd,Summer',
        'school_year'  => 'required|string',
    ]);

    // Find or create subject based on typed code and name
    $subject = \App\Models\Subject::firstOrCreate(
        ['code' => strtoupper(trim($request->subject_code))],
        ['name' => $request->subject_name, 'units' => 3]
    );

    // ── Conflict: Teacher already has a class at this time/day ──
    $teacherConflict = ClassSchedule::where('teacher_id', $request->teacher_id)
        ->where('school_year', $request->school_year)
        ->where('semester', $request->semester)
        ->where(function ($q) use ($request) {
            $q->where('time_start', '<', $request->time_end)
              ->where('time_end', '>', $request->time_start);
        })
        ->get()
        ->filter(function ($schedule) use ($request) {
            return !empty(array_intersect($schedule->days, $request->days));
        });

    if ($teacherConflict->isNotEmpty()) {
        return back()->withInput()
            ->with('error', 'Teacher conflict: This teacher already has a class at this day and time.');
    }

    // ── Conflict: Room already taken ──
    $roomConflict = ClassSchedule::where('room', $request->room)
        ->where('school_year', $request->school_year)
        ->where('semester', $request->semester)
        ->where(function ($q) use ($request) {
            $q->where('time_start', '<', $request->time_end)
              ->where('time_end', '>', $request->time_start);
        })
        ->get()
        ->filter(function ($schedule) use ($request) {
            return !empty(array_intersect($schedule->days, $request->days));
        });

    if ($roomConflict->isNotEmpty()) {
        return back()->withInput()
            ->with('error', 'Room conflict: This room is already occupied at this day and time.');
    }

    // ── Create schedule ──
    $schedule = ClassSchedule::create([
        'section_id'  => $request->section_id,
        'subject_id'  => $subject->id,
        'teacher_id'  => $request->teacher_id,
        'room'        => $request->room,
        'days'        => $request->days,
        'time_start'  => $request->time_start,
        'time_end'    => $request->time_end,
        'date_start'  => $request->date_start,
        'date_end'    => $request->date_end,
        'semester'    => $request->semester,
        'school_year' => $request->school_year,
    ]);

    // ── Auto-assign to existing students in this section ──
    $students = Student::where('section_id', $request->section_id)->get();
    foreach ($students as $student) {
        StudyLoad::firstOrCreate([
            'student_id'        => $student->id,
            'class_schedule_id' => $schedule->id,
            'school_year'       => $request->school_year,
            'semester'          => $request->semester,
        ]);
    }

    activity()
        ->causedBy(auth()->user())
        ->log('Added ' . $subject->code . ' to study load of section.');

    return redirect()->route('registrar.study-load', [
        'section_id'  => $request->section_id,
        'school_year' => $request->school_year,
        'semester'    => $request->semester,
    ])->with('success', 'Subject added to study load successfully.');
}

    public function destroy(ClassSchedule $schedule)
    {
        $sectionId  = $schedule->section_id;
        $schoolYear = $schedule->school_year;
        $semester   = $schedule->semester;

        // Remove study loads for this schedule
        StudyLoad::where('class_schedule_id', $schedule->id)->delete();
        $schedule->delete();

        return redirect()->route('registrar.study-load', [
            'section_id'  => $sectionId,
            'school_year' => $schoolYear,
            'semester'    => $semester,
        ])->with('success', 'Subject removed from study load.');
    }
}