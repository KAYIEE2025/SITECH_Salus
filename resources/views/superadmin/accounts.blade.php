@extends('layouts.app')

@section('title', 'Manage Accounts')

@section('content')
    <x-delete-confirm-modal
        name="delete-user-modal"
        title="Delete Account"
        message="Are you sure you want to delete this account?"
        recordName=""
        recordDetails=""
    />
    @session('success')
        <div class="mb-4 md:mb-6 rounded-lg border border-green-200 bg-green-50 px-3 py-2 md:px-4 md:py-3 text-xs md:text-sm text-green-800">
            {{ $value }}
        </div>
    @endsession

    @if(session('error'))
        <div class="mb-4 md:mb-6 rounded-lg border border-red-200 bg-red-50 px-3 py-2 md:px-4 md:py-3 text-xs md:text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

        <div class="mb-4 md:mb-6 grid grid-cols-1 gap-3 md:gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="sa-card p-3 md:p-5">
            <p class="text-xs md:text-sm text-gray-500">Total Accounts</p>
            <p class="mt-1 text-2xl md:text-3xl font-bold text-[#1a5c1a]">{{ $activeUsers + $inactiveUsers }}</p>
        </div>
        <div class="sa-card p-3 md:p-5">
            <p class="text-xs md:text-sm text-gray-500">Active</p>
            <p class="mt-1 text-2xl md:text-3xl font-bold text-[#1a5c1a]">{{ $activeUsers }}</p>
        </div>
        <div class="sa-card p-3 md:p-5">
            <p class="text-xs md:text-sm text-gray-500">Inactive</p>
            <p class="mt-1 text-2xl md:text-3xl font-bold text-red-600">{{ $inactiveUsers }}</p>
        </div>
        <div class="sa-card p-3 md:p-5">
            <p class="text-xs md:text-sm text-gray-500">Students</p>
            <p class="mt-1 text-2xl md:text-3xl font-bold text-[#c8a000]">{{ $roleCounts['Student'] ?? 0 }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:gap-6 xl:grid-cols-3">
        <div class="sa-card p-4 md:p-6">
            <h2 class="mb-3 md:mb-4 text-base font-semibold text-gray-800">Create Account</h2>

            <form method="POST" action="{{ route('superadmin.accounts.store') }}" class="space-y-3 md:space-y-4">
                @csrf

                <div>
                    <label for="name" class="mb-1 block text-xs md:text-sm font-medium text-gray-700">Full Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required
                        class="w-full rounded-lg border border-gray-300 px-2 py-1.5 md:px-3 md:py-2 text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5c1a]">
                    @error('name') <p class="mt-1 text-[10px] md:text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="mb-1 block text-xs md:text-sm font-medium text-gray-700">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                        class="w-full rounded-lg border border-gray-300 px-2 py-1.5 md:px-3 md:py-2 text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5c1a]">
                    @error('email') <p class="mt-1 text-[10px] md:text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="contact_number" class="mb-1 block text-xs md:text-sm font-medium text-gray-700">Contact Number</label>
                    <input id="contact_number" type="text" name="contact_number" value="{{ old('contact_number') }}"
                        class="w-full rounded-lg border border-gray-300 px-2 py-1.5 md:px-3 md:py-2 text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5c1a]">
                    @error('contact_number') <p class="mt-1 text-[10px] md:text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="role" class="mb-1 block text-xs md:text-sm font-medium text-gray-700">Role</label>
                    <select id="role" name="role" required
                        class="w-full rounded-lg border border-gray-300 px-2 py-1.5 md:px-3 md:py-2 text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5c1a]">
                        <option value="">Select role</option>
                        @foreach($assignableRoles as $role)
                            <option value="{{ $role->name }}" @selected(old('role') === $role->name)>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('role') <p class="mt-1 text-[10px] md:text-xs text-red-500">{{ $message }}</p> @enderror
                    <p class="mt-1 text-[10px] md:text-xs text-gray-500">Use <a href="{{ route('superadmin.role-management') }}" class="text-[#1a5c1a] hover:underline">Role Management</a> when a user needs multiple roles.</p>
                </div>

                <div>
                    <label for="password" class="mb-1 block text-xs md:text-sm font-medium text-gray-700">Password</label>
                    <input id="password" type="password" name="password" required
                        class="w-full rounded-lg border border-gray-300 px-2 py-1.5 md:px-3 md:py-2 text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5c1a]">
                    @error('password') <p class="mt-1 text-[10px] md:text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="mb-1 block text-xs md:text-sm font-medium text-gray-700">Confirm Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                        class="w-full rounded-lg border border-gray-300 px-2 py-1.5 md:px-3 md:py-2 text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5c1a]">
                </div>

                <button type="submit" class="w-full rounded-lg bg-[#1a5c1a] py-2 md:py-2.5 text-xs md:text-sm font-semibold text-white transition hover:bg-green-900">
                    Create Account
                </button>
            </form>
        </div>

        <div class="sa-card xl:col-span-2">
            <div class="sa-card-header">
                <div class="flex flex-col items-start justify-between gap-3 md:gap-4 md:flex-row md:items-center">
                    <div>
                        <h2 class="text-base font-semibold text-gray-800">All Accounts</h2>
                        <p class="mt-1 text-xs text-gray-500">Filter by role, status, name, email, or contact number.</p>
                    </div>
                    <a href="{{ route('superadmin.accounts') }}" class="text-xs font-semibold text-[#1a5c1a] hover:text-green-900 whitespace-nowrap">
                        Reset filters
                    </a>
                </div>

                <form method="GET" action="{{ route('superadmin.accounts') }}" class="mt-3 md:mt-4 grid grid-cols-1 gap-2 md:gap-3 sm:grid-cols-2" x-data="{ loading: false }">
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Search accounts"
                        class="sm:col-span-2 rounded-lg border border-gray-300 px-2 py-1.5 md:px-3 md:py-2 text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5c1a]"
                        x-model.debounce.500ms="search"
                        @input="$el.closest('form').submit()">
                    <select name="role" class="rounded-lg border border-gray-300 px-2 py-1.5 md:px-3 md:py-2 text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5c1a]">
                        <option value="">All roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" @selected(request('role') === $role->name)>
                                {{ $role->name }} ({{ $roleCounts[$role->name] ?? 0 }})
                            </option>
                        @endforeach
                    </select>
                    <select name="status" class="rounded-lg border border-gray-300 px-2 py-1.5 md:px-3 md:py-2 text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5c1a]">
                        <option value="">All statuses</option>
                        <option value="active" @selected(request('status') === 'active')>Active</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                    </select>
                    <button type="submit" class="rounded-xl bg-green-50 px-3 py-1.5 md:px-4 md:py-2 text-xs md:text-sm font-semibold text-green-900 transition hover:bg-green-100 sm:col-span-2 whitespace-nowrap">
                        Apply Filters
                    </button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-max text-xs md:text-sm">
                    <thead class="bg-gray-50 text-[10px] md:text-xs text-gray-500">
                        <tr>
                            <th class="px-2 py-2 md:px-6 md:py-3 text-left">Name</th>
                            <th class="px-2 py-2 md:px-6 md:py-3 text-left">Contact</th>
                            <th class="px-2 py-2 md:px-6 md:py-3 text-left">Role</th>
                            <th class="px-2 py-2 md:px-6 md:py-3 text-left">Status</th>
                            <th class="px-2 py-2 md:px-6 md:py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-2 py-2 md:px-6 md:py-3">
                                    <p class="font-medium text-gray-800">{{ $user->name }}</p>
                                    @if(!$user->hasRole('Student'))
                                        <p class="text-[10px] md:text-xs text-gray-500">{{ $user->email }}</p>
                                    @endif
                                </td>
                                <td class="px-2 py-2 md:px-6 md:py-3 text-gray-500">
                                    {{ $user->contact_number ?? 'Not provided' }}
                                </td>
                                <td class="px-2 py-2 md:px-6 md:py-3">
                                    @if($user->roles->isNotEmpty())
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($user->roles as $role)
                                                <span class="rounded-full bg-green-100 px-2 py-0.5 md:px-2.5 md:py-1 text-[10px] md:text-xs font-medium text-green-800 whitespace-nowrap">
                                                    {{ $role->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-[10px] md:text-xs text-gray-400">Unassigned</span>
                                    @endif
                                </td>
                                <td class="px-2 py-2 md:px-6 md:py-3">
                                    @if($user->hasRole('Super Admin'))
                                        <span class="rounded-full bg-yellow-50 px-2 py-0.5 md:px-2.5 md:py-1 text-[10px] md:text-xs font-medium text-yellow-700 whitespace-nowrap">
                                            Protected
                                        </span>
                                    @else
                                        <form method="POST" action="{{ route('superadmin.accounts.toggle', $user) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="rounded-full px-2 py-0.5 md:px-2.5 md:py-1 text-[10px] md:text-xs font-medium transition {{ $user->is_active ? 'bg-green-50 text-green-700 hover:bg-green-100' : 'bg-red-50 text-red-600 hover:bg-red-100' }} whitespace-nowrap">
                                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                                            </button>
                                        </form>
                                    @endif
                                </td>
                                <td class="px-2 py-2 md:px-6 md:py-3">
                                    @if(!$user->hasRole('Super Admin'))
                                        <div class="flex gap-1.5 md:gap-2">
                                            <a href="{{ route('superadmin.accounts.edit', $user) }}"
                                                class="rounded-lg bg-gray-100 px-2 py-1 md:px-3 md:py-1.5 text-[10px] md:text-xs text-gray-700 transition hover:bg-gray-200 whitespace-nowrap">
                                                Edit
                                            </a>
                                            <button
                                                type="button"
                                                x-on:click="$dispatch('open-delete-modal', {
                                                    modalName: 'delete-user-modal',
                                                    id: {{ $user->id }},
                                                    name: '{{ $user->name }}',
                                                    details: 'Role: {{ $user->getRoleNames()->first() ?? 'N/A' }}',
                                                    action: '/superadmin/accounts/{{ $user->id }}'
                                                })"
                                                class="rounded-lg bg-red-50 px-2 py-1 md:px-3 md:py-1.5 text-[10px] md:text-xs text-red-600 transition hover:bg-red-100 whitespace-nowrap"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-[10px] md:text-xs text-gray-400">No actions</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-2 py-4 md:px-6 md:py-8 text-center text-gray-400 text-[10px] md:text-xs">
                                    No accounts match the current filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="border-t border-gray-100 px-4 py-3 md:px-6 md:py-4">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
