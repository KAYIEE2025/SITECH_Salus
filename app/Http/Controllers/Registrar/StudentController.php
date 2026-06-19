<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\YearLevel;
use App\Models\Section;
use App\Models\User;
use App\Models\ClassSchedule;
use App\Models\StudyLoad;
use Illuminate\Http\Request;
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
        $request->validate([
            'user_id'        => 'nullable|exists:users,id',
            'student_number' => 'required|unique:students,student_number',
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
            'section_id'     => 'required|exists:sections,id',
            'school_year'    => 'required|string',
            'semester'       => 'required|in:1st,2nd,Summer',
            'qr_code_value'  => 'nullable|string',
        ]);

        $data = $request->except('_token');
        $data['encoded_by'] = auth()->id();
        $data['encoded_at'] = now();

        if (empty($request->qr_code_value)) {
            $qrValue = $request->student_number;
            $qrPath  = 'qrcodes/' . $request->student_number . '.svg';
            QrCode::size(200)->generate($qrValue, public_path('storage/' . $qrPath));
            $data['qr_code_value'] = $qrValue;
            $data['qr_code_path']  = $qrPath;
        }

        $student = Student::create($data);

        // Auto-assign study load based on section
        $sectionSchedules = ClassSchedule::where('section_id', $request->section_id)
            ->where('school_year', $request->school_year)
            ->where('semester', $request->semester)
            ->get();

        foreach ($sectionSchedules as $schedule) {
            StudyLoad::firstOrCreate([
                'student_id'        => $student->id,
                'class_schedule_id' => $schedule->id,
                'school_year'       => $request->school_year,
                'semester'          => $request->semester,
            ]);
        }

        activity()
            ->causedBy(auth()->user())
            ->performedOn($student)
            ->log('Encoded student profile: ' . $student->last_name . ', ' . $student->first_name);

        return redirect()->route('registrar.students')
            ->with('success', 'Student profile encoded successfully.');
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
            'semester'       => 'required|in:1st,2nd,Summer',
        ]);

        $student->update($request->except('_token', '_method'));

        activity()
            ->causedBy(auth()->user())
            ->performedOn($student)
            ->log('Updated student profile: ' . $student->last_name . ', ' . $student->first_name);

        return redirect()->route('registrar.students')
            ->with('success', 'Student profile updated successfully.');
    }
}
