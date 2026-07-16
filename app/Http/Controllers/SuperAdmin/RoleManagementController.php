<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleManagementController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 25);
        $validPerPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 25;

        $users = User::with('roles')
            ->orderBy('name')
            ->paginate($validPerPage);

        $roles = Role::orderBy('name')->get();

        return view('superadmin.role-management', compact('users', 'roles'));
    }

    public function getUserRoles(User $user)
    {
        return response()->json([
            'roles' => $user->getRoleNames()->toArray(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
        ]);

        // Protect the original system Super Admin account (user ID 1)
        $isProtectedUser = $user->id === 1;

        // If user is the protected Super Admin, ensure Super Admin role is always included
        if ($isProtectedUser && $user->hasRole('Super Admin')) {
            $roles = $request->roles ?? [];
            if (!in_array('Super Admin', $roles)) {
                $roles[] = 'Super Admin';
            }
            $user->syncRoles($roles);
        } else {
            // Sync roles using Spatie's syncRoles method
            $user->syncRoles($request->roles ?? []);
        }

        activity()
            ->event('roles_updated')
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->log('Updated roles for ' . $user->name . ': ' . $user->getRoleNames()->implode(', '));

        return back()->with('success', 'Roles updated successfully for ' . $user->name);
    }
}
