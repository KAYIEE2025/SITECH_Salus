<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $student = Student::where('user_id', auth()->id())->first();

        if (!$student) {
            return view('student.profile', [
                'student' => null,
            ]);
        }

        return view('student.profile', compact('student'));
    }

    public function updateContact(Request $request)
    {
        $request->validate([
            'contact_number' => 'required|string|max:15|regex:/^09[0-9]{9}$/',
        ]);

        $student = Student::where('user_id', auth()->id())->first();

        if (!$student) {
            return back()->with('error', 'Student profile not found.');
        }

        $student->update([
            'contact_number' => $request->contact_number,
        ]);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($student)
            ->log('Student updated contact number.');

        return back()->with('success', 'Contact number updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        // Update password without changing must_change_password flag
        // (this is for voluntary password changes, not forced first-login changes)
        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        activity()
            ->causedBy(auth()->user())
            ->log('Student changed password.');

        return back()->with('success', 'Password changed successfully.');
    }
}
