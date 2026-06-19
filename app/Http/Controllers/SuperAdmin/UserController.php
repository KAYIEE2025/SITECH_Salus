<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->latest()->get();
        $roles = Role::whereNotIn('name', ['Super Admin'])->get();
        return view('superadmin.accounts', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->log('Created account for ' . $user->name . ' (' . $request->role . ')');

        return back()->with('success', 'Account created successfully.');
    }

    public function edit(User $user)
    {
        $roles = Role::whereNotIn('name', ['Super Admin'])->get();
        return view('superadmin.accounts-edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|exists:roles,name',
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $user->update(['password' => Hash::make($request->password)]);
        }

        $user->syncRoles($request->role);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->log('Updated account for ' . $user->name);

        return redirect()->route('superadmin.accounts')
            ->with('success', 'Account updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->hasRole('Super Admin')) {
            return back()->with('error', 'Cannot delete Super Admin account.');
        }

        activity()
            ->causedBy(auth()->user())
            ->log('Deleted account: ' . $user->name . ' (' . $user->getRoleNames()->first() . ')');

        $user->delete();
        return back()->with('success', 'Account deleted successfully.');
    }

    public function toggleActive(User $user)
    {
        if ($user->hasRole('Super Admin')) {
            return back()->with('error', 'Cannot deactivate Super Admin account.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->log('Account ' . $status . ': ' . $user->name);

        return back()->with('success', 'Account status updated.');
    }
}