<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SsgEvent;
use App\Models\SsgEventAttendance;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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

    public function resetSSGRecords()
    {
        try {
            DB::beginTransaction();

            // Reset SSG event attendance records
            $attendanceCount = SsgEventAttendance::count();
            SsgEventAttendance::query()->delete();

            // Reset SSG event records
            $eventCount = SsgEvent::count();
            SsgEvent::query()->delete();

            DB::commit();

            // Log the action
            activity()
                ->causedBy(auth()->user())
                ->withProperties([
                    'attendance_records_reset' => $attendanceCount,
                    'events_reset' => $eventCount,
                ])
                ->log('Archived and Reset SSG Records');

            return redirect()->route('superadmin.dashboard')
                ->with('success', 'SSG records have been successfully archived and reset for the new school year.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('superadmin.dashboard')
                ->with('error', 'Failed to reset SSG records. Please try again.');
        }
    }
}
