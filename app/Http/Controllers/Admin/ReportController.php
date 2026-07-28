<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\YearLevel;
use App\Models\Section;
use App\Models\Subject;
use App\Models\FinalGrade;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $yearLevels = YearLevel::all();
        $sections = Section::with('yearLevel')->get();
        $subjects = Subject::all();
        
        return view('admin.reports.index', compact('yearLevels', 'sections', 'subjects'));
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

        return view('admin.reports.student-list-preview', compact('students', 'yearLevel', 'section'));
    }

    public function generateGradeSummary(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $subject = Subject::find($request->subject_id);
        
        $grades = FinalGrade::with(['student', 'classSchedule.subject'])
            ->whereHas('classSchedule', function ($query) use ($request) {
                $query->where('subject_id', $request->subject_id);
            })
            ->where('status', 'approved')
            ->get();

        $passedCount = $grades->where('final_grade', '>=', 75)->count();
        $failedCount = $grades->where('final_grade', '<', 75)->count();
        $averageGrade = $grades->avg('final_grade');

        return view('admin.reports.grade-summary-preview', compact('grades', 'subject', 'passedCount', 'failedCount', 'averageGrade'));
    }
}
