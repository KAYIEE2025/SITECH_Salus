<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\FinalGrade;
use App\Models\Section;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents = Student::count();
        
        // Count distinct class submissions (same logic as Grade Approval page)
        $pendingGrades = FinalGrade::where('status', 'submitted')
            ->whereNotNull('grading_period')
            ->distinct('class_schedule_id')
            ->count('class_schedule_id');
        
        $activeSections = Section::where('is_active', true)->count();
        return view('registrar.dashboard', compact('totalStudents', 'pendingGrades', 'activeSections'));
    }
}