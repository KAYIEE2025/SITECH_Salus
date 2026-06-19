<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;

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

        return view('superadmin.dashboard', compact(
            'totalUsers', 'totalAdmins', 'totalRegistrars',
            'totalTeachers', 'totalSSG', 'totalStudents'
        ));
    }
}
