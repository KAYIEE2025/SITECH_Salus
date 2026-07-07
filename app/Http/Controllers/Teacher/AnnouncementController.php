<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Announcement;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('poster')
            ->latest()
            ->get();

        return view('teacher.announcements.index', compact('announcements'));
    }
}
