<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Helpers\SchoolYearHelper;
use App\Models\ClassSchedule;
use App\Models\StudyLoad;
use App\Models\Section;
use App\Models\Subject;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StudyLoadController extends Controller
{
    public function index(Request $request)
{
    $sections   = Section::with('yearLevel')->get();
    $teachers   = User::role('Teacher')->get();
    $selected   = null;
    $schedules  = collect();
    $activeSchoolYear = SchoolYearHelper::getActive();
    $students   = collect();

    if (request('section_id')) {
        $selected  = Section::with('yearLevel')->find(request('section_id'));
        $schedules = ClassSchedule::with(['subject', 'teacher'])
            ->where('section_id', request('section_id'))
            ->where('school_year', request('school_year', $activeSchoolYear ?? '2025-2026'))
            ->orderBy('time_start')
            ->get();
    }

    // Handle student search in Student Study Load tab
    if (request('view') === 'students') {
        $studentQuery = Student::with(['yearLevel', 'section']);

        if ($request->filled('student_search')) {
            $search = $request->student_search;
            $studentQuery->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $students = $studentQuery->orderBy('last_name')->orderBy('first_name')->paginate(15)->withQueryString();
    }

    return view('registrar.study-load.index', compact(
        'sections', 'teachers', 'selected', 'schedules', 'activeSchoolYear', 'students'
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
        'time_end'     => 'required|after:time_start',
        'date_start'   => 'nullable|date',
        'date_end'     => 'nullable|date|after_or_equal:date_start',
        'school_year'  => 'required|string',
    ]);

    // Find or create subject based on typed code and name
    $subject = \App\Models\Subject::firstOrCreate(
        ['code' => strtoupper(trim($request->subject_code))],
        ['name' => $request->subject_name, 'units' => 3]
    );

    // Log if subject was newly created
    if ($subject->wasRecentlyCreated) {
        activity()
            ->causedBy(auth()->user())
            ->performedOn($subject)
            ->log('Created subject: ' . $subject->code . ' - ' . $subject->name);
    }

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
        'school_year' => $request->school_year,
    ]);

    // ── Auto-assign to existing students in this section ──
    $students = Student::where('section_id', $request->section_id)->get();
    $assignedCount = 0;
    foreach ($students as $student) {
        $studyLoad = StudyLoad::firstOrCreate([
            'student_id'        => $student->id,
            'class_schedule_id' => $schedule->id,
            'school_year'       => $request->school_year,
        ]);
        if ($studyLoad->wasRecentlyCreated) {
            $assignedCount++;
        }
    }

    activity()
        ->causedBy(auth()->user())
        ->log('Added ' . $subject->code . ' (' . $subject->name . ') to study load of section. ' . $assignedCount . ' student(s) assigned.');

    return redirect()->route('registrar.study-load', [
        'section_id'  => $request->section_id,
        'school_year' => $request->school_year,
    ])->with('success', 'Subject added to study load successfully.');
}

    public function destroy(ClassSchedule $schedule)
    {
        $sectionId  = $schedule->section_id;
        $schoolYear = $schedule->school_year;
        
        // Capture information before deletion for logging
        $subjectCode = $schedule->subject->code ?? 'Unknown';
        $subjectName = $schedule->subject->name ?? 'Unknown';
        $sectionName = $schedule->section->name ?? 'Unknown';
        $studyLoadCount = StudyLoad::where('class_schedule_id', $schedule->id)->count();

        // Remove study loads for this schedule
        StudyLoad::where('class_schedule_id', $schedule->id)->delete();
        $schedule->delete();

        activity()
            ->causedBy(auth()->user())
            ->log('Removed ' . $subjectCode . ' (' . $subjectName . ') from study load of ' . $sectionName . ' section. ' . $studyLoadCount . ' student(s) affected.');

        return redirect()->route('registrar.study-load', [
            'section_id'  => $sectionId,
            'school_year' => $schoolYear,
        ])->with('success', 'Subject removed from study load.');
    }
}