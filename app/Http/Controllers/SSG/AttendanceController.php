<?php

namespace App\Http\Controllers\SSG;

use App\Http\Controllers\Controller;
use App\Models\SsgEvent;
use App\Models\SsgEventAttendance;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index(SsgEvent $event)
    {
        // Ensure all active students have attendance records for this event
        $this->ensureAttendanceRecords($event);

        $event->loadCount([
            'attendances',
            'attendances as present_count' => fn ($query) => $query->where('is_present', true),
        ]);

        $attendances = $this->attendanceQuery($event)->get();

        return view('ssg.attendance.index', compact('event', 'attendances'));
    }

    public function scan(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'event_id' => ['required', 'exists:ssg_events,id'],
            'qr_value' => ['required', 'string'],
        ]);

        $event = SsgEvent::findOrFail($validated['event_id']);

        // Use computed status (automatic based on event times)
        if ($event->status !== 'Ongoing') {
            return response()->json([
                'success' => false,
                'message' => 'Attendance Closed.',
            ]);
        }

        // Validate scan time window
        $now = Carbon::now('Asia/Manila');
        $eventDate = $event->event_date;

        if ($event->scan_start_time) {
            $startTime = Carbon::parse($eventDate->format('Y-m-d') . ' ' . $event->scan_start_time, 'Asia/Manila');
            if ($now->lt($startTime)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attendance Closed.',
                ]);
            }
        }

        if ($event->scan_end_time) {
            $endTime = Carbon::parse($eventDate->format('Y-m-d') . ' ' . $event->scan_end_time, 'Asia/Manila');
            if ($now->gt($endTime)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attendance Closed.',
                ]);
            }
        }

        $student = Student::where('qr_code_value', $validated['qr_value'])->first();

        if (! $student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found.',
            ]);
        }

        $attendance = SsgEventAttendance::where('ssg_event_id', $event->id)
            ->where('student_id', $student->id)
            ->first();

        if (! $attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance record not found for this event.',
            ]);
        }

        if ($attendance->is_present) {
            return response()->json([
                'success' => false,
                'message' => 'Student already scanned.',
            ]);
        }

        $attendance->update([
            'is_present' => true,
            'scanned_at' => now(),
            'actual_fine' => 0,
        ]);

        return response()->json([
            'success' => true,
            'student_name' => $student->full_name,
            'student_number' => $student->student_number,
            'student_photo' => $student->photo,
            'scan_time' => now()->format('h:i A'),
            'attendance_status' => 'Present',
            'fine_status' => 'Waived',
            'message' => 'Attendance Recorded',
        ]);
    }

    public function list(SsgEvent $event)
    {
        // Ensure all active students have attendance records for this event
        $this->ensureAttendanceRecords($event);

        $attendances = $this->attendanceQuery($event)->get();

        return view('ssg.attendance._table', compact('attendances'));
    }

    public function extendTime(Request $request, SsgEvent $event): JsonResponse
    {
        $validated = $request->validate([
            'scan_end_time' => ['required', 'date_format:H:i'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $oldEndTime = $event->scan_end_time;
        $newEndTime = $validated['scan_end_time'];
        $reason = $validated['reason'] ?? 'No reason provided';

        $event->update([
            'scan_end_time' => $newEndTime,
        ]);

        // Log the extend time action
        DB::table('activity_logs')->insert([
            'user_id' => auth()->id(),
            'action' => 'extend_scan_time',
            'description' => "Extended scan end time for event '{$event->title}' from {$oldEndTime} to {$newEndTime}. Reason: {$reason}",
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Scan time extended successfully.',
            'new_scan_end_time' => $newEndTime,
        ]);
    }

    private function attendanceQuery(SsgEvent $event)
    {
        return $event->attendances()
            ->with(['student.yearLevel', 'student.section'])
            ->join('students', 'students.id', '=', 'ssg_event_attendances.student_id')
            ->orderByDesc('ssg_event_attendances.is_present')
            ->orderByDesc('ssg_event_attendances.scanned_at')
            ->orderBy('students.last_name')
            ->orderBy('students.first_name')
            ->select('ssg_event_attendances.*');
    }

    private function ensureAttendanceRecords(SsgEvent $event)
    {
        // Get all active, pending, and account created students who don't have attendance records for this event
        $studentsWithoutAttendance = Student::whereIn('status', ['Active', 'Pending Student Account', 'Account Created'])
            ->whereDoesntHave('ssgAttendances', fn ($query) => $query->where('ssg_event_id', $event->id))
            ->get();

        // Create missing attendance records
        foreach ($studentsWithoutAttendance as $student) {
            SsgEventAttendance::firstOrCreate([
                'ssg_event_id' => $event->id,
                'student_id' => $student->id,
            ], [
                'is_present' => false,
                'scanned_at' => null,
                'applicable_fine' => $event->fine_amount,
                'actual_fine' => $event->fine_amount,
                'payment_status' => 'Unpaid',
            ]);
        }
    }
}
