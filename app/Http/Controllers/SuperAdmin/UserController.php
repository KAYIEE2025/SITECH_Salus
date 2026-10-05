<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::orderBy('name')->get();
        $assignableRoles = $roles->whereNotIn('name', ['SSG', 'Student']);

        $users = User::with('roles')
            ->when($request->filled('role'), function ($query) use ($request) {
                $query->role($request->string('role')->toString());
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('is_active', $request->string('status')->toString() === 'active');
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('contact_number', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $roleCounts = $roles->mapWithKeys(fn ($role) => [
            $role->name => User::role($role->name)->count(),
        ]);

        $activeUsers = User::where('is_active', true)->count();
        $inactiveUsers = User::where('is_active', false)->count();

        return view('superadmin.accounts', compact(
            'users',
            'roles',
            'assignableRoles',
            'roleCounts',
            'activeUsers',
            'inactiveUsers'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required|exists:roles,name|not_in:SSG,Student',
            'contact_number' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'contact_number' => $request->contact_number,
            'is_active' => true,
        ]);

        $user->assignRole($request->role);

        activity()
            ->event('created')
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->log('Created account for ' . $user->name . ' (' . $request->role . ')');

        return back()->with('success', 'Account created successfully.');
    }

    public function edit(User $user)
    {
        return view('superadmin.accounts-edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'contact_number' => 'nullable|string|max:20',
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
            'contact_number' => $request->contact_number,
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $user->update(['password' => Hash::make($request->password)]);
        }

        // Note: Role changes are handled exclusively through User Role Management
        // The edit form does not include role selection, and role is not updated here

        activity()
            ->event('updated')
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->log('Updated account for ' . $user->name);

        return redirect()->route('superadmin.accounts')
            ->with('success', 'Account updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === 1) {
            return back()->with('error', 'Cannot delete the original system Super Admin account.');
        }

        activity()
            ->event('deleted')
            ->causedBy(auth()->user())
            ->log('Deleted account: ' . $user->name . ' (' . $user->getRoleNames()->first() . ')');

        $user->delete();
        return back()->with('success', 'Account deleted successfully.');
    }

    public function toggleActive(User $user)
    {
        if ($user->id === 1) {
            return back()->with('error', 'Cannot deactivate the original system Super Admin account.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        activity()
            ->event($status)
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->log('Account ' . $status . ': ' . $user->name);

        return back()->with('success', 'Account status updated.');
    }
}
