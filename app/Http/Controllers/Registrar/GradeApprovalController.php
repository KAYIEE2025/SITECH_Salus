<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\FinalGrade;
use App\Models\ClassSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradeApprovalController extends Controller
{
    public function index()
    {
        // Get submitted grades grouped by class schedule
        $pendingGrades = FinalGrade::where('status', 'submitted')
            ->with(['student', 'classSchedule.subject', 'classSchedule.section.yearLevel', 'classSchedule.teacher'])
            ->get()
            ->groupBy('class_schedule_id');

        // Get history (approved/rejected)
        $history = FinalGrade::whereIn('status', ['approved', 'rejected'])
            ->with(['student', 'classSchedule.subject'])
            ->orderBy('reviewed_at', 'desc')
            ->take(50)
            ->get();

        return view('registrar.grade-approval.index', compact('pendingGrades', 'history'));
    }

    public function view(ClassSchedule $classSchedule)
    {
        $grades = FinalGrade::where('class_schedule_id', $classSchedule->id)
            ->with('student')
            ->get();

        $classSchedule->load(['subject', 'section.yearLevel', 'teacher']);

        // Get grade history for timeline
        $gradeTimeline = \App\Models\GradeHistory::whereHas('finalGrade', function($query) use ($classSchedule) {
            $query->where('class_schedule_id', $classSchedule->id);
        })
        ->with('user')
        ->orderBy('created_at')
        ->get()
        ->groupBy(function($history) {
            return $history->created_at->format('Y-m-d H:i:s');
        })
        ->map(function($group) {
            $first = $group->first();
            return [
                'timestamp' => $first->created_at,
                'action' => ucfirst($first->action),
                'status' => ucfirst($first->status),
                'description' => $first->description,
                'user' => $first->user->name ?? 'System',
                'rejection_reason' => $first->rejection_reason,
            ];
        })
        ->values();

        return view('registrar.grade-approval.view', compact('classSchedule', 'grades', 'gradeTimeline'));
    }

    public function approveClass(Request $request, ClassSchedule $classSchedule)
    {
        try {
            DB::beginTransaction();

            // Get submitted grades for this class
            $grades = FinalGrade::where('class_schedule_id', $classSchedule->id)
                ->where('status', 'submitted')
                ->get();

            if ($grades->isEmpty()) {
                return back()->with('error', 'No submitted grades found for this class.');
            }

            // Determine which grading period is being approved
            // Check which term field has values in the submitted grades
            $firstGrade = $grades->first();
            $gradingPeriod = null;
            if ($firstGrade->term_1 && !$firstGrade->term_2 && !$firstGrade->term_3) {
                $gradingPeriod = 1;
            } elseif ($firstGrade->term_2 && !$firstGrade->term_3) {
                $gradingPeriod = 2;
            } elseif ($firstGrade->term_3) {
                $gradingPeriod = 3;
            }

            if (!$gradingPeriod) {
                return back()->with('error', 'Unable to determine which grading period is being approved.');
            }

            // Check if this specific grading period is already approved
            $termField = 'term_' . $gradingPeriod;
            $alreadyApproved = FinalGrade::where('class_schedule_id', $classSchedule->id)
                ->where('status', 'approved')
                ->whereNotNull($termField)
                ->exists();
            
            if ($alreadyApproved) {
                return back()->with('error', "Term {$gradingPeriod} grades for this class have already been approved.");
            }

            // Update the status to approved
            $approvedCount = FinalGrade::where('class_schedule_id', $classSchedule->id)
                ->where('status', 'submitted')
                ->update([
                    'status' => 'approved',
                    'reviewed_by' => auth()->id(),
                    'reviewed_at' => now(),
                ]);

            // Record history for each grade
            $grades->each(function ($grade) use ($gradingPeriod) {
                $grade->recordHistory('approved', 'approved', "Term {$gradingPeriod} grades approved by Registrar");
            });

            DB::commit();

            return back()->with('success', "Term {$gradingPeriod} grades approved successfully. {$approvedCount} student grades are now official.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error approving grades: ' . $e->getMessage());
        }
    }

    public function rejectClass(Request $request, ClassSchedule $classSchedule)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // Get submitted grades for this class
            $grades = FinalGrade::where('class_schedule_id', $classSchedule->id)
                ->where('status', 'submitted')
                ->get();

            if ($grades->isEmpty()) {
                return back()->with('error', 'No submitted grades found for this class.');
            }

            // Determine which grading period is being rejected
            // Check which term field has values in the submitted grades
            $firstGrade = $grades->first();
            $gradingPeriod = null;
            if ($firstGrade->term_1 && !$firstGrade->term_2 && !$firstGrade->term_3) {
                $gradingPeriod = 1;
            } elseif ($firstGrade->term_2 && !$firstGrade->term_3) {
                $gradingPeriod = 2;
            } elseif ($firstGrade->term_3) {
                $gradingPeriod = 3;
            }

            if (!$gradingPeriod) {
                return back()->with('error', 'Unable to determine which grading period is being rejected.');
            }

            // Check if this specific grading period is already rejected
            $termField = 'term_' . $gradingPeriod;
            $alreadyRejected = FinalGrade::where('class_schedule_id', $classSchedule->id)
                ->where('status', 'rejected')
                ->whereNotNull($termField)
                ->exists();
            
            if ($alreadyRejected) {
                return back()->with('error', "Term {$gradingPeriod} grades for this class have already been rejected.");
            }

            // Update the status to rejected
            $rejectedCount = FinalGrade::where('class_schedule_id', $classSchedule->id)
                ->where('status', 'submitted')
                ->update([
                    'status' => 'rejected',
                    'reviewed_by' => auth()->id(),
                    'reviewed_at' => now(),
                    'rejection_reason' => $request->rejection_reason,
                ]);

            // Record history for each grade
            $grades->each(function ($grade) use ($request, $gradingPeriod) {
                $grade->recordHistory('rejected', 'rejected', "Term {$gradingPeriod} grades rejected by Registrar", $request->rejection_reason);
            });

            DB::commit();

            return back()->with('success', "Term {$gradingPeriod} grades rejected and returned to teacher for revision. {$rejectedCount} student grades affected.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error rejecting grades: ' . $e->getMessage());
        }
    }
}