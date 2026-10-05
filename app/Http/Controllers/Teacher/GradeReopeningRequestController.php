<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassSchedule;
use App\Models\GradeSubmissionReopeningRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradeReopeningRequestController extends Controller
{
    public function create(Request $request, ClassSchedule $classSchedule)
    {
        if ($classSchedule->teacher_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'grading_period' => 'required|integer|in:1,2,3,4',
            'reason' => 'required|string|min:10|max:500',
        ]);

        // Check if a pending request already exists for this teacher + grading period + school year
        $existingRequest = GradeSubmissionReopeningRequest::forTeacher(auth()->id())
            ->forPeriod($classSchedule->school_year, $request->grading_period)
            ->pending()
            ->first();

        if ($existingRequest) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a pending reopening request for this grading period.'
            ]);
        }

        try {
            DB::beginTransaction();

            GradeSubmissionReopeningRequest::create([
                'teacher_id' => auth()->id(),
                'grading_period' => $request->grading_period,
                'school_year' => $classSchedule->school_year,
                'reason' => $request->reason,
                'status' => 'Pending',
                'requested_at' => now(),
            ]);

            activity()
                ->event('grade_reopening_request_created')
                ->causedBy(auth()->user())
                ->log('Created grade reopening request for Term ' . $request->grading_period . ' (' . $classSchedule->school_year . '). Reason: ' . $request->reason);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Your reopening request has been sent to the Registrar.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating reopening request: ' . $e->getMessage()
            ]);
        }
    }

    public function checkStatus(Request $request, ClassSchedule $classSchedule)
    {
        if ($classSchedule->teacher_id !== auth()->id()) {
            abort(403);
        }

        $gradingPeriod = $request->query('grading_period');

        if (!$gradingPeriod) {
            return response()->json([
                'has_pending' => false,
                'has_approved' => false,
                'has_rejected' => false,
            ]);
        }

        $request = GradeSubmissionReopeningRequest::forTeacher(auth()->id())
            ->forPeriod($classSchedule->school_year, $gradingPeriod)
            ->orderBy('created_at', 'desc')
            ->first();

        return response()->json([
            'has_pending' => $request && $request->isPending(),
            'has_approved' => $request && $request->isApproved(),
            'has_rejected' => $request && $request->isRejected(),
            'status' => $request ? $request->status : null,
            'temporary_deadline' => $request ? $request->temporary_deadline : null,
        ]);
    }
}
