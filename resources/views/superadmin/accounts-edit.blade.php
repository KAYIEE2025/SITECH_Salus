@extends('layouts.app')

@section('title', 'Edit Account')

@section('content')
    <div class="max-w-2xl">
        <div class="sa-card p-6 sm:p-8">
            <div class="mb-6 flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-base font-semibold text-gray-800">Edit Account</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ $user->email }}</p>
                </div>

                <span class="rounded-full {{ $user->is_active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }} px-3 py-1 text-xs font-semibold">
                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>

            <form method="POST" action="{{ route('superadmin.accounts.update', $user) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="mb-1 block text-sm font-medium text-gray-700">Full Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5c1a]">
                    @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="mb-1 block text-sm font-medium text-gray-700">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5c1a]">
                    @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="contact_number" class="mb-1 block text-sm font-medium text-gray-700">Contact Number</label>
                    <input id="contact_number" type="text" name="contact_number" value="{{ old('contact_number', $user->contact_number) }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5c1a]">
                    @error('contact_number') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" class="mb-1 block text-sm font-medium text-gray-700">New Password</label>
                    <input id="password" type="password" name="password" autocomplete="new-password"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5c1a]">
                    <p class="mt-1 text-xs text-gray-400">Leave blank to keep the current password.</p>
                    @error('password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="mb-1 block text-sm font-medium text-gray-700">Confirm New Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5c1a]">
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="rounded-lg bg-[#1a5c1a] px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-green-900">
                        Save Changes
                    </button>
                    <a href="{{ route('superadmin.accounts') }}" class="rounded-lg bg-gray-100 px-6 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-200">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
