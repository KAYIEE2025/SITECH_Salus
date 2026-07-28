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
                'gwa' => 'N/A',
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

        // Calculate GWA from filtered grades (respects school year filter)
        $gwa = $this->calculateGWA($grades);

        return view('student.grades.index', [
            'groupedGrades' => $groupedGrades,
            'gradeSummary' => $gradeSummary,
            'schoolYears' => $schoolYears,
            'filterSchoolYear' => $request->get('school_year', ''),
            'gwa' => $gwa,
        ]);
    }

    private function approvedGradesFor(int $studentId): Collection
    {
        $grades = FinalGrade::with(['classSchedule.subject', 'classSchedule.section.yearLevel', 'classSchedule.teacher'])
            ->where('student_id', $studentId)
            ->where('status', 'approved')
            ->orderByDesc('reviewed_at')
            ->orderByDesc('created_at')
            ->get();

        // Group by class_schedule_id and pivot grading_period to term columns
        $groupedGrades = $grades->groupBy('class_schedule_id')->map(function ($gradeRecords) {
            $firstRecord = $gradeRecords->first();
            
            // Initialize term columns
            $term1 = null;
            $term2 = null;
            $term3 = null;

            // Pivot grading_period to term columns
            foreach ($gradeRecords as $record) {
                if ($record->grading_period == 1) {
                    $term1 = $record->term_1 ?? $record->quarterly_grade;
                } elseif ($record->grading_period == 2) {
                    $term2 = $record->term_2;
                } elseif ($record->grading_period == 3) {
                    $term3 = $record->term_3;
                }
            }

            // Backward compatibility: if no grading_period but has quarterly_grade, treat as term_1
            if (!$term1 && !$term2 && !$term3 && $firstRecord->quarterly_grade && !$firstRecord->grading_period) {
                $term1 = $firstRecord->quarterly_grade;
            }

            // Calculate Final Rating only if all three terms are present
            if ($term1 !== null && $term2 !== null && $term3 !== null) {
                $finalRating = number_format(($term1 + $term2 + $term3) / 3, 2);
            } else {
                $finalRating = null;
            }

            // Create a unified grade object with pivoted terms
            $unifiedGrade = clone $firstRecord;
            $unifiedGrade->term_1 = $term1;
            $unifiedGrade->term_2 = $term2;
            $unifiedGrade->term_3 = $term3;
            $unifiedGrade->final_rating = $finalRating;

            return $unifiedGrade;
        });

        return $groupedGrades;
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

    private function calculateGWA(Collection $grades): ?string
    {
        // Filter grades that have a final_rating
        $finalRatings = $grades->filter(function ($grade) {
            return $grade->final_rating !== null && $grade->final_rating !== '';
        });

        if ($finalRatings->isEmpty()) {
            return 'N/A';
        }

        // Convert final_rating strings to float for calculation
        $numericFinalRatings = $finalRatings->map(function ($grade) {
            return is_numeric($grade->final_rating) ? floatval($grade->final_rating) : null;
        })->filter();

        if ($numericFinalRatings->isEmpty()) {
            return 'N/A';
        }

        // Calculate GWA: Sum of Final Ratings / Number of Subjects with Final Ratings
        $gwa = $numericFinalRatings->avg();

        return number_format($gwa, 2);
    }
}
