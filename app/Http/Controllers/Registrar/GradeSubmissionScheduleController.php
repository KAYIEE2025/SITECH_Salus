<?php

namespace App\Http\Controllers\Registrar;

use App\Helpers\SchoolYearHelper;
use App\Http\Controllers\Controller;
use App\Models\GradeSubmissionSchedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class GradeSubmissionScheduleController extends Controller
{

    public function index(Request $request)
    {
        // Get available school years from database
        $availableSchoolYears = GradeSubmissionSchedule::select('school_year')
            ->distinct()
            ->orderBy('school_year', 'desc')
            ->pluck('school_year')
            ->toArray();

        // If no school years exist yet, use active school year as default
        if (empty($availableSchoolYears)) {
            $availableSchoolYears[] = SchoolYearHelper::getActiveSchoolYear();
        }

        // Get selected school year from query parameter or use active school year
        $selectedSchoolYear = $request->query('school_year', SchoolYearHelper::getActiveSchoolYear());

        // Ensure selected school year is in available school years
        if (!in_array($selectedSchoolYear, $availableSchoolYears)) {
            $selectedSchoolYear = reset($availableSchoolYears);
        }

        // Filter schedules by selected school year
        $schedules = GradeSubmissionSchedule::where('school_year', $selectedSchoolYear)
            ->orderBy('grading_period')
            ->get();

        return view('registrar.grade-submission-schedules.index', compact(
            'schedules',
            'availableSchoolYears',
            'selectedSchoolYear'
        ));
    }

    public function create(Request $request)
    {
        // Use selected school year from query parameter or default to active school year
        $activeSchoolYear = $request->query('school_year', SchoolYearHelper::getActiveSchoolYear());
        return view('registrar.grade-submission-schedules.create', compact('activeSchoolYear'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'school_year' => 'required|string|max:20',
            'grading_period' => 'required|integer|in:1,2,3,4',
            'start_date' => 'required|date',
            'start_time' => 'required',
            'deadline_date' => 'required|date',
            'deadline_time' => 'required',
        ]);

        $startAt = $request->start_date . ' ' . $request->start_time;
        $deadlineAt = $request->deadline_date . ' ' . $request->deadline_time;

        if (Carbon::parse($deadlineAt)->lte(Carbon::parse($startAt))) {
            return back()->with('error', 'Deadline must be after start date and time.');
        }

        $exists = GradeSubmissionSchedule::where('school_year', $request->school_year)
            ->where('grading_period', $request->grading_period)
            ->exists();

        if ($exists) {
            return back()->with('error', 'A schedule for this school year and grading period already exists.');
        }

        $schedule = GradeSubmissionSchedule::create([
            'school_year' => $request->school_year,
            'grading_period' => $request->grading_period,
            'start_at' => $startAt,
            'end_at' => $deadlineAt,
            'created_by' => auth()->id(),
        ]);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($schedule)
            ->log('Schedule Created: ' . $schedule->school_year . ' - Term ' . $schedule->grading_period);

        return redirect()->route('registrar.grade-submission-schedules.index', ['school_year' => $schedule->school_year])
            ->with('success', 'Grade submission schedule created successfully.');
    }

    public function edit(Request $request, GradeSubmissionSchedule $schedule)
    {
        $activeSchoolYear = $request->query('school_year', SchoolYearHelper::getActiveSchoolYear());
        return view('registrar.grade-submission-schedules.edit', compact('schedule', 'activeSchoolYear'));
    }

    public function update(Request $request, GradeSubmissionSchedule $schedule)
    {
        $request->validate([
            'school_year' => 'required|string|max:20',
            'grading_period' => 'required|integer|in:1,2,3,4',
            'start_date' => 'required|date',
            'start_time' => 'required',
            'deadline_date' => 'required|date',
            'deadline_time' => 'required',
        ]);

        $startAt = $request->start_date . ' ' . $request->start_time;
        $deadlineAt = $request->deadline_date . ' ' . $request->deadline_time;

        if (Carbon::parse($deadlineAt)->lte(Carbon::parse($startAt))) {
            return back()->with('error', 'Deadline must be after start date and time.');
        }

        $exists = GradeSubmissionSchedule::where('school_year', $request->school_year)
            ->where('grading_period', $request->grading_period)
            ->where('id', '!=', $schedule->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'A schedule for this school year and grading period already exists.');
        }

        $schedule->update([
            'school_year' => $request->school_year,
            'grading_period' => $request->grading_period,
            'start_at' => $startAt,
            'end_at' => $deadlineAt,
        ]);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($schedule)
            ->log('Schedule Updated: ' . $schedule->school_year . ' - Term ' . $schedule->grading_period);

        return redirect()->route('registrar.grade-submission-schedules.index', ['school_year' => $schedule->school_year])
            ->with('success', 'Grade submission schedule updated successfully.');
    }

    public function destroy(GradeSubmissionSchedule $schedule)
    {
        $scheduleInfo = $schedule->school_year . ' - Term ' . $schedule->grading_period;
        $schoolYear = $schedule->school_year;
        
        $schedule->delete();

        activity()
            ->causedBy(auth()->user())
            ->log('Schedule Deleted: ' . $scheduleInfo);

        return redirect()->route('registrar.grade-submission-schedules.index', ['school_year' => $schoolYear])
            ->with('success', 'Grade submission schedule deleted successfully.');
    }
}
