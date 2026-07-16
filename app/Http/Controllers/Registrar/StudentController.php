<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\YearLevel;
use App\Models\Section;
use App\Models\User;
use App\Models\ClassSchedule;
use App\Models\StudyLoad;
use App\Models\SsgEvent;
use App\Models\SsgEventAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with(['yearLevel', 'section'])
            ->latest()->paginate(15);
        return view('registrar.students.index', compact('students'));
    }

    public function create()
    {
        $yearLevels = YearLevel::orderBy('level')->get();
        $sections   = Section::with('yearLevel')->get();
        $users      = User::role('Student')
            ->whereDoesntHave('student')
            ->get();
        return view('registrar.students.create', compact('yearLevels', 'sections', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'        => 'nullable|exists:users,id',
            'student_type'   => 'required|in:new,old',
            'student_number' => 'required|string|max:20|unique:students,student_number',
            'first_name'     => 'required|string|max:100',
            'middle_name'    => 'nullable|string|max:100',
            'last_name'      => 'required|string|max:100',
            'suffix'         => 'nullable|string|max:10',
            'date_of_birth'  => 'nullable|date',
            'gender'         => 'nullable|in:Male,Female,Other',
            'address'        => 'nullable|string',
            'contact_number' => 'nullable|string|max:20',
            'email'          => 'nullable|email',
            'guardian_name'  => 'nullable|string|max:100',
            'guardian_contact' => 'nullable|string|max:20',
            'guardian_relationship' => 'nullable|string|max:50',
            'year_level_id'  => 'required|exists:year_levels,id',
            'section_id'     => [
                'required',
                Rule::exists('sections', 'id')->where(fn ($query) => $query->where('year_level_id', $request->year_level_id)),
            ],
            'school_year'    => 'required|string|max:20',
        ]);

        if (! empty($validated['user_id']) && ! User::role('Student')->whereKey($validated['user_id'])->whereDoesntHave('student')->exists()) {
            throw ValidationException::withMessages([
                'user_id' => 'Select an available Student account that is not yet linked to another profile.',
            ]);
        }

        $student = DB::transaction(function () use ($validated) {
            $studentData = array_merge($validated, [
                'status' => 'Pending Student Account',
                'encoded_by' => auth()->id(),
                'encoded_at' => now(),
            ]);

            // Only generate QR code for new students
            if ($validated['student_type'] === 'new') {
                $qrValue = 'SITech-STUDENT|' . $validated['student_number'] . '|' . (string) Str::uuid();
                $qrPath = 'qrcodes/' . $validated['student_number'] . '-' . Str::random(10) . '.svg';

                $qrWritten = Storage::disk('public')->put(
                    $qrPath,
                    QrCode::format('svg')->size(200)->generate($qrValue)
                );

                if (! $qrWritten) {
                    throw new \RuntimeException('Unable to generate the student QR code.');
                }

                $studentData['qr_code_value'] = $qrValue;
                $studentData['qr_code_path'] = $qrPath;
            }

            $student = Student::create($studentData);

            $sectionSchedules = ClassSchedule::where('section_id', $validated['section_id'])
                ->where('school_year', $validated['school_year'])
                ->get();

            foreach ($sectionSchedules as $schedule) {
                StudyLoad::firstOrCreate([
                    'student_id' => $student->id,
                    'class_schedule_id' => $schedule->id,
                    'school_year' => $validated['school_year'],
                ]);
            }

            // Create SSG attendance records for all existing events
            $ssgEvents = SsgEvent::all();
            $attendanceCount = 0;

            foreach ($ssgEvents as $event) {
                $attendance = SsgEventAttendance::firstOrCreate([
                    'ssg_event_id' => $event->id,
                    'student_id' => $student->id,
                ], [
                    'is_present' => false,
                    'scanned_at' => null,
                    'applicable_fine' => $event->fine_amount,
                    'actual_fine' => $event->fine_amount,
                    'payment_status' => 'Unpaid',
                ]);

                if ($attendance->wasRecentlyCreated) {
                    $attendanceCount++;
                }
            }

            $student->ssg_attendance_count = $attendanceCount;

            return $student->loadCount('studyLoads');
        });

        activity()
            ->causedBy(auth()->user())
            ->performedOn($student)
            ->log('Encoded student profile: ' . $student->last_name . ', ' . $student->first_name);

        // Log QR code generation if applicable
        if ($validated['student_type'] === 'new' && $student->qr_code_path) {
            activity()
                ->causedBy(auth()->user())
                ->performedOn($student)
                ->log('Generated QR Code for Student: ' . $student->last_name . ', ' . $student->first_name);
        }

        $message = 'Student profile encoded successfully. ';
        if ($validated['student_type'] === 'new') {
            $message .= 'QR code generated, ';
        }
        $message .= $student->study_loads_count . ' study load record(s) created. ' . $student->ssg_attendance_count . ' SSG attendance record(s) prepared.';

        return redirect()->route('registrar.students')
            ->with('success', $message);
    }

    public function edit(Student $student)
    {
        $yearLevels = YearLevel::orderBy('level')->get();
        $sections   = Section::with('yearLevel')->get();
        return view('registrar.students.edit', compact('student', 'yearLevels', 'sections'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'year_level_id'  => 'required|exists:year_levels,id',
            'section_id'     => 'required|exists:sections,id',
            'school_year'    => 'required|string',
        ]);

        $student->update($request->except('_token', '_method'));

        activity()
            ->causedBy(auth()->user())
            ->performedOn($student)
            ->log('Updated student profile: ' . $student->last_name . ', ' . $student->first_name);

        return redirect()->route('registrar.students')
            ->with('success', 'Student profile updated successfully.');
    }

    public function printClassSchedule(Student $student)
    {
        $student->load(['section.yearLevel', 'yearLevel']);
        
        // Get schedules from section (inherited by student)
        $schedules = ClassSchedule::with(['subject', 'teacher'])
            ->where('section_id', $student->section_id)
            ->where('school_year', request('school_year', $student->school_year))
            ->orderBy('time_start')
            ->get();

        $schoolName = 'SALUS INSTITUTE OF TECHNOLOGY';
        $schoolLogo = asset('images/salus-logo.png');
        $generatedBy = auth()->user()->name;
        $dateGenerated = now()->format('F d, Y g:i A');

        // Return partial content for modal preview
        if (request()->ajax() || request()->has('section_id')) {
            return view('registrar.students.print-class-schedule-partial', compact(
                'student', 'schedules', 'schoolName', 'schoolLogo', 
                'generatedBy', 'dateGenerated'
            ));
        }

        return view('registrar.students.print-class-schedule', compact(
            'student', 'schedules', 'schoolName', 'schoolLogo', 
            'generatedBy', 'dateGenerated'
        ));
    }
}
