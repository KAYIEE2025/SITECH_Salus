<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentAccountController extends Controller
{
    public function index()
    {
        $pendingStudents = Student::with(['yearLevel', 'section'])
            ->whereNull('user_id')
            ->where('status', 'Pending Student Account')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(15);

        return view('superadmin.student-accounts.index', compact('pendingStudents'));
    }

    public function store(Student $student)
    {
        if ($student->user_id || $student->status !== 'Pending Student Account') {
            return back()->with('error', 'This student already has an account or is not pending account creation.');
        }

        $credential = DB::transaction(fn () => $this->createStudentAccount($student));

        return back()
            ->with('success', 'Student account created successfully.')
            ->with('generatedCredentials', [$credential]);
    }

    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'integer|exists:students,id',
        ]);

        $students = Student::whereIn('id', $validated['student_ids'])
            ->whereNull('user_id')
            ->where('status', 'Pending Student Account')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        if ($students->isEmpty()) {
            return back()->with('error', 'No pending students were selected.');
        }

        $credentials = DB::transaction(function () use ($students) {
            return $students->map(fn (Student $student) => $this->createStudentAccount($student))->all();
        });

        return back()
            ->with('success', count($credentials) . ' student account(s) created successfully.')
            ->with('generatedCredentials', $credentials);
    }

    private function createStudentAccount(Student $student): array
    {
        $temporaryPassword = $this->generateTemporaryPassword($student->first_name, $student->student_number);
        $username = $this->uniqueUsername($student->student_number);
        $email = $this->uniqueEmailFor($student);

        $user = User::create([
            'name' => $student->first_name . ' ' . $student->last_name,
            'username' => $username,
            'email' => $email,
            'password' => Hash::make($temporaryPassword),
            'contact_number' => $student->contact_number,
            'is_active' => true,
            'must_change_password' => true,
        ]);

        $user->assignRole('Student');

        $student->update([
            'user_id' => $user->id,
            'status' => 'Account Created',
        ]);

        activity()
            ->event('created')
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->log('Created student account for ' . $student->last_name . ', ' . $student->first_name);

        return [
            'student_name' => $student->last_name . ', ' . $student->first_name,
            'student_number' => $student->student_number,
            'username' => $username,
            'email' => $email,
            'temporary_password' => $temporaryPassword,
        ];
    }

    private function uniqueUsername(string $studentNumber): string
    {
        // Extract the last 6 digits of the student number
        $base = substr($studentNumber, -6);
        $username = $base;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base . '-' . $counter;
            $counter++;
        }

        return $username;
    }

    private function uniqueEmailFor(Student $student): string
    {
        $email = $student->email;

        if ($email && ! User::where('email', $email)->exists()) {
            return $email;
        }

        $base = Str::lower(Str::slug($student->student_number, ''));
        $email = $base . '@students.local';
        $counter = 1;

        while (User::where('email', $email)->exists()) {
            $email = $base . $counter . '@students.local';
            $counter++;
        }

        return $email;
    }

    private function generateTemporaryPassword(string $firstName, string $studentNumber): string
    {
        // Get the first name only (in case it contains spaces)
        $firstName = trim(explode(' ', $firstName)[0]);
        
        // Take the first three letters (or entire name if fewer than 3 letters)
        $firstThreeLetters = mb_substr($firstName, 0, 3);
        
        // Convert to uppercase
        $firstThreeLetters = strtoupper($firstThreeLetters);
        
        // Extract the last 6 digits of the student number
        $lastSixDigits = substr($studentNumber, -6);
        
        // Combine with underscore and last 6 digits of student number
        return $firstThreeLetters . '_' . $lastSixDigits;
    }
}
