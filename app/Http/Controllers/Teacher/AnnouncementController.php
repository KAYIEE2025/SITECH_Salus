<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AnnouncementView;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('poster')
            ->latest()
            ->get();

        return view('teacher.announcements.index', compact('announcements'));
    }

    public function markSeen(Announcement $announcement)
    {
        AnnouncementView::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'announcement_id' => $announcement->id,
            ],
            [
                'viewed_at' => now(),
            ]
        );

        return response()->json(['success' => true]);
    }
}
