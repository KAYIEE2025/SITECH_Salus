<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SsgEventAttendance;
use App\Models\Student;
use Illuminate\Http\Request;

class SSGEventController extends Controller
{
    public function index(Request $request)
    {
        $student = Student::where('user_id', auth()->id())->first();

        if (!$student) {
            return view('student.ssg-events', [
                'student' => null,
                'attendances' => collect(),
                'totalEvents' => 0,
                'eventsAttended' => 0,
                'eventsMissed' => 0,
                'outstandingBalance' => 0,
            ]);
        }

        // Get student's SSG event attendances
        $attendances = SsgEventAttendance::with('ssgEvent')
            ->where('student_id', $student->id)
            ->orderByDesc('created_at')
            ->get();

        // Calculate summary statistics
        $totalEvents = $attendances->count();
        $eventsAttended = $attendances->where('is_present', true)->count();
        $eventsMissed = $attendances->where('is_present', false)->count();
        $outstandingBalance = $attendances->where('payment_status', 'unpaid')
            ->where('is_present', false)
            ->sum('actual_fine');

        return view('student.ssg-events', compact(
            'student',
            'attendances',
            'totalEvents',
            'eventsAttended',
            'eventsMissed',
            'outstandingBalance'
        ));
    }
}
