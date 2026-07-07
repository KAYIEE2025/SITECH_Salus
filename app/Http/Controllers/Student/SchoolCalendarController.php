<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SchoolEvent;
use Illuminate\Http\Request;

class SchoolCalendarController extends Controller
{
    public function index()
    {
        // Get all school events
        $events = SchoolEvent::orderBy('event_date')->get();
        
        // Get upcoming events (next 5 events from today onwards)
        $upcomingEvents = SchoolEvent::where('event_date', '>=', now()->toDateString())
            ->orderBy('event_date')
            ->take(5)
            ->get();

        return view('student.school-calendar', compact('events', 'upcomingEvents'));
    }
}
