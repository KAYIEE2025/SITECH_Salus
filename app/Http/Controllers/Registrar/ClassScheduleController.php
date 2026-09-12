<?php

namespace App\Http\Controllers\Registrar;

use App\Helpers\SchoolYearHelper;
use App\Http\Controllers\Controller;
use App\Models\ClassSchedule;
use App\Models\Subject;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;

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
            'time_end'   => 'required',
            'school_year'=> 'required|string',
        ]);

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
        $schedule->delete();
        return back()->with('success', 'Class schedule deleted.');
    }
}