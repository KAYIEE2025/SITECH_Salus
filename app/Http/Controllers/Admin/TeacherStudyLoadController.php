<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSchedule;
use App\Models\User;
use Illuminate\Http\Request;

class TeacherStudyLoadController extends Controller
{
    public function index(Request $request)
    {
        $teachers = User::role('Teacher')->orderBy('name')->get();

        // Get unique school years from class_schedules
        $schoolYears = ClassSchedule::select('school_year')
            ->distinct()
            ->orderBy('school_year', 'desc')
            ->pluck('school_year');

        $query = ClassSchedule::with(['teacher', 'subject', 'section.yearLevel'])
            ->when($request->filled('teacher_id'), fn ($q) =>
                $q->where('teacher_id', $request->teacher_id)
            )
            ->when($request->filled('search'), fn ($q) =>
                $q->whereHas('teacher', fn ($subQuery) =>
                    $subQuery->where('name', 'like', '%' . $request->search . '%')
                )
            )
            ->when($request->filled('school_year'), fn ($q) =>
                $q->where('school_year', $request->school_year)
            )
            ->orderBy('school_year', 'desc')
            ->orderBy('time_start');

        $schedules = $query->paginate(50);
        
        return view('admin.teacher-study-load.index', compact(
            'teachers',
            'schoolYears',
            'schedules'
        ));
    }
}
