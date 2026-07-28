@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
    @if(session('success'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid gap-6 md:grid-cols-2">
        <div class="sg-card p-6">
            <h2 class="mb-4 text-base font-semibold text-gray-800">Personal Information</h2>
            <form method="POST" action="{{ route('ssg.profile.update') }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Contact Number</label>
                    <input type="text" name="contact_number" value="{{ old('contact_number', auth()->user()->contact_number) }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                </div>

                <div class="mb-6">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Role</label>
                    <input type="text" value="{{ auth()->user()->getRoleNames()->first() }}" disabled
                        class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-500">
                </div>

                <button type="submit" class="rounded-lg bg-green-800 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-green-900">
                    Save Changes
                </button>
            </form>
        </div>

        <div class="sg-card p-6">
            <h2 class="mb-4 text-base font-semibold text-gray-800">Change Password</h2>
            <form method="POST" action="{{ route('ssg.profile.password') }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Current Password</label>
                    <input type="password" name="current_password"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                </div>

                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium text-gray-700">New Password</label>
                    <input type="password" name="password"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                </div>

                <div class="mb-6">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Confirm New Password</label>
                    <input type="password" name="password_confirmation"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                </div>

                <button type="submit" class="rounded-lg bg-green-800 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-green-900">
                    Change Password
                </button>
            </form>
        </div>
    </div>
@endsection
