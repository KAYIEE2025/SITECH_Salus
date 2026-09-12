<?php

namespace App\Http\Controllers\SSG;

use App\Http\Controllers\Controller;
use App\Models\SsgEvent;
use App\Models\SsgEventAttendance;

class DashboardController extends Controller
{
    public function index()
    {
        $totalEvents = SsgEvent::count();
        $upcomingEvents = SsgEvent::where('status', 'Upcoming')->count();
        $ongoingEvents = SsgEvent::where('status', 'Ongoing')->count();
        $totalFinesCollected = SsgEventAttendance::sum('actual_fine');
        $recentEvents = SsgEvent::latest()->take(5)->get();

        return view('ssg.dashboard', compact(
            'totalEvents',
            'upcomingEvents',
            'ongoingEvents',
            'totalFinesCollected',
            'recentEvents'
        ));
    }
}
