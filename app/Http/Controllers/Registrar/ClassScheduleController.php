<?php

namespace App\Http\Controllers\Registrar;

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
        $sections  = Section::with(['course', 'yearLevel'])->get();
        $teachers  = User::role('Teacher')->get();

        return view('registrar.schedules.index', compact('schedules', 'subjects', 'sections', 'teachers'));
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
            'semester'   => 'required|in:1st,2nd,Summer',
            'school_year'=> 'required|string',
        ]);

        $schedule = ClassSchedule::create($request->all());

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