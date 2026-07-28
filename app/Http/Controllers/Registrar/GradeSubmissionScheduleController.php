<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\GradeSubmissionSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradeSubmissionScheduleController extends Controller
{
    public function index()
    {
        $schedules = GradeSubmissionSchedule::with('creator')
            ->orderBy('school_year', 'desc')
            ->orderBy('grading_period')
            ->get();

        return view('registrar.grade-submission-schedules.index', compact('schedules'));
    }

    public function create()
    {
        return view('registrar.grade-submission-schedules.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'school_year' => 'required|string|max:20',
            'grading_period' => 'required|integer|in:1,2,3',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
        ]);

        $exists = GradeSubmissionSchedule::where('school_year', $validated['school_year'])
            ->where('grading_period', $validated['grading_period'])
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', 'A schedule already exists for this school year and grading period.');
        }

        $schedule = GradeSubmissionSchedule::create([
            'school_year' => $validated['school_year'],
            'grading_period' => $validated['grading_period'],
            'start_at' => $validated['start_at'],
            'end_at' => $validated['end_at'],
            'created_by' => Auth::id(),
        ]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($schedule)
            ->withProperties([
                'school_year' => $schedule->school_year,
                'grading_period' => $schedule->grading_period,
                'start_at' => $schedule->start_at->toDateTimeString(),
                'end_at' => $schedule->end_at->toDateTimeString(),
            ])
            ->log('grade_submission_schedule_created');

        return redirect()
            ->route('registrar.grade-submission-schedules.index')
            ->with('success', 'Grade submission schedule created successfully.');
    }

    public function edit(GradeSubmissionSchedule $schedule)
    {
        return view('registrar.grade-submission-schedules.edit', compact('schedule'));
    }

    public function update(Request $request, GradeSubmissionSchedule $schedule)
    {
        $validated = $request->validate([
            'school_year' => 'required|string|max:20',
            'grading_period' => 'required|integer|in:1,2,3',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
        ]);

        $exists = GradeSubmissionSchedule::where('school_year', $validated['school_year'])
            ->where('grading_period', $validated['grading_period'])
            ->where('id', '!=', $schedule->id)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', 'A schedule already exists for this school year and grading period.');
        }

        $oldValues = [
            'school_year' => $schedule->school_year,
            'grading_period' => $schedule->grading_period,
            'start_at' => $schedule->start_at->toDateTimeString(),
            'end_at' => $schedule->end_at->toDateTimeString(),
        ];

        $schedule->update($validated);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($schedule)
            ->withProperties([
                'school_year' => $schedule->school_year,
                'grading_period' => $schedule->grading_period,
                'start_at' => $schedule->start_at->toDateTimeString(),
                'end_at' => $schedule->end_at->toDateTimeString(),
                'old_values' => $oldValues,
            ])
            ->log('grade_submission_schedule_updated');

        return redirect()
            ->route('registrar.grade-submission-schedules.index')
            ->with('success', 'Grade submission schedule updated successfully.');
    }

    public function destroy(GradeSubmissionSchedule $schedule)
    {
        $scheduleData = [
            'school_year' => $schedule->school_year,
            'grading_period' => $schedule->grading_period,
            'start_at' => $schedule->start_at->toDateTimeString(),
            'end_at' => $schedule->end_at->toDateTimeString(),
        ];

        $schedule->delete();

        activity()
            ->causedBy(Auth::user())
            ->withProperties($scheduleData)
            ->log('grade_submission_schedule_deleted');

        return redirect()
            ->route('registrar.grade-submission-schedules.index')
            ->with('success', 'Grade submission schedule deleted successfully.');
    }
}
