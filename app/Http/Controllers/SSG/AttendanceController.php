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

        $student = Student::where('qr_code_value', $validated['qr_value'])->first();

        \Log::info('SSG Scanner - Primary Lookup', [
            'qr_value' => $validated['qr_value'],
            'found_by_qr_code_value' => $student ? true : false,
            'student_id' => $student ? $student->id : null,
        ]);

        // Fallback: Try to find student by student number extracted from QR value
        // This handles old students with uploaded QR codes that may have different formats
        if (! $student) {
            $studentNumber = $this->extractStudentNumberFromQR($validated['qr_value']);

            \Log::info('SSG Scanner - Fallback Extraction', [
                'qr_value' => $validated['qr_value'],
                'extracted_student_number' => $studentNumber,
            ]);

            if ($studentNumber) {
                $student = Student::where('student_number', $studentNumber)->first();

                \Log::info('SSG Scanner - Fallback Lookup', [
                    'extracted_student_number' => $studentNumber,
                    'found_by_student_number' => $student ? true : false,
                    'student_id' => $student ? $student->id : null,
                ]);
            }
        }

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
                'payment_status' => 'Unpaid',
            ]);
        }
    }

    private function extractStudentNumberFromQR(string $qrValue): ?string
    {
        // Try to extract student number from various QR code formats
        // Format 1: SITech-STUDENT|{student_number}|{uuid}
        if (preg_match('/SITech-STUDENT\|([^|]+)/', $qrValue, $matches)) {
            return $matches[1];
        }

        // Format 2: YYYYMMDD-{student_number} (old student QR format)
        if (preg_match('/^\d{8}-\d+$/', $qrValue)) {
            return $qrValue;
        }

        // Format 3: Just the student number (for old students with simple QR codes)
        if (preg_match('/^\d+$/', $qrValue)) {
            return $qrValue;
        }

        // Format 4: Any pipe-separated format where second part might be student number
        if (str_contains($qrValue, '|')) {
            $parts = explode('|', $qrValue);
            foreach ($parts as $part) {
                if (preg_match('/^\d+$/', trim($part))) {
                    return trim($part);
                }
            }
        }

        return null;
    }
}
