<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassSchedule;
use App\Models\FinalGrade;
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
