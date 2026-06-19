<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\FinalGrade;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents = Student::count();
        $pendingGrades = FinalGrade::where('status', 'submitted')->count();
        return view('registrar.dashboard', compact('totalStudents', 'pendingGrades'));
    }
}