<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Helpers\SchoolYearHelper;
use App\Models\FinalGrade;
use App\Models\ClassSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradeApprovalController extends Controller
{
    public function index(Request $request)
    {
        // Get filter options
        $schoolYears = ClassSchedule::select('school_year')
            ->whereNotNull('school_year')
            ->distinct()
            ->orderBy('school_year', 'desc')
            ->pluck('school_year');

        $yearLevels = \App\Models\YearLevel::orderBy('level')->get();
        $sections = \App\Models\Section::with('yearLevel')->orderBy('name')->get();
        $subjects = \App\Models\Subject::where('is_active', true)->orderBy('code')->get();
        $teachers = \App\Models\User::role('Teacher')->orderBy('name')->get();

        // Build pending grades query with filters
        $pendingQuery = FinalGrade::where('status', 'submitted')
            ->whereNotNull('grading_period')
            ->with(['student', 'classSchedule.subject', 'classSchedule.section.yearLevel', 'classSchedule.teacher']);

        // Apply filters
        $pendingQuery->when($request->filled('search'), function ($query) use ($request) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($studentQuery) use ($search) {
                    $studentQuery->where('first_name', 'like', "%{$search}%")
                        ->orWhere('middle_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('student_number', 'like', "%{$search}%");
                })
                ->orWhere('student_name', 'like', "%{$search}%")
                ->orWhere('student_number', 'like', "%{$search}%");
            });
        })
        ->when($request->filled('school_year'), function ($query) use ($request) {
            $query->whereHas('classSchedule', function ($q) use ($request) {
                $q->where('school_year', $request->school_year);
            });
        })
        ->when($request->filled('term'), function ($query) use ($request) {
            $termMap = ['Term 1' => 1, 'Term 2' => 2, 'Term 3' => 3];
            $gradingPeriod = $termMap[$request->term] ?? null;
            if ($gradingPeriod) {
                $query->where('grading_period', $gradingPeriod);
            }
        })
        ->when($request->filled('grade_level_id'), function ($query) use ($request) {
            $query->whereHas('classSchedule.section.yearLevel', function ($q) use ($request) {
                $q->where('id', $request->grade_level_id);
            });
        })
        ->when($request->filled('section_id'), function ($query) use ($request) {
            $query->whereHas('classSchedule', function ($q) use ($request) {
                $q->where('section_id', $request->section_id);
            });
        })
        ->when($request->filled('subject_id'), function ($query) use ($request) {
            $query->whereHas('classSchedule', function ($q) use ($request) {
                $q->where('subject_id', $request->subject_id);
            });
        })
        ->when($request->filled('teacher_id'), function ($query) use ($request) {
            $query->whereHas('classSchedule', function ($q) use ($request) {
                $q->where('teacher_id', $request->teacher_id);
            });
        });

        // Get paginated pending grades
        $pendingGrades = $pendingQuery->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Group by class schedule for display
        $groupedPendingGrades = $pendingGrades->getCollection()->groupBy('class_schedule_id');

        \Log::info('REGISTRAR INDEX - Pending grades query', [
            'total_pending_count' => $groupedPendingGrades->count(),
            'grouped_by_class' => $groupedPendingGrades->keys()->toArray(),
        ]);

        // Get history (approved/rejected) with filters
        $historyQuery = FinalGrade::whereIn('status', ['approved', 'rejected'])
            ->with(['student', 'classSchedule.subject']);

        // Apply same filters to history
        $historyQuery->when($request->filled('search'), function ($query) use ($request) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($studentQuery) use ($search) {
                    $studentQuery->where('first_name', 'like', "%{$search}%")
                        ->orWhere('middle_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('student_number', 'like', "%{$search}%");
                })
                ->orWhere('student_name', 'like', "%{$search}%")
                ->orWhere('student_number', 'like', "%{$search}%");
            });
        })
        ->when($request->filled('school_year'), function ($query) use ($request) {
            $query->whereHas('classSchedule', function ($q) use ($request) {
                $q->where('school_year', $request->school_year);
            });
        })
        ->when($request->filled('term'), function ($query) use ($request) {
            $termMap = ['Term 1' => 1, 'Term 2' => 2, 'Term 3' => 3];
            $gradingPeriod = $termMap[$request->term] ?? null;
            if ($gradingPeriod) {
                $query->where('grading_period', $gradingPeriod);
            }
        })
        ->when($request->filled('grade_level_id'), function ($query) use ($request) {
            $query->whereHas('classSchedule.section.yearLevel', function ($q) use ($request) {
                $q->where('id', $request->grade_level_id);
            });
        })
        ->when($request->filled('section_id'), function ($query) use ($request) {
            $query->whereHas('classSchedule', function ($q) use ($request) {
                $q->where('section_id', $request->section_id);
            });
        })
        ->when($request->filled('subject_id'), function ($query) use ($request) {
            $query->whereHas('classSchedule', function ($q) use ($request) {
                $q->where('subject_id', $request->subject_id);
            });
        })
        ->when($request->filled('teacher_id'), function ($query) use ($request) {
            $query->whereHas('classSchedule', function ($q) use ($request) {
                $q->where('teacher_id', $request->teacher_id);
            });
        });

        $history = $historyQuery->orderBy('reviewed_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $activeSchoolYear = SchoolYearHelper::getActive();

        return view('registrar.grade-approval.index', compact(
            'pendingGrades',
            'groupedPendingGrades',
            'history',
            'activeSchoolYear',
            'schoolYears',
            'yearLevels',
            'sections',
            'subjects',
            'teachers'
        ));
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

            // Get submitted grades for this class (exclude old records without grading_period)
            $grades = FinalGrade::where('class_schedule_id', $classSchedule->id)
                ->where('status', 'submitted')
                ->whereNotNull('grading_period')
                ->get();

            \Log::info('APPROVE - Grades lookup', [
                'class_schedule_id' => $classSchedule->id,
                'count' => $grades->count(),
                'grading_periods' => $grades->pluck('grading_period')->unique(),
            ]);

            if ($grades->isEmpty()) {
                return back()->with('error', 'No submitted grades found for this class.');
            }

            // Get grading period from the submitted records (persisted during Teacher submit)
            $firstGrade = $grades->first();
            $gradingPeriod = $firstGrade->grading_period;

            if (!$gradingPeriod || !in_array($gradingPeriod, [1, 2, 3])) {
                return back()->with('error', 'Unable to determine grading period. Please ensure grades were submitted correctly.');
            }

            // Check if this specific grading period is already approved using grading_period column
            $alreadyApproved = FinalGrade::where('class_schedule_id', $classSchedule->id)
                ->where('grading_period', $gradingPeriod)
                ->where('status', 'approved')
                ->exists();
            
            if ($alreadyApproved) {
                return back()->with('error', "Term {$gradingPeriod} grades for this class have already been approved.");
            }

            // Update the status to approved for this grading period
            \Log::info('APPROVE BEFORE UPDATE', [
                'class_schedule_id' => $classSchedule->id,
                'grading_period' => $gradingPeriod,
                'record_count' => $grades->count(),
            ]);

            $approvedCount = FinalGrade::where('class_schedule_id', $classSchedule->id)
                ->where('grading_period', $gradingPeriod)
                ->where('status', 'submitted')
                ->update([
                    'status' => 'approved',
                    'reviewed_by' => auth()->id(),
                    'reviewed_at' => now(),
                ]);

            \Log::info('APPROVE AFTER UPDATE', [
                'affected_rows' => $approvedCount,
            ]);

            // Record history for each grade
            $grades->each(function ($grade) use ($gradingPeriod) {
                $grade->recordHistory('approved', 'approved', "Term {$gradingPeriod} grades approved by Registrar");
            });

            activity()
                ->event('grade_approved')
                ->causedBy(auth()->user())
                ->performedOn($classSchedule)
                ->log('Approved Term ' . $gradingPeriod . ' grades for ' . $classSchedule->subject->name . ' - ' . $classSchedule->section->name . '. ' . $approvedCount . ' student(s) affected.');

            DB::commit();

            return redirect()->route('registrar.grade-approval')->with('success', "Term {$gradingPeriod} grades approved successfully. {$approvedCount} student grades are now official.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error approving grades: ' . $e->getMessage());
        }
    }

    public function rejectClass(Request $request, ClassSchedule $classSchedule)
    {
        \Log::info('REJECT TRACE A - Method entered', [
            'class_schedule_id' => $classSchedule->id,
            'registrar_id' => auth()->id(),
        ]);

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        \Log::info('REJECT TRACE B - Validation passed', [
            'rejection_reason' => $request->rejection_reason,
        ]);

        try {
            DB::beginTransaction();

            \Log::info('REJECT TRACE C - Transaction started');

            // Get submitted grades for this class (without grading_period filter first)
            $grades = FinalGrade::where('class_schedule_id', $classSchedule->id)
                ->where('status', 'submitted')
                ->whereNotNull('grading_period')
                ->get();

            \Log::info('REJECT TRACE D - Initial record lookup', [
                'class_schedule_id' => $classSchedule->id,
                'count' => $grades->count(),
                'grades_ids' => $grades->pluck('id'),
                'grades_status' => $grades->pluck('status'),
                'grading_periods' => $grades->pluck('grading_period'),
            ]);

            if ($grades->isEmpty()) {
                \Log::warning('REJECT TRACE D.1 - No submitted grades found');
                return back()->with('error', 'No submitted grades found for this class.');
            }

            \Log::info('REJECT TRACE E - Grades not empty');

            // Get grading period from the submitted records (persisted during Teacher submit)
            $firstGrade = $grades->first();
            $gradingPeriod = $firstGrade->grading_period;

            \Log::info('REJECT TRACE F - Database grading period check', [
                'grading_period' => $gradingPeriod,
                'first_grade_id' => $firstGrade->id,
            ]);

            if (!$gradingPeriod || !in_array($gradingPeriod, [1, 2, 3])) {
                \Log::warning('REJECT TRACE F.1 - Invalid or missing grading period', [
                    'grading_period' => $gradingPeriod,
                ]);
                return back()->with('error', 'Unable to determine grading period. Please ensure grades were submitted correctly.');
            }

            \Log::info('REJECT TRACE G - Grading period resolved', [
                'grading_period' => $gradingPeriod,
            ]);

            // Now filter by the specific grading period
            $grades = FinalGrade::where('class_schedule_id', $classSchedule->id)
                ->where('grading_period', $gradingPeriod)
                ->where('status', 'submitted')
                ->get();

            \Log::info('REJECT TRACE H - Filtered by grading period', [
                'class_schedule_id' => $classSchedule->id,
                'grading_period' => $gradingPeriod,
                'count' => $grades->count(),
                'grades_ids' => $grades->pluck('id'),
            ]);

            if ($grades->isEmpty()) {
                \Log::warning('REJECT TRACE H.1 - No submitted grades found for this grading period');
                return back()->with('error', "No submitted grades found for Term {$gradingPeriod} of this class.");
            }

            // Check if this specific grading period is already rejected
            $termField = 'term_' . $gradingPeriod;
            $alreadyRejected = FinalGrade::where('class_schedule_id', $classSchedule->id)
                ->where('grading_period', $gradingPeriod)
                ->where('status', 'rejected')
                ->exists();

            \Log::info('REJECT TRACE I - Already rejected check', [
                'already_rejected' => $alreadyRejected,
                'term_field' => $termField,
            ]);

            if ($alreadyRejected) {
                \Log::warning('REJECT TRACE I.1 - Already rejected');
                return back()->with('error', "Term {$gradingPeriod} grades for this class have already been rejected.");
            }

            \Log::info('REJECT TRACE J - Before update');

            \Log::info('REJECT BEFORE UPDATE', [
                'class_schedule_id' => $classSchedule->id,
                'grading_period' => $gradingPeriod,
                'record_ids' => $grades->pluck('id'),
                'statuses' => $grades->pluck('status'),
                'rejection_reason' => $request->rejection_reason,
                'registrar_id' => auth()->id(),
            ]);

            // Update the status to rejected for this grading period
            $rejectedCount = FinalGrade::where('class_schedule_id', $classSchedule->id)
                ->where('grading_period', $gradingPeriod)
                ->where('status', 'submitted')
                ->update([
                    'status' => 'rejected',
                    'reviewed_by' => auth()->id(),
                    'reviewed_at' => now(),
                    'rejection_reason' => $request->rejection_reason,
                ]);

            \Log::info('REJECT AFTER UPDATE', [
                'affected_rows' => $rejectedCount,
            ]);

            if ($rejectedCount == 0) {
                \Log::error('REJECT ERROR - No records updated', [
                    'class_schedule_id' => $classSchedule->id,
                    'grading_period' => $gradingPeriod,
                    'expected_count' => $grades->count(),
                ]);
                DB::rollBack();
                return back()->with('error', 'Error: No grade records were updated. Please try again or contact support.');
            }

            // Query fresh records from database immediately after update
            $freshGrades = FinalGrade::where('class_schedule_id', $classSchedule->id)
                ->where('grading_period', $gradingPeriod)
                ->get(['id', 'student_id', 'status', 'rejection_reason', 'reviewed_by', 'reviewed_at']);

            \Log::info('REJECT DATABASE AFTER UPDATE', [
                'records' => $freshGrades->map(function($g) {
                    return [
                        'id' => $g->id,
                        'status' => $g->status,
                        'rejection_reason' => $g->rejection_reason,
                        'reviewed_by' => $g->reviewed_by,
                        'reviewed_at' => $g->reviewed_at,
                    ];
                }),
            ]);

            // Record history for each grade
            $grades->each(function ($grade) use ($request, $gradingPeriod) {
                $grade->recordHistory('rejected', 'rejected', "Term {$gradingPeriod} grades rejected by Registrar", $request->rejection_reason);
            });

            activity()
                ->event('grade_rejected')
                ->causedBy(auth()->user())
                ->performedOn($classSchedule)
                ->log('Rejected Term ' . $gradingPeriod . ' grades for ' . $classSchedule->subject->name . ' - ' . $classSchedule->section->name . '. Reason: ' . $request->rejection_reason . '. ' . $rejectedCount . ' student(s) affected.');

            \Log::info('REJECT BEFORE COMMIT');

            DB::commit();

            \Log::info('REJECT AFTER COMMIT');

            return redirect()->route('registrar.grade-approval')->with('success', "Term {$gradingPeriod} grades rejected and returned to teacher for revision. {$rejectedCount} student grades affected.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error rejecting grades: ' . $e->getMessage());
        }
    }

    public function approveSelected(Request $request)
    {
        \Log::info('BULK APPROVE', [
            'selected_class_schedule_ids' => $request->class_schedule_ids,
        ]);

        $request->validate([
            'class_schedule_ids' => 'required|array',
            'class_schedule_ids.*' => 'integer|exists:class_schedules,id',
        ]);

        $classScheduleIds = $request->class_schedule_ids;

        try {
            DB::beginTransaction();

            $totalApproved = 0;

            foreach ($classScheduleIds as $classScheduleId) {
                \Log::info('BULK APPROVE - Processing class', [
                    'class_schedule_id' => $classScheduleId,
                ]);

                // Get ALL submitted grades for this class (all grading periods)
                $grades = FinalGrade::where('class_schedule_id', $classScheduleId)
                    ->where('status', 'submitted')
                    ->whereNotNull('grading_period')
                    ->get();

                if ($grades->isEmpty()) {
                    \Log::warning('BULK APPROVE - No submitted grades found', [
                        'class_schedule_id' => $classScheduleId,
                    ]);
                    continue;
                }

                \Log::info('BULK APPROVE BEFORE UPDATE', [
                    'class_schedule_id' => $classScheduleId,
                    'record_count' => $grades->count(),
                    'grading_periods' => $grades->pluck('grading_period')->unique()->toArray(),
                    'record_ids' => $grades->pluck('id'),
                ]);

                // Update ALL submitted grades for this class to approved
                $affected = FinalGrade::where('class_schedule_id', $classScheduleId)
                    ->where('status', 'submitted')
                    ->update([
                        'status' => 'approved',
                        'reviewed_by' => auth()->id(),
                        'reviewed_at' => now(),
                    ]);

                \Log::info('BULK APPROVE RESULT', [
                    'class_schedule_id' => $classScheduleId,
                    'affected_rows' => $affected,
                ]);

                // Record history for each grade
                $grades->each(function ($grade) {
                    $grade->recordHistory('approved', 'approved', "Class grades approved by Registrar");
                });

                $totalApproved += $affected;
            }

            activity()
                ->event('grade_approved_bulk')
                ->causedBy(auth()->user())
                ->log('Bulk approved grades for ' . count($classScheduleIds) . ' class(es). ' . $totalApproved . ' student(s) affected.');

            DB::commit();

            \Log::info('BULK APPROVE COMMITTED', [
                'total_approved' => $totalApproved,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully approved {$totalApproved} grade records across " . count($classScheduleIds) . " class(es).",
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('APPROVE SELECTED ERROR', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error approving selected classes: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function rejectSelected(Request $request)
    {
        \Log::info('BULK REJECT', [
            'selected_class_schedule_ids' => $request->class_schedule_ids,
        ]);

        $request->validate([
            'class_schedule_ids' => 'required|array',
            'class_schedule_ids.*' => 'integer|exists:class_schedules,id',
            'rejection_reason' => 'required|string|max:500',
        ]);

        $classScheduleIds = $request->class_schedule_ids;
        $rejectionReason = $request->rejection_reason;

        try {
            DB::beginTransaction();

            $totalRejected = 0;

            foreach ($classScheduleIds as $classScheduleId) {
                \Log::info('BULK REJECT - Processing class', [
                    'class_schedule_id' => $classScheduleId,
                ]);

                // Get ALL submitted grades for this class (all grading periods)
                $grades = FinalGrade::where('class_schedule_id', $classScheduleId)
                    ->where('status', 'submitted')
                    ->whereNotNull('grading_period')
                    ->get();

                if ($grades->isEmpty()) {
                    \Log::warning('BULK REJECT - No submitted grades found', [
                        'class_schedule_id' => $classScheduleId,
                    ]);
                    continue;
                }

                \Log::info('BULK REJECT BEFORE UPDATE', [
                    'class_schedule_id' => $classScheduleId,
                    'record_count' => $grades->count(),
                    'grading_periods' => $grades->pluck('grading_period')->unique()->toArray(),
                    'record_ids' => $grades->pluck('id'),
                    'rejection_reason' => $rejectionReason,
                ]);

                // Update ALL submitted grades for this class to rejected
                $affected = FinalGrade::where('class_schedule_id', $classScheduleId)
                    ->where('status', 'submitted')
                    ->update([
                        'status' => 'rejected',
                        'rejection_reason' => $rejectionReason,
                        'reviewed_by' => auth()->id(),
                        'reviewed_at' => now(),
                    ]);

                \Log::info('BULK REJECT RESULT', [
                    'class_schedule_id' => $classScheduleId,
                    'affected_rows' => $affected,
                ]);

                // Record history for each grade
                $grades->each(function ($grade) use ($rejectionReason) {
                    $grade->recordHistory('rejected', 'rejected', "Class grades rejected by Registrar", $rejectionReason);
                });

                $totalRejected += $affected;
            }

            activity()
                ->event('grade_rejected_bulk')
                ->causedBy(auth()->user())
                ->log('Bulk rejected grades for ' . count($classScheduleIds) . ' class(es). Reason: ' . $rejectionReason . '. ' . $totalRejected . ' student(s) affected.');

            DB::commit();

            \Log::info('BULK REJECT COMMITTED', [
                'total_rejected' => $totalRejected,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully rejected {$totalRejected} grade records across " . count($classScheduleIds) . " class(es).",
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('REJECT SELECTED ERROR', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting selected classes: ' . $e->getMessage(),
            ], 500);
        }
    }
}