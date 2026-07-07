<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\FinalGrade;
use Illuminate\Support\Collection;

class GradeViewController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;
        $grades = $this->approvedGradesFor($student?->id);
        
        // Group grades by school year and semester
        $groupedGrades = $grades->groupBy(function ($grade) {
            return $grade->classSchedule->school_year . '|' . $grade->classSchedule->semester;
        })->map(function ($grades) {
            return [
                'school_year' => $grades->first()->classSchedule->school_year,
                'semester' => $grades->first()->classSchedule->semester,
                'grades' => $grades->sortBy('classSchedule.subject.name'),
            ];
        })->sortByDesc(function ($group) {
            return $group['school_year'];
        });

        return view('student.grades.index', compact('groupedGrades'));
    }

    public function print()
    {
        // Only Registrar users can access print functionality
        if (!auth()->user()->hasRole('registrar')) {
            abort(403, 'Unauthorized access. Only Registrar users can print grades.');
        }

        $student = auth()->user()->student;
        $grades = $this->approvedGradesFor($student?->id);
        
        // Group grades by school year and semester
        $groupedGrades = $grades->groupBy(function ($grade) {
            return $grade->classSchedule->school_year . '|' . $grade->classSchedule->semester;
        })->map(function ($grades) {
            return [
                'school_year' => $grades->first()->classSchedule->school_year,
                'semester' => $grades->first()->classSchedule->semester,
                'grades' => $grades->sortBy('classSchedule.subject.name'),
            ];
        })->sortByDesc(function ($group) {
            return $group['school_year'];
        });

        return view('student.grades.print', compact('student', 'groupedGrades'));
    }

    private function approvedGradesFor(?int $studentId): Collection
    {
        if (!$studentId) {
            return collect();
        }

        return FinalGrade::with(['classSchedule.subject', 'classSchedule.section'])
            ->where('student_id', $studentId)
            ->where('status', 'approved')
            ->orderByDesc('reviewed_at')
            ->orderByDesc('created_at')
            ->get();
    }
}
