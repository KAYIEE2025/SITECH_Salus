<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\SchoolEvent;

class DashboardController extends Controller
{
    public function index()
    {
        $totalAnnouncements = Announcement::count();
        $totalEvents = SchoolEvent::count();
        return view('admin.dashboard', compact('totalAnnouncements', 'totalEvents'));
    }
}