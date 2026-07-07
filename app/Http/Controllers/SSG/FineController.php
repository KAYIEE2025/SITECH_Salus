<?php

namespace App\Http\Controllers\SSG;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Student;
use App\Models\YearLevel;
use Illuminate\Http\Request;

class FineController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'school_year' => ['nullable', 'string', 'max:20'],
            'year_level_id' => ['nullable', 'exists:year_levels,id'],
            'section_id' => ['nullable', 'exists:sections,id'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $students = Student::query()
            ->with(['yearLevel', 'section'])
            ->withCount([
                'ssgAttendances as events_attended_count' => fn ($query) => $query->where('is_present', true),
                'ssgAttendances as events_absent_count' => fn ($query) => $query->where('is_present', false),
            ])
            ->withSum([
                'ssgAttendances as total_fine_balance' => fn ($query) => $query->where('actual_fine', '>', 0),
            ], 'actual_fine')
            ->when($filters['school_year'] ?? null, fn ($query, $schoolYear) => $query->where('school_year', $schoolYear))
            ->when($filters['year_level_id'] ?? null, fn ($query, $yearLevelId) => $query->where('year_level_id', $yearLevelId))
            ->when($filters['section_id'] ?? null, fn ($query, $sectionId) => $query->where('section_id', $sectionId))
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('student_number', 'like', '%' . $search . '%')
                        ->orWhere('first_name', 'like', '%' . $search . '%')
                        ->orWhere('middle_name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('last_name')
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

        return view('ssg.fines.index', compact(
            'students',
            'schoolYears',
            'yearLevels',
            'sections',
            'filters'
        ));
    }

    public function show(Student $student)
    {
        $student->load(['yearLevel', 'section']);

        $fineRecords = $student->ssgAttendances()
            ->with('ssgEvent')
            ->join('ssg_events', 'ssg_events.id', '=', 'ssg_event_attendances.ssg_event_id')
            ->orderByDesc('ssg_events.event_date')
            ->orderByDesc('ssg_events.event_time')
            ->select('ssg_event_attendances.*')
            ->get();

        $totalOutstanding = $fineRecords
            ->where('actual_fine', '>', 0)
            ->sum('actual_fine');

        return view('ssg.fines.show', compact('student', 'fineRecords', 'totalOutstanding'));
    }
}
