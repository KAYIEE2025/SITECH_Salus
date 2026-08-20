<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ForcedPasswordChangeController extends Controller
{
    /**
     * Show the forced password change form.
     */
    public function index()
    {
        return view('student.forced-password-change');
    }

    /**
     * Handle the forced password change submission.
     */
    public function store(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        // Update password and reset the flag
        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
        ]);

        activity()
            ->causedBy($user)
            ->log('Student changed password (forced change on first login).');

        return redirect()->route('student.dashboard')
            ->with('success', 'Password changed successfully. Welcome to the Student Portal.');
    }
}
