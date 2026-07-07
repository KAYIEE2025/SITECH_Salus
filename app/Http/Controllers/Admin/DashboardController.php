<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\SchoolEvent;
use App\Models\Student;
use App\Models\FinalGrade;

class DashboardController extends Controller
{
    public function index()
    {
        $totalAnnouncements = Announcement::count();
        $totalEvents = SchoolEvent::count();
        $totalStudents = Student::count();
        $pendingGrades = FinalGrade::where('status', 'submitted')->count();
        return view('admin.dashboard', compact('totalAnnouncements', 'totalEvents', 'totalStudents', 'pendingGrades'));
    }
}