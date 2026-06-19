<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'contact_number' => 'nullable|string|max:20',
        ]);

        auth()->user()->update([
            'name'           => $request->name,
            'email'          => $request->email,
            'contact_number' => $request->contact_number,
        ]);

        activity()
            ->causedBy(auth()->user())
            ->log('Updated their profile');

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        activity()
            ->causedBy(auth()->user())
            ->log('Changed their password');

        return back()->with('success', 'Password changed successfully.');
    }
}