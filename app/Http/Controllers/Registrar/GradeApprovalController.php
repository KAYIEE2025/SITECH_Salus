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

        $finalGrade->update([
            'status'           => 'rejected',
            'reviewed_by'      => auth()->id(),
            'reviewed_at'      => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($finalGrade)
            ->log('Rejected grade for ' . $finalGrade->student->last_name . ', ' . $finalGrade->student->first_name
                . ' in ' . optional($finalGrade->classSchedule->subject)->code);

        return back()->with('success', 'Grade rejected and sent back to teacher.');
    }
}