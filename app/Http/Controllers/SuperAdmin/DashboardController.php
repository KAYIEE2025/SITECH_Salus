<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalAdmins = User::role('Admin')->count();
        $totalRegistrars = User::role('Registrar')->count();
        $totalTeachers = User::role('Teacher')->count();
        $totalSSG = User::role('SSG')->count();
        $totalStudents = User::role('Student')->count();
        $activeUsers = User::where('is_active', true)->count();
        $inactiveUsers = User::where('is_active', false)->count();
        $pendingStudentAccounts = Student::whereNull('user_id')
            ->where('status', 'Pending Student Account')
            ->count();
        $recentLogs = Activity::with('causer')->latest()->take(8)->get();

        return view('superadmin.dashboard', compact(
            'totalUsers', 'totalAdmins', 'totalRegistrars',
            'totalTeachers', 'totalSSG', 'totalStudents',
            'activeUsers', 'inactiveUsers', 'pendingStudentAccounts', 'recentLogs'
        ));
    }
}
