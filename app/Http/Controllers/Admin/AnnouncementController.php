<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\YearLevel;
use App\Models\Section;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('poster')->latest()->get();
        $yearLevels = YearLevel::all();
        $sections = Section::with('yearLevel')->get();
        
        return view('admin.announcements.index', compact('announcements', 'yearLevels', 'sections'));
    }

    public function create()
    {
        $yearLevels = YearLevel::all();
        $sections = Section::with('yearLevel')->get();
        
        return view('admin.announcements.create', compact('yearLevels', 'sections'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'target_type' => 'required|in:all,grade_level,section',
            'target_id' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        Announcement::create([
            'title' => $request->title,
            'body' => $request->body,
            'target_type' => $request->target_type,
            'target_id' => $request->target_type !== 'all' ? $request->target_id : null,
            'posted_by' => auth()->id(),
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement created successfully.');
    }

    public function edit(Announcement $announcement)
    {
        $yearLevels = YearLevel::all();
        $sections = Section::with('yearLevel')->get();
        
        return view('admin.announcements.edit', compact('announcement', 'yearLevels', 'sections'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'target_type' => 'required|in:all,grade_level,section',
            'target_id' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $announcement->update([
            'title' => $request->title,
            'body' => $request->body,
            'target_type' => $request->target_type,
            'target_id' => $request->target_type !== 'all' ? $request->target_id : null,
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement deleted successfully.');
    }
}
