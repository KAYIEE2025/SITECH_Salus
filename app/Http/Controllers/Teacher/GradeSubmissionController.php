<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassSchedule;
use App\Models\FinalGrade;
use App\Models\GradeSubmissionSchedule;
use Illuminate\Http\Request;

class GradeSubmissionController extends Controller
{
    public function submit(ClassSchedule $classSchedule)
    {
        if ($classSchedule->teacher_id !== auth()->id()) {
            abort(403);
        }

        // Check if all students have grades
        $grades = FinalGrade::where('class_schedule_id', $classSchedule->id)->get();

        if ($grades->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No grades to submit. Please compute grades first.']);
        }

        // Check if any grades are null
        if ($grades->where('final_grade', null)->isNotEmpty()) {
            return response()->json(['success' => false, 'message' => 'Some students have incomplete grades. Please complete all grades before submitting.']);
        }

        // Update status to submitted
        FinalGrade::where('class_schedule_id', $classSchedule->id)
            ->where('status', 'draft')
            ->update([
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

        return response()->json(['success' => true, 'message' => 'Grades submitted successfully. Waiting for Registrar approval.']);
    }

    public function resubmit(ClassSchedule $classSchedule)
    {
        if ($classSchedule->teacher_id !== auth()->id()) {
            abort(403);
        }

        // Only allow resubmission if grades were rejected
        $rejectedGrades = FinalGrade::where('class_schedule_id', $classSchedule->id)
            ->where('status', 'rejected')
            ->exists();

        if (!$rejectedGrades) {
            return redirect()
                ->route('teacher.grades.index', $classSchedule)
                ->with('error', 'Only rejected grades can be resubmitted.');
        }

        // PHASE 2: Validate submission timeframe for resubmission
        $schoolYear = $classSchedule->school_year;
        $currentTime = now();

        // Determine grading period from rejected grades
        $rejectedGrade = FinalGrade::where('class_schedule_id', $classSchedule->id)
            ->where('status', 'rejected')
            ->first();

        $gradingPeriod = $rejectedGrade->grading_period;

        if (!$gradingPeriod || !in_array($gradingPeriod, [1, 2, 3])) {
            return redirect()
                ->route('teacher.grades.index', $classSchedule)
                ->with('error', 'Unable to determine grading period for resubmission.');
        }

        // Find the matching GradeSubmissionSchedule
        $schedule = GradeSubmissionSchedule::where('school_year', $schoolYear)
            ->where('grading_period', $gradingPeriod)
            ->first();

        if (!$schedule) {
            activity()
                ->causedBy(auth()->user())
                ->withProperties([
                    'class_schedule_id' => $classSchedule->id,
                    'school_year' => $schoolYear,
                    'grading_period' => $gradingPeriod,
                    'current_timestamp' => $currentTime->toDateTimeString(),
                    'result' => 'BLOCKED',
                    'reason' => 'NO SCHEDULE',
                ])
                ->log('grade_resubmission_blocked_no_schedule');

            return redirect()
                ->route('teacher.grades.index', $classSchedule)
                ->with('error', 'Grade resubmission is currently unavailable because no submission period has been configured.');
        }

        // Check if current time is before start_at
        if ($currentTime->lt($schedule->start_at)) {
            activity()
                ->causedBy(auth()->user())
                ->withProperties([
                    'class_schedule_id' => $classSchedule->id,
                    'school_year' => $schoolYear,
                    'grading_period' => $gradingPeriod,
                    'current_timestamp' => $currentTime->toDateTimeString(),
                    'schedule_id' => $schedule->id,
                    'start_at' => $schedule->start_at->toDateTimeString(),
                    'end_at' => $schedule->end_at->toDateTimeString(),
                    'result' => 'BLOCKED',
                    'reason' => 'NOT YET OPEN',
                ])
                ->log('grade_resubmission_blocked_not_yet_open');

            return redirect()
                ->route('teacher.grades.index', $classSchedule)
                ->with('error', 'Grade resubmission is not yet open. Submission opens on ' . $schedule->start_at->format('M d, Y g:i A') . '.');
        }

        // Check if current time is after end_at
        if ($currentTime->gt($schedule->end_at)) {
            activity()
                ->causedBy(auth()->user())
                ->withProperties([
                    'class_schedule_id' => $classSchedule->id,
                    'school_year' => $schoolYear,
                    'grading_period' => $gradingPeriod,
                    'current_timestamp' => $currentTime->toDateTimeString(),
                    'schedule_id' => $schedule->id,
                    'start_at' => $schedule->start_at->toDateTimeString(),
                    'end_at' => $schedule->end_at->toDateTimeString(),
                    'result' => 'BLOCKED',
                    'reason' => 'DEADLINE PASSED',
                ])
                ->log('grade_resubmission_blocked_deadline_passed');

            return redirect()
                ->route('teacher.grades.index', $classSchedule)
                ->with('error', 'Grade resubmission is closed. The submission deadline was ' . $schedule->end_at->format('M d, Y g:i A') . '.');
        }

        // Timeframe is valid - log and continue
        activity()
            ->causedBy(auth()->user())
            ->withProperties([
                'class_schedule_id' => $classSchedule->id,
                'school_year' => $schoolYear,
                'grading_period' => $gradingPeriod,
                'current_timestamp' => $currentTime->toDateTimeString(),
                'schedule_id' => $schedule->id,
                'start_at' => $schedule->start_at->toDateTimeString(),
                'end_at' => $schedule->end_at->toDateTimeString(),
                'result' => 'ALLOWED',
            ])
            ->log('grade_resubmission_allowed');

        // Update status back to submitted
        FinalGrade::where('class_schedule_id', $classSchedule->id)
            ->where('status', 'rejected')
            ->update([
                'status' => 'submitted',
                'submitted_at' => now(),
                'rejection_reason' => null,
            ]);

        return redirect()
            ->route('teacher.grades.index', $classSchedule)
            ->with('success', 'Grades resubmitted successfully.');
    }
}
