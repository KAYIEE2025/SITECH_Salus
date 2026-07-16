<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\FinalGrade;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class GradeViewController extends Controller
{
    public function index(Request $request)
    {
        $student = Student::where('user_id', auth()->id())->first();
        
        if (!$student) {
            return view('student.grades.index', [
                'groupedGrades' => collect(),
                'gradeSummary' => $this->calculateGradeSummary(collect()),
                'schoolYears' => collect(),
                'filterSchoolYear' => $request->get('school_year', ''),
            ]);
        }

        $grades = $this->approvedGradesFor($student->id);

        // Apply school year filter
        if ($request->filled('school_year')) {
            $grades = $grades->filter(function ($grade) use ($request) {
                return $grade->classSchedule->school_year === $request->school_year;
            });
        }

        // Get available filters
        $allGrades = $this->approvedGradesFor($student->id);
        $schoolYears = $allGrades->pluck('classSchedule.school_year')->unique()->sortDesc();

        // Group grades by school year only
        $groupedGrades = $grades->groupBy(function ($grade) {
            return $grade->classSchedule->school_year;
        })->map(function ($grades) {
            return [
                'school_year' => $grades->first()->classSchedule->school_year,
                'grades' => $grades->sortBy('classSchedule.subject.name'),
            ];
        })->sortByDesc(function ($group) {
            return $group['school_year'];
        });

        // Calculate grade summary
        $gradeSummary = $this->calculateGradeSummary($allGrades);

        return view('student.grades.index', [
            'groupedGrades' => $groupedGrades,
            'gradeSummary' => $gradeSummary,
            'schoolYears' => $schoolYears,
            'filterSchoolYear' => $request->get('school_year', ''),
        ]);
    }

    private function approvedGradesFor(int $studentId): Collection
    {
        return FinalGrade::with(['classSchedule.subject', 'classSchedule.section.yearLevel', 'classSchedule.teacher'])
            ->where('student_id', $studentId)
            ->where('status', 'approved')
            ->orderByDesc('reviewed_at')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($grade) {
                // Map quarterly_grade to term_1 if term fields are empty (for backward compatibility)
                if (!$grade->term_1 && !$grade->term_2 && !$grade->term_3 && $grade->quarterly_grade) {
                    $grade->term_1 = $grade->quarterly_grade;
                }
                
                // Compute final rating if all terms are present
                if ($grade->term_1 && $grade->term_2 && $grade->term_3) {
                    $grade->final_rating = round(($grade->term_1 + $grade->term_2 + $grade->term_3) / 3, 2);
                } elseif ($grade->term_1) {
                    // If only term_1 exists (backward compatibility), use it as final rating
                    $grade->final_rating = $grade->term_1;
                } else {
                    $grade->final_rating = null;
                }
                return $grade;
            });
    }

    private function calculateGradeSummary(Collection $grades): array
    {
        $totalSubjects = $grades->count();
        
        if ($totalSubjects === 0) {
            return [
                'total_subjects' => 0,
                'average_grade' => 0,
                'passed_subjects' => 0,
                'failed_subjects' => 0,
            ];
        }

        $numericGrades = $grades->map(function ($grade) {
            $quarterlyGrade = $grade->quarterly_grade;
            return is_numeric($quarterlyGrade) ? floatval($quarterlyGrade) : null;
        })->filter();

        $averageGrade = $numericGrades->isNotEmpty() 
            ? round($numericGrades->avg(), 2) 
            : 0;

        $passedSubjects = $grades->filter(function ($grade) {
            return in_array(strtolower($grade->remarks ?? ''), ['passed', 'pass']) || 
                   ($grade->quarterly_grade && is_numeric($grade->quarterly_grade) && $grade->quarterly_grade >= 75);
        })->count();

        $failedSubjects = $totalSubjects - $passedSubjects;

        return [
            'total_subjects' => $totalSubjects,
            'average_grade' => $averageGrade,
            'passed_subjects' => $passedSubjects,
            'failed_subjects' => $failedSubjects,
        ];
    }
}
