<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\ClassSchedule;
use App\Models\GradeSubmissionReopeningRequest;
use App\Models\GradeSubmissionSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GradeReopeningRequestController extends Controller
{
    public function index()
    {
        $requests = GradeSubmissionReopeningRequest::with(['teacher', 'reviewer', 'approver'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('registrar.grade-reopening-requests.index', compact('requests'));
    }

    public function show(GradeSubmissionReopeningRequest $reopeningRequest)
    {
        $reopeningRequest->load(['teacher', 'reviewer', 'approver']);

        // Get class schedule details if available
        $classSchedule = ClassSchedule::where('teacher_id', $reopeningRequest->teacher_id)
            ->where('school_year', $reopeningRequest->school_year)
            ->first();

        return view('registrar.grade-reopening-requests.show', compact('reopeningRequest', 'classSchedule'));
    }

    public function approve(Request $httpRequest, GradeSubmissionReopeningRequest $reopeningRequest)
    {
        Log::info('APPROVE METHOD CALLED', [
            'request_id' => $reopeningRequest->id,
            'input' => $httpRequest->all(),
        ]);

        $httpRequest->validate([
            'new_deadline' => 'required|date|after:now',
            'remarks' => 'nullable|string',
        ]);

        Log::info('VALIDATION PASSED', [
            'new_deadline' => $httpRequest->new_deadline,
            'remarks' => $httpRequest->remarks,
        ]);

        try {
            DB::beginTransaction();

            $newDeadline = Carbon::parse($httpRequest->new_deadline);

            Log::info('BEFORE APPROVING REOPENING REQUEST', [
                'request_id' => $reopeningRequest->id,
                'current_status' => $reopeningRequest->status,
                'school_year' => $reopeningRequest->school_year,
                'grading_period' => $reopeningRequest->grading_period,
            ]);

            // Update the reopening request with approval details
            $reopeningRequest->approve(auth()->id(), $newDeadline, $httpRequest->remarks);

            activity()
                ->event('grade_reopening_request_approved')
                ->causedBy(auth()->user())
                ->performedOn($reopeningRequest)
                ->log('Approved grade reopening request for Term ' . $reopeningRequest->grading_period . ' (' . $reopeningRequest->school_year . '). New deadline: ' . $newDeadline->format('F d, Y g:i A'));

            Log::info('AFTER APPROVING REOPENING REQUEST', [
                'request_id' => $reopeningRequest->id,
                'new_status' => $reopeningRequest->fresh()->status,
                'approved_by' => $reopeningRequest->fresh()->approved_by,
                'approved_at' => $reopeningRequest->fresh()->approved_at,
            ]);

            // Find and update the matching GradeSubmissionSchedule
            $schedule = GradeSubmissionSchedule::where('school_year', $reopeningRequest->school_year)
                ->where('grading_period', $reopeningRequest->grading_period)
                ->first();

            Log::info('LOOKING FOR GRADE SUBMISSION SCHEDULE', [
                'school_year' => $reopeningRequest->school_year,
                'grading_period' => $reopeningRequest->grading_period,
                'schedule_found' => $schedule ? true : false,
            ]);

            if ($schedule) {
                Log::info('BEFORE UPDATING SCHEDULE', [
                    'schedule_id' => $schedule->id,
                    'current_start_at' => $schedule->start_at,
                    'current_end_at' => $schedule->end_at,
                    'new_start_at' => now(),
                    'new_end_at' => $newDeadline,
                ]);

                // Update start_at to now() and end_at to the selected deadline
                // Status is computed automatically from dates in the model
                $schedule->update([
                    'start_at' => now(),
                    'end_at' => $newDeadline,
                ]);

                Log::info('AFTER UPDATING SCHEDULE', [
                    'schedule_id' => $schedule->id,
                    'updated_start_at' => $schedule->fresh()->start_at,
                    'updated_end_at' => $schedule->fresh()->end_at,
                ]);
            } else {
                Log::warning('NO GRADE SUBMISSION SCHEDULE FOUND', [
                    'school_year' => $reopeningRequest->school_year,
                    'grading_period' => $reopeningRequest->grading_period,
                ]);
            }

            DB::commit();

            Log::info('TRANSACTION COMMITTED SUCCESSFULLY');

            return redirect()
                ->route('registrar.grade-reopening-requests.index')
                ->with('success', 'Submission period successfully reopened.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ERROR IN APPROVE METHOD', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()
                ->route('registrar.grade-reopening-requests.index')
                ->with('error', 'Error approving request: ' . $e->getMessage());
        }
    }

    public function reject(Request $httpRequest, GradeSubmissionReopeningRequest $reopeningRequest)
    {
        Log::info('REJECT METHOD CALLED', [
            'request_id' => $reopeningRequest->id,
        ]);

        try {
            DB::beginTransaction();

            $reopeningRequest->reject(auth()->id());

            activity()
                ->event('grade_reopening_request_rejected')
                ->causedBy(auth()->user())
                ->performedOn($reopeningRequest)
                ->log('Rejected grade reopening request for Term ' . $reopeningRequest->grading_period . ' (' . $reopeningRequest->school_year . ')');

            Log::info('REJECT METHOD COMPLETED', [
                'request_id' => $reopeningRequest->id,
                'new_status' => $reopeningRequest->fresh()->status,
            ]);

            DB::commit();

            return redirect()
                ->route('registrar.grade-reopening-requests.index')
                ->with('success', 'Reopening request rejected.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ERROR IN REJECT METHOD', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()
                ->route('registrar.grade-reopening-requests.index')
                ->with('error', 'Error rejecting request: ' . $e->getMessage());
        }
    }
}
