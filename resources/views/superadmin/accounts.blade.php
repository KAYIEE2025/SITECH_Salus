@extends('layouts.app')

@section('title', 'Manage Accounts')

<!-- @section('sidebar-links')
    <x-nav-link href="{{ route('superadmin.dashboard') }}" :active="false">Dashboard</x-nav-link>
    <x-nav-link href="{{ route('superadmin.accounts') }}" :active="true">Manage Accounts</x-nav-link>
    <x-nav-link href="{{ route('superadmin.activity-logs') }}" :active="false">Activity Logs</x-nav-link>
    <x-nav-link href="{{ route('superadmin.profile') }}" :active="false">My Profile</x-nav-link>
@endsection -->

@section('content')

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg px-4 py-3 mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3 mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-3 gap-6">

        {{-- Create Account Form --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-4">Create Account</h2>
            <form method="POST" action="{{ route('superadmin.accounts.store') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select name="role"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                        <option value="">Select role...</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                </div>
                <button type="submit"
                    class="w-full bg-green-800 hover:bg-green-900 text-white text-sm font-semibold py-2.5 rounded-lg transition">
                    Create Account
                </button>
            </form>
        </div>

        {{-- Accounts Table --}}
        <div class="col-span-2 bg-white rounded-xl border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-800">All Accounts</h2>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs">
                    <tr>
                        <th class="text-left px-6 py-3">Name</th>
                        <th class="text-left px-6 py-3">Email</th>
                        <th class="text-left px-6 py-3">Role</th>
                        <th class="text-left px-6 py-3">Status</th>
                        <th class="text-left px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 font-medium text-gray-800">{{ $user->name }}</td>
                        <td class="px-6 py-3 text-gray-500">{{ $user->email }}</td>
                        <td class="px-6 py-3">
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-1 rounded-full">
                                {{ $user->getRoleNames()->first() ?? '—' }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <form method="POST" action="{{ route('superadmin.accounts.toggle', $user) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="text-xs px-2.5 py-1 rounded-full
                                    {{ $user->is_active
                                        ? 'bg-green-50 text-green-700 hover:bg-green-100'
                                        : 'bg-red-50 text-red-600 hover:bg-red-100' }}">
                                    {{ $user->is_active ? '● Active' : '● Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-3">
                            <div class="flex gap-2">
                                @if(!$user->hasRole('Super Admin'))
                                    <a href="{{ route('superadmin.accounts.edit', $user) }}"
                                        class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg transition">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('superadmin.accounts.destroy', $user) }}"
                                        onsubmit="return confirm('Delete this account?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="text-xs bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-lg transition">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

@endsection