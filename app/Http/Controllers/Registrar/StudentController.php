<?php

namespace App\Http\Controllers\Registrar;

use App\Helpers\SchoolYearHelper;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\LegacyStudent;
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
use chillerlan\QRCode\QRCode as ChillerlanQRCode;
use chillerlan\QRCode\QROptions;

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
        $activeSchoolYear = SchoolYearHelper::getActive();
        return view('registrar.students.create', compact('yearLevels', 'sections', 'activeSchoolYear'));
    }

    public function store(Request $request)
    {
        $studentNumber = $request->student_number;
        
        // Check if student already exists
        $existingStudent = Student::where('student_number', $studentNumber)->first();

        if ($request->student_type === 'old') {
            // For old students, check if they exist in legacy records
            $legacyStudent = LegacyStudent::findByStudentNumber($studentNumber);
            
            // If old student but not found in either table, return error
            if (!$existingStudent && !$legacyStudent) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Student not found in the system. Please scan a valid QR code or check the student number.');
            }
        }

        $validated = $request->validate([
            'student_type'   => 'required|in:new,old',
            'student_number' => [
                'required',
                'string',
                'max:20',
                // For existing students, ignore their own ID
                // For new students, must be unique
                Rule::unique('students', 'student_number')->ignore($existingStudent?->id),
            ],
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
            'term'           => 'required|in:Term 1,Term 2,Term 3',
            'qr_code_file'   => 'nullable|file|mimes:png,jpg,jpeg,svg|max:2048',
        ], [
            'student_number.unique' => '❌ School ID already exists. This QR already belongs to another student.',
            'term.in' => '❌ Invalid term selected. Please select Term 1, Term 2, or Term 3.',
        ]);

        if ($existingStudent) {
            // CASE 1: Existing SIS Student - Re-enrollment
            return $this->handleReenrollment($existingStudent, $validated, $request);
        }

        // CASE 2: New Student or Legacy Student (from old student type)
        // Both paths are handled the same way - create a new student record
        $student = DB::transaction(function () use ($validated, $request) {
            $studentData = array_merge($validated, [
                'status' => 'Pending Student Account',
                'encoded_by' => auth()->id(),
                'encoded_at' => now(),
            ]);

            // Generate QR code
            $qrValue = 'SITech-STUDENT|' . $validated['student_number'] . '|' . (string) Str::uuid();
            $qrPath = 'qrcodes/' . $validated['student_number'] . '-' . Str::random(10) . '.svg';

            $qrWritten = Storage::disk('public')->put(
                $qrPath,
                QrCode::format('svg')->size(300)->generate($qrValue)
            );

            if (! $qrWritten) {
                throw new \RuntimeException('Unable to generate the student QR code.');
            }

            $studentData['qr_code_value'] = $qrValue;
            $studentData['qr_code_path'] = $qrPath;

            $student = Student::create($studentData);

            // Create enrollment history record
            $this->createEnrollmentRecord($student, $validated);

            // Create study load records
            $this->createStudyLoadRecords($student, $validated);

            // Create SSG attendance records
            $attendanceCount = $this->createSSGAttendanceRecords($student);

            $student->ssg_attendance_count = $attendanceCount;

            return $student->loadCount('studyLoads');
        });

        activity()
            ->causedBy(auth()->user())
            ->performedOn($student)
            ->log('Encoded student profile: ' . $student->last_name . ', ' . $student->first_name);

        if ($student->qr_code_path) {
            activity()
                ->causedBy(auth()->user())
                ->performedOn($student)
                ->log('Generated QR Code for Student: ' . $student->last_name . ', ' . $student->first_name);
        }

        $message = 'Student profile encoded successfully. ';
        $message .= 'QR code generated, ';
        $message .= $student->study_loads_count . ' study load record(s) created. ' . $student->ssg_attendance_count . ' SSG attendance record(s) prepared.';

        return redirect()->route('registrar.students')
            ->with('success', $message);
    }

    private function handleReenrollment(Student $student, array $validated, Request $request)
    {
        // Check if enrollment already exists for this school year/term
        $existingEnrollment = StudentEnrollment::where('student_id', $student->id)
            ->where('school_year', $validated['school_year'])
            ->where('term', $validated['term'])
            ->first();

        if ($existingEnrollment) {
            return redirect()->route('registrar.students.edit', $student)
                ->with('info', 'Student already has an enrollment for ' . $validated['school_year'] . ' ' . $validated['term'] . '. Please edit the existing enrollment.');
        }

        // Create new enrollment record
        DB::transaction(function () use ($student, $validated) {
            // Create enrollment history record
            $this->createEnrollmentRecord($student, $validated);

            // Update current student record fields for compatibility
            $student->update([
                'year_level_id' => $validated['year_level_id'],
                'section_id' => $validated['section_id'],
                'school_year' => $validated['school_year'],
                'status' => $student->user_id ? 'Account Created' : 'Pending Student Account',
            ]);

            // Create study load records for new enrollment
            $this->createStudyLoadRecords($student, $validated);
        });

        activity()
            ->causedBy(auth()->user())
            ->performedOn($student)
            ->log('Re-enrolled student: ' . $student->last_name . ', ' . $student->first_name . ' for ' . $validated['school_year'] . ' ' . $validated['term']);

        return redirect()->route('registrar.students')
            ->with('success', 'Student re-enrolled successfully for ' . $validated['school_year'] . ' ' . $validated['term'] . '. Existing account preserved.');
    }

    private function createEnrollmentRecord(Student $student, array $validated)
    {
        return StudentEnrollment::create([
            'student_id' => $student->id,
            'year_level_id' => $validated['year_level_id'],
            'section_id' => $validated['section_id'],
            'school_year' => $validated['school_year'],
            'term' => $validated['term'],
            'status' => $student->user_id ? 'Account Created' : 'Pending Student Account',
            'encoded_by' => auth()->id(),
            'encoded_at' => now(),
        ]);
    }

    private function createStudyLoadRecords(Student $student, array $validated)
    {
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
    }

    private function createSSGAttendanceRecords(Student $student)
    {
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
            ]);

            if ($attendance->wasRecentlyCreated) {
                $attendanceCount++;
            }
        }

        return $attendanceCount;
    }

    public function show(Student $student)
    {
        $student->load('enrollments.yearLevel', 'enrollments.section', 'enrollments.encoder');
        return view('registrar.students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $yearLevels = YearLevel::orderBy('level')->get();
        $sections   = Section::with('yearLevel')->get();
        $activeSchoolYear = SchoolYearHelper::getActive();
        return view('registrar.students.edit', compact('student', 'yearLevels', 'sections', 'activeSchoolYear'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'year_level_id'  => 'required|exists:year_levels,id',
            'section_id'     => 'required|exists:sections,id',
            'school_year'    => 'required|string',
            'term'           => 'required|in:Term 1,Term 2,Term 3',
        ]);

        // Fill the student with the request data but don't save yet
        $student->fill($request->except('_token', '_method'));

        // Check if any fields actually changed
        if (! $student->isDirty()) {
            return redirect()->route('registrar.students.edit', $student)
                ->with('info', 'No changes detected.');
        }

        // Save the changes
        $student->save();

        // Update or create enrollment record for the current school year/term
        $enrollment = StudentEnrollment::updateOrCreate(
            [
                'student_id' => $student->id,
                'school_year' => $request->school_year,
                'term' => $request->term,
            ],
            [
                'year_level_id' => $request->year_level_id,
                'section_id' => $request->section_id,
                'status' => $student->user_id ? 'Account Created' : 'Pending Student Account',
                'encoded_by' => auth()->id(),
                'encoded_at' => now(),
            ]
        );

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

    public function assignQR(Request $request, Student $student)
    {
        $request->validate([
            'qr_code_value' => 'required|string',
        ]);

        // Check if QR code already belongs to another student
        $existingStudent = Student::where('student_number', $request->qr_code_value)
            ->where('id', '!=', $student->id)
            ->first();

        if ($existingStudent) {
            return response()->json([
                'success' => false,
                'message' => 'This QR Code already belongs to another student.'
            ], 409);
        }

        // Generate QR image file with high quality (same as new students)
        $qrPath = 'qrcodes/' . $request->qr_code_value . '-assigned-' . Str::random(10) . '.svg';
        $qrWritten = Storage::disk('public')->put(
            $qrPath,
            QrCode::format('svg')->size(300)->generate($request->qr_code_value)
        );

        if (! $qrWritten) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to generate QR code image.'
            ], 500);
        }

        // Save the QR code value and path - also update student_number for consistency
        $student->student_number = $request->qr_code_value;
        $student->qr_code_value = $request->qr_code_value;
        $student->qr_code_path = $qrPath;
        $student->save();

        activity()
            ->causedBy(auth()->user())
            ->performedOn($student)
            ->log('Assigned existing QR Code to Student: ' . $student->last_name . ', ' . $student->first_name);

        return response()->json([
            'success' => true,
            'message' => 'Existing QR assigned successfully.'
        ]);
    }

    public function replaceQR(Request $request, Student $student)
    {
        $request->validate([
            'qr_code_file' => 'required|file|mimes:png,jpg,jpeg,svg|max:2048',
        ]);

        $file = $request->file('qr_code_file');

        // Decode the QR value from the uploaded image FIRST
        $qrValue = $this->decodeQRCode($file);

        if (! $qrValue) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to decode the QR code from the uploaded image. Please ensure the image contains a valid QR code.'
            ], 400);
        }

        // Check if the new QR value already belongs to another student
        $existingStudent = Student::where('student_number', $qrValue)
            ->where('id', '!=', $student->id)
            ->first();

        if ($existingStudent) {
            return response()->json([
                'success' => false,
                'message' => 'This QR code already belongs to another student.'
            ], 400);
        }

        // Delete old QR file if it exists
        if ($student->qr_code_path && Storage::disk('public')->exists($student->qr_code_path)) {
            Storage::disk('public')->delete($student->qr_code_path);
        }

        // REGENERATE QR code with high quality (same as new students)
        // instead of just uploading the original file
        $qrPath = 'qrcodes/' . $qrValue . '-replaced-' . Str::random(10) . '.svg';
        $qrWritten = Storage::disk('public')->put(
            $qrPath,
            QrCode::format('svg')->size(300)->generate($qrValue)
        );

        if (! $qrWritten) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to generate the QR code.'
            ], 500);
        }

        // Update student record - for old students, also update student_number to match QR
        $student->student_number = $qrValue;
        $student->qr_code_value = $qrValue;
        $student->qr_code_path = $qrPath;
        $student->save();

        activity()
            ->causedBy(auth()->user())
            ->performedOn($student)
            ->log('Replaced QR Code for Student: ' . $student->last_name . ', ' . $student->first_name);

        return response()->json([
            'success' => true,
            'message' => 'QR code replaced successfully.'
        ]);
    }

    public function decodeQR(Request $request)
    {
        $request->validate([
            'qr_code_file' => 'required|file|mimes:png,jpg,jpeg|max:2048',
        ]);

        if (!$request->hasFile('qr_code_file')) {
            return response()->json([
                'success' => false,
                'message' => 'No file uploaded.'
            ], 400);
        }

        $file = $request->file('qr_code_file');
        $decodedValue = $this->decodeQRCode($file);

        if ($decodedValue) {
            return response()->json([
                'success' => true,
                'decoded_value' => $decodedValue,
                'message' => 'QR Successfully Read'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Unable to read QR code. Please upload a valid QR image.'
            ], 400);
        }
    }

    private function decodeQRCode($file): ?string
    {
        try {
            $filePath = $file->getRealPath();
            $qrCode = new ChillerlanQRCode(new QROptions);
            $result = $qrCode->readFromFile($filePath);

            return $result ? $result->data : null;
        } catch (\Exception $e) {
            \Log::error('QR Code decode error', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
            ]);
            return null;
        }
    }

    public function lookupStudentQR(Request $request)
    {
        $request->validate([
            'student_number' => 'required|string|max:20',
        ]);

        $studentNumber = trim($request->student_number);

        // First check if student exists in current students table
        $existingStudent = Student::where('student_number', $studentNumber)->first();

        if ($existingStudent) {
            return response()->json([
                'success' => true,
                'student_type' => 'existing',
                'message' => 'Student already exists in the system.',
                'student' => [
                    'id' => $existingStudent->id,
                    'student_number' => $existingStudent->student_number,
                    'full_name' => $existingStudent->full_name,
                    'first_name' => $existingStudent->first_name,
                    'middle_name' => $existingStudent->middle_name,
                    'last_name' => $existingStudent->last_name,
                    'suffix' => $existingStudent->suffix,
                    'year_level_id' => $existingStudent->year_level_id,
                    'section_id' => $existingStudent->section_id,
                    'school_year' => $existingStudent->school_year,
                    'has_account' => !is_null($existingStudent->user_id),
                    'current_enrollment' => $existingStudent->load('enrollments')->enrollments->last(),
                ],
            ]);
        }

        // If not found in current table, check legacy students
        $legacyStudent = LegacyStudent::findByStudentNumber($studentNumber);

        if ($legacyStudent) {
            return response()->json([
                'success' => true,
                'student_type' => 'legacy',
                'message' => 'Student found in old-student master records.',
                'student' => [
                    'student_number' => $legacyStudent->student_number,
                    'full_name' => $legacyStudent->full_name,
                    'first_name' => $legacyStudent->first_name,
                    'middle_name' => $legacyStudent->middle_name,
                    'last_name' => $legacyStudent->last_name,
                    'middle_initial' => $legacyStudent->middle_initial,
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Student not found in current or legacy records.',
        ], 404);
    }

    public function lookupLegacyQR(Request $request)
    {
        $request->validate([
            'student_number' => 'required|string|max:20',
        ]);

        $studentNumber = trim($request->student_number);

        // Lookup in legacy_students table
        $legacyStudent = LegacyStudent::findByStudentNumber($studentNumber);

        if (!$legacyStudent) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found in the old-student master records.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'student' => [
                'student_number' => $legacyStudent->student_number,
                'full_name' => $legacyStudent->full_name,
                'first_name' => $legacyStudent->first_name,
                'middle_name' => $legacyStudent->middle_name,
                'last_name' => $legacyStudent->last_name,
                'middle_initial' => $legacyStudent->middle_initial,
            ],
        ]);
    }
}
