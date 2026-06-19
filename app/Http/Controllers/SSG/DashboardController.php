<?php

namespace App\Http\Controllers\SSG;

use App\Http\Controllers\Controller;
use App\Models\SsgEvent;

class DashboardController extends Controller
{
    public function index()
    {
        $totalEvents = SsgEvent::count();
        $upcomingEvents = SsgEvent::where('status', 'Upcoming')->count();
        return view('ssg.dashboard', compact('totalEvents', 'upcomingEvents'));
    }
}