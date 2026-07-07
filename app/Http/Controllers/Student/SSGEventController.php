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
                'filters' => [
                    'school_year' => $request->school_year ?? '',
                    'attendance_status' => $request->attendance_status ?? '',
                    'payment_status' => $request->payment_status ?? '',
                    'search' => $request->search ?? '',
                ],
            ]);
        }

        // Build query for student's SSG event attendances
        $query = SsgEventAttendance::with('ssgEvent')
            ->where('student_id', $student->id);

        // Apply filters
        if ($request->filled('school_year')) {
            $query->whereHas('ssgEvent', function ($q) use ($request) {
                $q->where('school_year', $request->school_year);
            });
        }

        if ($request->filled('attendance_status')) {
            if ($request->attendance_status === 'present') {
                $query->where('is_present', true);
            } elseif ($request->attendance_status === 'absent') {
                $query->where('is_present', false);
            }
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('ssgEvent', function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%");
            });
        }

        $attendances = $query->orderByDesc('created_at')->get();

        // Calculate summary statistics
        $totalEvents = $attendances->count();
        $eventsAttended = $attendances->where('is_present', true)->count();
        $eventsMissed = $attendances->where('is_present', false)->count();
        $outstandingBalance = $attendances->where('payment_status', 'unpaid')
            ->where('is_present', false)
            ->sum('actual_fine');

        $filters = [
            'school_year' => $request->school_year ?? '',
            'attendance_status' => $request->attendance_status ?? '',
            'payment_status' => $request->payment_status ?? '',
            'search' => $request->search ?? '',
        ];

        return view('student.ssg-events', compact(
            'student',
            'attendances',
            'totalEvents',
            'eventsAttended',
            'eventsMissed',
            'outstandingBalance',
            'filters'
        ));
    }
}
