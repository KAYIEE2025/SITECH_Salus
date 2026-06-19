<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\FinalGrade;

class DashboardController extends Controller
{
    public function index()
    {
        $student = Student::where('user_id', auth()->id())->first();
        $approvedGrades = FinalGrade::where('student_id', optional($student)->id)
            ->where('status', 'approved')->count();
        return view('student.dashboard', compact('student', 'approvedGrades'));
    }
}