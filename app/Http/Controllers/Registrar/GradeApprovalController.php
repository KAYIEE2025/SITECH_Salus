<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\FinalGrade;
use Illuminate\Http\Request;

class GradeApprovalController extends Controller
{
    public function index()
    {
        $pendingGrades = FinalGrade::with(['student', 'classSchedule.subject', 'classSchedule.teacher'])
            ->where('status', 'submitted')
            ->get()
            ->groupBy(function ($grade) {
                return $grade->classSchedule->id;
            });

        $history = FinalGrade::with(['student', 'classSchedule.subject', 'classSchedule.teacher', 'reviewer'])
            ->whereIn('status', ['approved', 'rejected'])
            ->latest('reviewed_at')
            ->take(20)
            ->get();

        return view('registrar.grade-approval.index', compact('pendingGrades', 'history'));
    }

    public function approveClass(Request $request, $classScheduleId)
    {
        $grades = FinalGrade::where('class_schedule_id', $classScheduleId)
            ->where('status', 'submitted')
            ->get();

        foreach ($grades as $grade) {
            $grade->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);
        }

        return back()->with('success', 'All grades for this class approved successfully.');
    }

    public function rejectClass(Request $request, $classScheduleId)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:255',
        ]);

        $grades = FinalGrade::where('class_schedule_id', $classScheduleId)
            ->where('status', 'submitted')
            ->get();

        foreach ($grades as $grade) {
            $grade->update([
                'status' => 'draft',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'rejection_reason' => $request->rejection_reason,
            ]);
        }

        return back()->with('success', 'All grades rejected and returned to teacher for revision.');
    }

    public function approve(Request $request, FinalGrade $finalGrade)
    {
        $finalGrade->update([
            'status'      => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($finalGrade)
            ->log('Approved grade for ' . $finalGrade->student->last_name . ', ' . $finalGrade->student->first_name
                . ' in ' . optional($finalGrade->classSchedule->subject)->code);

        return back()->with('success', 'Grade approved successfully.');
    }

    public function reject(Request $request, FinalGrade $finalGrade)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:255',
        ]);

        // Return to draft status so teacher can edit
        $finalGrade->update([
            'status'           => 'draft',
            'reviewed_by'      => auth()->id(),
            'reviewed_at'      => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($finalGrade)
            ->log('Rejected grade for ' . $finalGrade->student->last_name . ', ' . $finalGrade->student->first_name
                . ' in ' . optional($finalGrade->classSchedule->subject)->code);

        return back()->with('success', 'Grade rejected and returned to teacher for revision.');
    }
}