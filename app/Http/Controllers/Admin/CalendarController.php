<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolEvent;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index()
    {
        $events = SchoolEvent::with('creator')->latest()->paginate(50);

        return view('admin.calendar.index', compact('events'));
    }

    public function create()
    {
        return view('admin.calendar.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'event_end_date' => 'nullable|date|after_or_equal:event_date',
            'color' => 'nullable|string|max:7',
        ]);

        $event = SchoolEvent::create([
            'title' => $request->title,
            'description' => $request->description,
            'event_date' => $request->event_date,
            'event_end_date' => $request->event_end_date,
            'color' => $request->color ?? '#3b82f6',
            'created_by' => auth()->id(),
        ]);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($event)
            ->log('Created school event: ' . $event->title . ' on ' . $event->event_date->format('F d, Y'));

        return redirect()->route('admin.calendar.index')
            ->with('success', 'School event added successfully.');
    }

    public function edit(SchoolEvent $event)
    {
        return view('admin.calendar.edit', compact('event'));
    }

    public function update(Request $request, SchoolEvent $event)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'event_end_date' => 'nullable|date|after_or_equal:event_date',
            'color' => 'nullable|string|max:7',
        ]);

        $event->update([
            'title' => $request->title,
            'description' => $request->description,
            'event_date' => $request->event_date,
            'event_end_date' => $request->event_end_date,
            'color' => $request->color ?? '#3b82f6',
        ]);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($event)
            ->log('Updated school event: ' . $event->title);

        return redirect()->route('admin.calendar.index')
            ->with('success', 'School event updated successfully.');
    }

    public function destroy(SchoolEvent $event)
    {
        // Capture information before deletion for logging
        $eventTitle = $event->title;
        $eventDate = $event->event_date->format('F d, Y');

        $event->delete();

        activity()
            ->causedBy(auth()->user())
            ->log('Deleted school event: ' . $eventTitle . ' on ' . $eventDate);

        return redirect()->route('admin.calendar.index')
            ->with('success', 'School event deleted successfully.');
    }
}
