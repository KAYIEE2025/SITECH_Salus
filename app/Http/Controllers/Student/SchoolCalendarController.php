<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SchoolEvent;
use Illuminate\Http\Request;

class SchoolCalendarController extends Controller
{
    public function index(Request $request)
    {
        // Get school events with optional search filter
        $eventsQuery = SchoolEvent::orderBy('event_date');

        // Apply server-side search filter
        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $eventsQuery->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $events = $eventsQuery->get();

        // Get upcoming events (next 5 events from today onwards)
        // Apply the same search filter to upcoming events
        $upcomingEventsQuery = SchoolEvent::where('event_date', '>=', now()->toDateString())
            ->orderBy('event_date')
            ->take(5);

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $upcomingEventsQuery->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $upcomingEvents = $upcomingEventsQuery->get();

        return view('student.school-calendar', compact('events', 'upcomingEvents'));
    }
}
