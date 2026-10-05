<?php

namespace App\Http\Controllers\SSG;

use App\Http\Controllers\Controller;
use App\Models\SsgEvent;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function index()
    {
        $events = SsgEvent::withCount('attendances')
            ->latest('created_at')
            ->paginate(10);

        return view('ssg.events.index', compact('events'));
    }

    public function create()
    {
        return view('ssg.events.create', [
            'event' => new SsgEvent(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateEvent($request);
        $attendanceCount = 0;

        DB::transaction(function () use ($validated, &$attendanceCount) {
            $event = SsgEvent::create(array_merge($validated, [
                'created_by' => auth()->id(),
            ]));

            Student::query()
                ->whereIn('status', ['Active', 'Pending Student Account', 'Account Created'])
                ->select('id')
                ->chunkById(500, function ($students) use ($event, &$attendanceCount) {
                    foreach ($students as $student) {
                        $attendance = \App\Models\SsgEventAttendance::firstOrCreate(
                            [
                                'ssg_event_id' => $event->id,
                                'student_id' => $student->id,
                            ],
                            [
                                'is_present' => false,
                                'scanned_at' => null,
                                'applicable_fine' => $event->fine_amount,
                                'actual_fine' => $event->fine_amount,
                            ]
                        );

                        if ($attendance->wasRecentlyCreated) {
                            $attendanceCount++;
                        }
                    }
                });
        });

        activity()
            ->causedBy(auth()->user())
            ->log('Created SSG event: ' . $validated['title'] . ' on ' . $validated['event_date'] . '. ' . $attendanceCount . ' attendance record(s) prepared.');

        return redirect()
            ->route('ssg.events.index')
            ->with('success', 'Event created successfully. ' . $attendanceCount . ' attendance record(s) prepared.');
    }

    public function show(SsgEvent $event)
    {
        $event->load('creator')->loadCount('attendances');

        return view('ssg.events.show', compact('event'));
    }

    public function edit(SsgEvent $event)
    {
        return view('ssg.events.edit', compact('event'));
    }

    public function update(Request $request, SsgEvent $event)
    {
        $event->update($this->validateEvent($request));

        activity()
            ->causedBy(auth()->user())
            ->performedOn($event)
            ->log('Updated SSG event: ' . $event->title);

        return redirect()
            ->route('ssg.events.index')
            ->with('success', 'Event updated successfully.');
    }

    public function destroy(SsgEvent $event)
    {
        // Capture information before deletion for logging
        $eventTitle = $event->title;
        $eventDate = $event->event_date->format('F d, Y');

        $event->delete();

        activity()
            ->causedBy(auth()->user())
            ->log('Deleted SSG event: ' . $eventTitle . ' on ' . $eventDate);

        return redirect()
            ->route('ssg.events.index')
            ->with('success', 'Event deleted successfully.');
    }

    public function getStatus(SsgEvent $event): JsonResponse
    {
        return response()->json([
            'status' => $event->status,
        ]);
    }

    private function validateEvent(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'event_date' => ['required', 'date'],
            'event_start_time' => ['required', 'date_format:H:i'],
            'event_end_time' => ['required', 'date_format:H:i', 'after:event_start_time'],
            'scan_start_time' => ['nullable', 'date_format:H:i'],
            'scan_end_time' => ['nullable', 'date_format:H:i', 'after:scan_start_time'],
            'venue' => ['nullable', 'string', 'max:100'],
            'fine_amount' => ['required', 'numeric', 'min:0', 'max:999999.99'],
        ]);
    }
}
