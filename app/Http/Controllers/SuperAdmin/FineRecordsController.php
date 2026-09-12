<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Student;
use App\Models\YearLevel;
use Illuminate\Http\Request;

class FineRecordsController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'school_year' => ['nullable', 'string', 'max:20'],
            'year_level_id' => ['nullable', 'exists:year_levels,id'],
            'section_id' => ['nullable', 'exists:sections,id'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $query = Student::query()
            ->with(['yearLevel', 'section'])
            ->withCount([
                'ssgAttendances as events_attended_count' => fn ($query) => $query->where('is_present', true),
                'ssgAttendances as events_absent_count' => fn ($query) => $query->where('is_present', false),
            ])
            ->withSum([
                'ssgAttendances as total_fine_balance' => fn ($query) => $query->where('actual_fine', '>', 0),
            ], 'actual_fine');

        // Apply date filters on event dates
        if ($request->filled('date_from') || $request->filled('date_to')) {
            $query->whereHas('ssgAttendances', function ($q) use ($request) {
                $q->whereHas('ssgEvent', function ($eventQuery) use ($request) {
                    if ($request->filled('date_from')) {
                        $eventQuery->where('event_date', '>=', $request->date('date_from'));
                    }
                    if ($request->filled('date_to')) {
                        $eventQuery->where('event_date', '<=', $request->date('date_to'));
                    }
                });
            });
        }

        $query->when($filters['school_year'] ?? null, fn ($query, $schoolYear) => $query->where('school_year', $schoolYear))
            ->when($filters['year_level_id'] ?? null, fn ($query, $yearLevelId) => $query->where('year_level_id', $yearLevelId))
            ->when($filters['section_id'] ?? null, fn ($query, $sectionId) => $query->where('section_id', $sectionId))
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('student_number', 'like', '%' . $search . '%')
                        ->orWhere('first_name', 'like', '%' . $search . '%')
                        ->orWhere('middle_name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%');
                });
            });

        $students = $query->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(15)
            ->withQueryString();

        $schoolYears = Student::query()
            ->select('school_year')
            ->whereNotNull('school_year')
            ->distinct()
            ->orderByDesc('school_year')
            ->pluck('school_year');

        $yearLevels = YearLevel::orderBy('level')->get();
        $sections = Section::with('yearLevel')->orderBy('name')->get();

        return view('superadmin.fine-records.index', compact(
            'students',
            'schoolYears',
            'yearLevels',
            'sections',
            'filters'
        ));
    }

    public function generateReport(Request $request)
    {
        $filters = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'school_year' => ['nullable', 'string', 'max:20'],
            'year_level_id' => ['nullable', 'exists:year_levels,id'],
            'section_id' => ['nullable', 'exists:sections,id'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $query = Student::query()
            ->with(['yearLevel', 'section'])
            ->with(['ssgAttendances' => function ($q) {
                $q->with(['ssgEvent', 'scanner'])
                    ->join('ssg_events', 'ssg_events.id', '=', 'ssg_event_attendances.ssg_event_id')
                    ->orderByDesc('ssg_events.event_date')
                    ->orderByDesc('ssg_events.event_time')
                    ->select('ssg_event_attendances.*');
            }])
            ->withCount([
                'ssgAttendances as events_attended_count' => fn ($query) => $query->where('is_present', true),
                'ssgAttendances as events_absent_count' => fn ($query) => $query->where('is_present', false),
            ])
            ->withSum([
                'ssgAttendances as total_fine_balance' => fn ($query) => $query->where('actual_fine', '>', 0),
            ], 'actual_fine');

        // Apply date filters on event dates
        if ($request->filled('date_from') || $request->filled('date_to')) {
            $query->whereHas('ssgAttendances', function ($q) use ($request) {
                $q->whereHas('ssgEvent', function ($eventQuery) use ($request) {
                    if ($request->filled('date_from')) {
                        $eventQuery->where('event_date', '>=', $request->date('date_from'));
                    }
                    if ($request->filled('date_to')) {
                        $eventQuery->where('event_date', '<=', $request->date('date_to'));
                    }
                });
            });
        }

        $query->when($filters['school_year'] ?? null, fn ($query, $schoolYear) => $query->where('school_year', $schoolYear))
            ->when($filters['year_level_id'] ?? null, fn ($query, $yearLevelId) => $query->where('year_level_id', $yearLevelId))
            ->when($filters['section_id'] ?? null, fn ($query, $sectionId) => $query->where('section_id', $sectionId))
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('student_number', 'like', '%' . $search . '%')
                        ->orWhere('first_name', 'like', '%' . $search . '%')
                        ->orWhere('middle_name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%');
                });
            });

        $students = $query->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        // Calculate summary statistics from filtered data
        $totalStudents = $students->count();
        $studentsWithOutstandingFines = $students->where('total_fine_balance', '>', 0)->count();
        $studentsWithZeroBalance = $students->where('total_fine_balance', '<=', 0)->count();
        $totalEventsAttended = $students->sum('events_attended_count');
        $totalEventsMissed = $students->sum('events_absent_count');
        $totalOutstandingFineBalance = $students->sum('total_fine_balance');

        $summary = [
            'total_students' => $totalStudents,
            'students_with_outstanding_fines' => $studentsWithOutstandingFines,
            'students_with_zero_balance' => $studentsWithZeroBalance,
            'total_events_attended' => $totalEventsAttended,
            'total_events_missed' => $totalEventsMissed,
            'total_outstanding_fine_balance' => $totalOutstandingFineBalance,
        ];

        $currentUser = auth()->user();
        $generatedAt = now();

        // Generate unique report number: FRR-YYYYMMDD-XXXX
        $reportNumber = 'FRR-' . $generatedAt->format('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        // Prepare filter information for display
        $schoolYearDisplay = $filters['school_year'] ?? 'All School Years';
        $yearLevelDisplay = $filters['year_level_id'] ? (YearLevel::find($filters['year_level_id'])->name ?? 'All Grade Levels') : 'All Grade Levels';
        $sectionDisplay = $filters['section_id'] ? (Section::find($filters['section_id'])->name ?? 'All Sections') : 'All Sections';
        
        $filterDisplay = [
            'school_year' => $schoolYearDisplay,
            'year_level' => $yearLevelDisplay,
            'section' => $sectionDisplay,
            'generated_records' => $totalStudents,
        ];

        // Generate filename based on date range
        $dateFrom = $request->filled('date_from') ? $request->date('date_from')->format('Y-m-d') : now()->format('Y-m-d');
        $dateTo = $request->filled('date_to') ? $request->date('date_to')->format('Y-m-d') : $dateFrom;

        if ($dateFrom === $dateTo) {
            $filename = "Fine_Records_{$dateFrom}.pdf";
        } else {
            $filename = "Fine_Records_{$dateFrom}_to_{$dateTo}.pdf";
        }

        // Determine action (download or print)
        $action = $request->input('action', 'download');

        return view('superadmin.pdf.fine-records', compact(
            'students',
            'summary',
            'currentUser',
            'generatedAt',
            'filterDisplay',
            'action',
            'filename',
            'reportNumber'
        ));
    }
}
