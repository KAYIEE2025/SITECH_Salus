<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassSchedule;

class DashboardController extends Controller
{
    public function index()
    {
        $classes = ClassSchedule::where('teacher_id', auth()->id())->count();
        return view('teacher.dashboard', compact('classes'));
    }
}