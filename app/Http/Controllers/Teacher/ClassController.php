<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassSchedule;
use App\Models\StudyLoad;
use App\Models\Student;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        $classes = ClassSchedule::where('teacher_id', auth()->id())
            ->with(['subject', 'section.yearLevel'])
            ->withCount('studyLoads')
            ->orderBy('school_year', 'desc')
            ->orderBy('semester')
            ->orderBy('section_id')
            ->get();

        $sectionGroups = $classes
            ->groupBy(fn (ClassSchedule $class) => $class->section_id ?? 'unassigned')
            ->map(function ($sectionClasses) {
                $section = $sectionClasses->first()->section;

                return [
                    'section' => $section,
                    'classes' => $sectionClasses
                        ->sortBy(fn (ClassSchedule $class) => implode('|', [
                            $class->school_year,
                            $class->semester,
                            $class->subject->name ?? '',
                        ]))
                        ->values(),
                    'student_count' => $sectionClasses->max('study_loads_count') ?? 0,
                ];
            })
            ->sortBy(fn ($group) => ($group['section']->yearLevel->level ?? 999) . '-' . ($group['section']->name ?? 'Unassigned'))
            ->values();

        return view('teacher.classes.index', compact('sectionGroups'));
    }

    public function showStudents(ClassSchedule $classSchedule)
    {
        // Verify the class belongs to the logged-in teacher
        if ($classSchedule->teacher_id !== auth()->id()) {
            abort(403);
        }

        $students = StudyLoad::where('class_schedule_id', $classSchedule->id)
            ->with('student')
            ->get()
            ->sortBy('student.last_name');

        return view('teacher.classes.students', compact('classSchedule', 'students'));
    }

    public function manageGrades(ClassSchedule $classSchedule)
    {
        if ($classSchedule->teacher_id !== auth()->id()) {
            abort(403);
        }

        $classSchedule->load(['subject', 'section.yearLevel']);

        $students = StudyLoad::where('class_schedule_id', $classSchedule->id)
            ->with('student')
            ->get()
            ->sortBy('student.last_name');

        return view('teacher.classes.grades', compact('classSchedule', 'students'));
    }
}
