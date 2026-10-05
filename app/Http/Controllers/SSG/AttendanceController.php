<?php

namespace App\Http\Controllers\SSG;

use App\Http\Controllers\Controller;
use App\Models\SsgEvent;
use App\Models\SsgEventAttendance;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

        // TEMPORARY LOGGING: Log exact QR value received from scanner
        \Log::info('SSG Scanner - QR Value Received', [
            'qr_value' => $validated['qr_value'],
            'qr_value_length' => strlen($validated['qr_value']),
        ]);

        // COMPREHENSIVE LOGGING: Log entire scan process
        \Log::info('SSG Scanner - Scan Started', [
            'qr_value' => $validated['qr_value'],
            'qr_value_length' => strlen($validated['qr_value']),
            'event_id' => $validated['event_id'],
        ]);

        $event = SsgEvent::findOrFail($validated['event_id']);

        // Use computed status (automatic based on event times)
        if ($event->status !== 'Ongoing') {
            \Log::info('SSG Scanner - Event Not Ongoing', [
                'event_status' => $event->status,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Attendance Closed.',
            ]);
        }

        // Validate scan time window
        $now = Carbon::now('Asia/Manila');
        $eventDate = $event->event_date;

        \Log::info('SSG Scanner - Time Check', [
            'now' => $now->format('Y-m-d H:i:s'),
            'event_date' => $eventDate->format('Y-m-d'),
            'scan_start_time' => $event->scan_start_time,
            'scan_end_time' => $event->scan_end_time,
        ]);

        if ($event->scan_start_time) {
            $startTime = Carbon::parse($eventDate->format('Y-m-d') . ' ' . $event->scan_start_time, 'Asia/Manila');
            if ($now->lt($startTime)) {
                \Log::info('SSG Scanner - Too Early', [
                    'now' => $now->format('Y-m-d H:i:s'),
                    'start_time' => $startTime->format('Y-m-d H:i:s'),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Attendance Closed.',
                ]);
            }
        }

        if ($event->scan_end_time) {
            $endTime = Carbon::parse($eventDate->format('Y-m-d') . ' ' . $event->scan_end_time, 'Asia/Manila');
            if ($now->gt($endTime)) {
                \Log::info('SSG Scanner - Too Late', [
                    'now' => $now->format('Y-m-d H:i:s'),
                    'end_time' => $endTime->format('Y-m-d H:i:s'),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Attendance Closed.',
                ]);
            }
        }

        // QR codes contain only the student number - direct lookup
        $student = Student::where('student_number', $validated['qr_value'])->first();

        \Log::info('SSG Scanner - Student Lookup', [
            'qr_value' => $validated['qr_value'],
            'found' => $student ? true : false,
            'student_id' => $student ? $student->id : null,
        ]);

        if (! $student) {
            \Log::info('SSG Scanner - Student Not Found', [
                'qr_value' => $validated['qr_value'],
            ]);
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
            'scanned_by_user_id' => auth()->id(),
            'actual_fine' => 0,
        ]);

        activity()
            ->event('qr_attendance_scan')
            ->causedBy(auth()->user())
            ->performedOn($attendance)
            ->log('QR attendance scan recorded for ' . $student->full_name . ' (' . $student->student_number . ') at event: ' . $event->title);

        return response()->json([
            'success' => true,
            'student_name' => $student->full_name,
            'student_number' => $student->student_number,
            'student_photo' => $student->photo_path ? asset($student->photo_path) : null,
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

    public function manualEntry(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'event_id' => ['required', 'exists:ssg_events,id'],
            'last_6_digits' => ['required', 'digits:6'],
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

        // Search for students by last 6 digits of student_number
        $last6Digits = $validated['last_6_digits'];
        $students = Student::whereRaw('RIGHT(student_number, 6) = ?', [$last6Digits])
            ->get();

        \Log::info('SSG Manual Entry - Search Results', [
            'last_6_digits' => $last6Digits,
            'students_found' => $students->count(),
        ]);

        if ($students->count() === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found.',
            ]);
        }

        if ($students->count() > 1) {
            // Multiple students found - return list for selection
            return response()->json([
                'success' => false,
                'requires_selection' => true,
                'message' => 'Multiple students found. Please select the correct student.',
                'students' => $students->map(function ($student) {
                    return [
                        'id' => $student->id,
                        'student_number' => $student->student_number,
                        'full_name' => $student->full_name,
                        'year_level' => $student->yearLevel?->name ?? 'N/A',
                        'section' => $student->section?->name ?? 'N/A',
                    ];
                }),
            ]);
        }

        // Exactly one student found - proceed with attendance recording
        $student = $students->first();

        return $this->recordAttendance($event, $student);
    }

    public function manualEntryWithFullId(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'event_id' => ['required', 'exists:ssg_events,id'],
            'student_id' => ['required', 'exists:students,id'],
        ]);

        $event = SsgEvent::findOrFail($validated['event_id']);
        $student = Student::findOrFail($validated['student_id']);

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

        return $this->recordAttendance($event, $student);
    }

    private function recordAttendance(SsgEvent $event, Student $student): JsonResponse
    {
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
            'scanned_by_user_id' => auth()->id(),
            'actual_fine' => 0,
        ]);

        activity()
            ->event('manual_attendance_entry')
            ->causedBy(auth()->user())
            ->performedOn($attendance)
            ->log('Manual attendance entry recorded for ' . $student->full_name . ' (' . $student->student_number . ') at event: ' . $event->title);

        return response()->json([
            'success' => true,
            'student_name' => $student->full_name,
            'student_number' => $student->student_number,
            'student_photo' => $student->photo_path ? asset($student->photo_path) : null,
            'scan_time' => now()->format('h:i A'),
            'attendance_status' => 'Present',
            'fine_status' => 'Waived',
            'message' => 'Attendance Recorded',
        ]);
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

        // Log the extend time action using the existing Activity Log system
        activity()
            ->event('attendance_extended')
            ->causedBy(auth()->user())
            ->performedOn($event)
            ->log("Extended attendance time for event '{$event->title}' until {$newEndTime}. Reason: {$reason}");

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
            ]);
        }
    }
}
