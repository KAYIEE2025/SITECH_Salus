<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AnnouncementView;
use App\Models\Student;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $student = Student::where('user_id', auth()->id())->first();
        $search = $request->search ?? '';

        if (!$student) {
            return view('student.announcements', [
                'student' => null,
                'announcements' => collect(),
                'search' => $search,
            ]);
        }

        // Build query for visible announcements
        $query = Announcement::with('poster')
            ->where('is_active', true)
            ->where(function ($q) use ($student) {
                // Show announcements targeted to all users
                $q->where('target_type', 'all')
                  // Show announcements targeted to student's year level
                  ->orWhere(function ($subQuery) use ($student) {
                      $subQuery->where('target_type', 'year_level')
                               ->where('target_id', $student->year_level_id);
                  })
                  // Show announcements targeted to student's section
                  ->orWhere(function ($subQuery) use ($student) {
                      $subQuery->where('target_type', 'section')
                               ->where('target_id', $student->section_id);
                  });
            });

        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('body', 'like', "%{$searchTerm}%");
            });
        }

        // Get announcements sorted by newest first
        $announcements = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('student.announcements', compact(
            'student',
            'announcements',
            'search'
        ));
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
