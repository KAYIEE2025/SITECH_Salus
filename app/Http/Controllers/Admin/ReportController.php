<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\SchoolYearHelper;
use App\Models\Student;
use App\Models\YearLevel;
use App\Models\Section;
use App\Models\Subject;
use App\Models\FinalGrade;
use App\Models\StudyLoad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        $yearLevels = YearLevel::all();
        $sections = Section::with('yearLevel')->get();
        $subjects = Subject::all();
        $activeSchoolYear = SchoolYearHelper::getActive();
        
        return view('admin.reports.index', compact('yearLevels', 'sections', 'subjects', 'activeSchoolYear'));
    }

    public function generateStudentList(Request $request)
    {
        $request->validate([
            'year_level_id' => 'nullable|exists:year_levels,id',
            'section_id' => 'nullable|exists:sections,id',
        ]);

        $query = Student::with(['yearLevel', 'section']);

        if ($request->filled('year_level_id')) {
            $query->where('year_level_id', $request->year_level_id);
        }

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        $students = $query->orderBy('last_name')->get();

        $yearLevel = $request->filled('year_level_id') ? YearLevel::find($request->year_level_id) : null;
        $section = $request->filled('section_id') ? Section::with('yearLevel')->find($request->section_id) : null;

        activity()
            ->event('report_generated')
            ->causedBy(auth()->user())
            ->log('Generated student list report. ' . $students->count() . ' student(s) included.');

        return view('admin.reports.student-list-preview', compact('students', 'yearLevel', 'section'));
    }

    public function generateGradeSummary(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'year_level_id' => 'nullable|exists:year_levels,id',
            'section_id' => 'nullable|exists:sections,id',
        ]);

        $subject = Subject::find($request->subject_id);
        
        // Get class schedules for this subject
        $classScheduleIds = \App\Models\ClassSchedule::where('subject_id', $request->subject_id)
            ->pluck('id');

        // Get approved grades for these class schedules
        $query = FinalGrade::with(['student'])
            ->whereIn('class_schedule_id', $classScheduleIds)
            ->where('status', 'approved');

        // Apply filters through student relationship
        if ($request->filled('year_level_id')) {
            $query->whereHas('student', function ($query) use ($request) {
                $query->where('year_level_id', $request->year_level_id);
            });
        }
        if ($request->filled('section_id')) {
            $query->whereHas('student', function ($query) use ($request) {
                $query->where('section_id', $request->section_id);
            });
        }

        $grades = $query->get();

        // Group grades by student
        $studentGrades = [];
        $processedStudents = [];

        foreach ($grades as $grade) {
            $studentId = $grade->student_id;
            
            if (!isset($studentGrades[$studentId])) {
                $studentGrades[$studentId] = [
                    'student' => $grade->student,
                    'term1' => '-',
                    'term2' => '-',
                    'term3' => '-',
                    'final_grade' => '-',
                ];
                $processedStudents[$studentId] = true;
            }

            // Set term grade based on grading_period using the correct term columns
            if ($grade->grading_period == 1 && $grade->term_1 !== null) {
                $studentGrades[$studentId]['term1'] = number_format($grade->term_1, 2);
            } elseif ($grade->grading_period == 2 && $grade->term_2 !== null) {
                $studentGrades[$studentId]['term2'] = number_format($grade->term_2, 2);
            } elseif ($grade->grading_period == 3 && $grade->term_3 !== null) {
                $studentGrades[$studentId]['term3'] = number_format($grade->term_3, 2);
            }

            // Check if student has all terms approved
            $term1 = $studentGrades[$studentId]['term1'] !== '-';
            $term2 = $studentGrades[$studentId]['term2'] !== '-';
            $term3 = $studentGrades[$studentId]['term3'] !== '-';

            if ($term1 && $term2 && $term3) {
                // Calculate final grade if not already set
                if ($studentGrades[$studentId]['final_grade'] === '-') {
                    $t1 = (float) str_replace('-', '0', $studentGrades[$studentId]['term1']);
                    $t2 = (float) str_replace('-', '0', $studentGrades[$studentId]['term2']);
                    $t3 = (float) str_replace('-', '0', $studentGrades[$studentId]['term3']);
                    $final = ($t1 + $t2 + $t3) / 3;
                    $studentGrades[$studentId]['final_grade'] = number_format($final, 2);
                }
            }
        }

        // Convert to indexed array
        $studentGrades = array_values($studentGrades);

        $yearLevel = $request->filled('year_level_id') ? YearLevel::find($request->year_level_id) : null;
        $section = $request->filled('section_id') ? Section::with('yearLevel')->find($request->section_id) : null;

        activity()
            ->event('report_generated')
            ->causedBy(auth()->user())
            ->log('Generated grade summary report for ' . $subject->name . '. ' . count($studentGrades) . ' student(s) included.');

        return view('admin.reports.grade-summary-preview', compact('studentGrades', 'subject', 'yearLevel', 'section'));
    }

    public function generateStudentListPdf(Request $request)
    {
        $request->validate([
            'year_level_id' => 'nullable|exists:year_levels,id',
            'section_id' => 'nullable|exists:sections,id',
        ]);

        $query = Student::with(['yearLevel', 'section']);

        if ($request->filled('year_level_id')) {
            $query->where('year_level_id', $request->year_level_id);
        }

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        $students = $query->orderBy('last_name')->get();

        $yearLevel = $request->filled('year_level_id') ? YearLevel::find($request->year_level_id) : null;
        $section = $request->filled('section_id') ? Section::with('yearLevel')->find($request->section_id) : null;

        $currentUser = Auth::user();
        $generatedAt = now();

        $filename = 'student-list-' . $generatedAt->format('Y-m-d-His') . '.pdf';

        return view('admin.reports.student-list-pdf', compact('students', 'yearLevel', 'section', 'currentUser', 'generatedAt', 'filename'));
    }

    public function generateGradeSummaryPdf(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'year_level_id' => 'nullable|exists:year_levels,id',
            'section_id' => 'nullable|exists:sections,id',
        ]);

        $subject = Subject::find($request->subject_id);
        
        // Get class schedules for this subject
        $classScheduleIds = \App\Models\ClassSchedule::where('subject_id', $request->subject_id)
            ->pluck('id');

        // Get approved grades for these class schedules
        $query = FinalGrade::with(['student'])
            ->whereIn('class_schedule_id', $classScheduleIds)
            ->where('status', 'approved');

        // Apply filters through student relationship
        if ($request->filled('year_level_id')) {
            $query->whereHas('student', function ($query) use ($request) {
                $query->where('year_level_id', $request->year_level_id);
            });
        }
        if ($request->filled('section_id')) {
            $query->whereHas('student', function ($query) use ($request) {
                $query->where('section_id', $request->section_id);
            });
        }

        $grades = $query->get();

        // Group grades by student
        $studentGrades = [];
        $processedStudents = [];

        foreach ($grades as $grade) {
            $studentId = $grade->student_id;
            
            if (!isset($studentGrades[$studentId])) {
                $studentGrades[$studentId] = [
                    'student' => $grade->student,
                    'term1' => '-',
                    'term2' => '-',
                    'term3' => '-',
                    'final_grade' => '-',
                ];
                $processedStudents[$studentId] = true;
            }

            // Set term grade based on grading_period using the correct term columns
            if ($grade->grading_period == 1 && $grade->term_1 !== null) {
                $studentGrades[$studentId]['term1'] = number_format($grade->term_1, 2);
            } elseif ($grade->grading_period == 2 && $grade->term_2 !== null) {
                $studentGrades[$studentId]['term2'] = number_format($grade->term_2, 2);
            } elseif ($grade->grading_period == 3 && $grade->term_3 !== null) {
                $studentGrades[$studentId]['term3'] = number_format($grade->term_3, 2);
            }

            // Check if student has all terms approved
            $term1 = $studentGrades[$studentId]['term1'] !== '-';
            $term2 = $studentGrades[$studentId]['term2'] !== '-';
            $term3 = $studentGrades[$studentId]['term3'] !== '-';

            if ($term1 && $term2 && $term3) {
                // Calculate final grade if not already set
                if ($studentGrades[$studentId]['final_grade'] === '-') {
                    $t1 = (float) str_replace('-', '0', $studentGrades[$studentId]['term1']);
                    $t2 = (float) str_replace('-', '0', $studentGrades[$studentId]['term2']);
                    $t3 = (float) str_replace('-', '0', $studentGrades[$studentId]['term3']);
                    $final = ($t1 + $t2 + $t3) / 3;
                    $studentGrades[$studentId]['final_grade'] = number_format($final, 2);
                }
            }
        }

        // Convert to indexed array
        $studentGrades = array_values($studentGrades);

        $yearLevel = $request->filled('year_level_id') ? YearLevel::find($request->year_level_id) : null;
        $section = $request->filled('section_id') ? Section::with('yearLevel')->find($request->section_id) : null;

        $currentUser = Auth::user();
        $generatedAt = now();

        $filename = 'grade-summary-' . $generatedAt->format('Y-m-d-His') . '.pdf';

        return view('admin.reports.grade-summary-pdf', compact('studentGrades', 'subject', 'yearLevel', 'section', 'currentUser', 'generatedAt', 'filename'));
    }
}
