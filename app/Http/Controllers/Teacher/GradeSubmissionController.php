<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassSchedule;
use App\Models\FinalGrade;
use App\Models\GradeSubmissionSchedule;
use App\Models\GradeSubmissionReopeningRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class GradeSubmissionController extends Controller
{
    public function submit(Request $request, ClassSchedule $classSchedule)
    {
        if ($classSchedule->teacher_id !== auth()->id()) {
            abort(403);
        }

        // Get grading period from request or default to Term 1
        $gradingPeriod = $request->input('grading_period', 1);

        // Check for any reopening request
        $reopeningRequest = GradeSubmissionReopeningRequest::forTeacher(auth()->id())
            ->forPeriod($classSchedule->school_year, $gradingPeriod)
            ->orderBy('created_at', 'desc')
            ->first();

        // Security: Cannot submit while request is Pending
        if ($reopeningRequest && $reopeningRequest->isPending()) {
            return response()->json(['success' => false, 'message' => 'You cannot submit grades while your reopening request is pending approval.']);
        }

        // Security: Cannot submit while request is Rejected
        if ($reopeningRequest && $reopeningRequest->isRejected()) {
            return response()->json(['success' => false, 'message' => 'You cannot submit grades because your reopening request was rejected.']);
        }

        // Check for approved reopening request with temporary access
        $hasTemporaryAccess = $reopeningRequest && $reopeningRequest->isApproved() && $reopeningRequest->hasTemporaryAccess();

        // Security: Cannot submit after temporary deadline expires
        if ($reopeningRequest && $reopeningRequest->isApproved() && !$reopeningRequest->hasTemporaryAccess()) {
            return response()->json(['success' => false, 'message' => 'Your temporary submission access has expired.']);
        }

        // Check submission window (or temporary access)
        $schedule = GradeSubmissionSchedule::where('school_year', $classSchedule->school_year)
            ->where('grading_period', $gradingPeriod)
            ->first();

        if (!$schedule && !$hasTemporaryAccess) {
            return response()->json(['success' => false, 'message' => 'No grade submission schedule configured for this period.']);
        }

        $now = Carbon::now();

        // Allow submission if within normal window OR has temporary access
        $withinNormalWindow = $schedule && $now >= $schedule->start_at && $now <= $schedule->end_at;

        if (!$withinNormalWindow && !$hasTemporaryAccess) {
            return response()->json(['success' => false, 'message' => 'You cannot submit grades because the submission period is closed.']);
        }

        if ($schedule && $now < $schedule->start_at && !$hasTemporaryAccess) {
            return response()->json(['success' => false, 'message' => 'You cannot submit grades because the submission period has not started yet.']);
        }

        // Check if all students have grades for the specific grading period
        $grades = FinalGrade::where('class_schedule_id', $classSchedule->id)
            ->where('grading_period', $gradingPeriod)
            ->get();

        if ($grades->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No grades to submit for Term ' . $gradingPeriod . '. Please import grades first.']);
        }

        // Check if any grades are null for the specific grading period
        if ($grades->where('final_grade', null)->isNotEmpty()) {
            return response()->json(['success' => false, 'message' => 'Some students have incomplete grades for Term ' . $gradingPeriod . '. Please complete all grades before submitting.']);
        }

        // Update status to submitted for the specific grading period
        FinalGrade::where('class_schedule_id', $classSchedule->id)
            ->where('grading_period', $gradingPeriod)
            ->where('status', 'draft')
            ->update([
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

        activity()
            ->event('grade_submitted')
            ->causedBy(auth()->user())
            ->performedOn($classSchedule)
            ->log('Submitted Term ' . $gradingPeriod . ' grades for ' . $classSchedule->subject->name . ' - ' . $classSchedule->section->name);

        return response()->json(['success' => true, 'message' => 'Grades submitted successfully. Waiting for Registrar approval.']);
    }

    public function resubmit(ClassSchedule $classSchedule)
    {
        if ($classSchedule->teacher_id !== auth()->id()) {
            abort(403);
        }

        // Get the current grading period from session
        $gradingPeriod = session('import_grading_period_' . $classSchedule->id);
        
        if (!$gradingPeriod) {
            return redirect()
                ->route('teacher.classes.grades', $classSchedule)
                ->with('error', 'No grading period selected. Please import grades first.');
        }

        // Only allow resubmission if grades were rejected for this specific grading period
        $rejectedGrades = FinalGrade::where('class_schedule_id', $classSchedule->id)
            ->where('grading_period', $gradingPeriod)
            ->where('status', 'rejected')
            ->exists();

        if (!$rejectedGrades) {
            return redirect()
                ->route('teacher.classes.grades', $classSchedule)
                ->with('error', 'Only rejected grades can be resubmitted.');
        }

        // Update status back to submitted for the specific grading period
        FinalGrade::where('class_schedule_id', $classSchedule->id)
            ->where('grading_period', $gradingPeriod)
            ->where('status', 'rejected')
            ->update([
                'status' => 'submitted',
                'submitted_at' => now(),
                'rejection_reason' => null,
            ]);

        activity()
            ->event('grade_resubmitted')
            ->causedBy(auth()->user())
            ->performedOn($classSchedule)
            ->log('Resubmitted Term ' . $gradingPeriod . ' grades for ' . $classSchedule->subject->name . ' - ' . $classSchedule->section->name);

        return redirect()
            ->route('teacher.classes.grades', $classSchedule)
            ->with('success', 'Term ' . $gradingPeriod . ' grades resubmitted successfully.');
    }
}
