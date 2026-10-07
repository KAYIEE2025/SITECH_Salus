<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\FinalGrade;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class GradeViewController extends Controller
{
    public function index(Request $request)
    {
        $students = Student::with(['yearLevel', 'section'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where(function ($query) use ($search) {
                    $query->where('student_number', 'like', "%{$search}%")
                        ->orWhere('qr_code_value', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('middle_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('registrar.grades.index', compact('students'));
    }

    public function show(Student $student, Request $request)
    {
        $student->load('yearLevel', 'section');

        $grades = $this->approvedGradesFor($student->id);

        // Apply school year filter
        if ($request->filled('school_year')) {
            $grades = $grades->filter(function ($grade) use ($request) {
                return $grade->classSchedule->school_year === $request->school_year;
            });
        }

        // Apply term filter
        if ($request->filled('term')) {
            $term = $request->term;
            $grades = $grades->filter(function ($grade) use ($term) {
                return $grade->grading_period == $term;
            });
        }

        // Get available filters
        $allGrades = $this->approvedGradesFor($student->id);
        $schoolYears = $allGrades->pluck('classSchedule.school_year')->unique()->sortDesc();

        // Group grades by school year
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

        return view('registrar.grades.show', [
            'student' => $student,
            'groupedGrades' => $groupedGrades,
            'schoolYears' => $schoolYears,
            'filterSchoolYear' => $request->get('school_year', ''),
            'filterTerm' => $request->get('term', '')
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
}
