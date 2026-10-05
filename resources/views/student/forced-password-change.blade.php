@extends('layouts.app')

@section('title', 'Password Change Required')

@section('content')
    <div class="min-h-[calc(100vh-4rem)] flex items-center justify-center px-4">
        <div class="max-w-md w-full">
            <div class="bg-white rounded-2xl border border-red-200 shadow-lg p-8">
                <!-- Warning Icon -->
                <div class="flex justify-center mb-6">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Header -->
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Password Change Required</h1>
                    <p class="text-gray-600 text-sm">
                        For security purposes, you must change your temporary password before accessing the Student Portal.
                    </p>
                </div>

                <!-- Success/Error Messages -->
                @session('success')
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                        {{ $value }}
                    </div>
                @endsession

                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                        {{ $errors->first() }}
                    </div>
                @endif

                <!-- Password Change Form -->
                <form action="{{ route('student.forced-password-change.store') }}" method="POST">
                    @csrf
                    <div class="space-y-6">
                        <!-- Current Password -->
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                                Current Password
                            </label>
                            <div class="relative">
                                <input type="password"
                                       id="current_password"
                                       name="current_password"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-12 text-sm focus:ring-red-500 focus:border-red-500"
                                       required
                                       autofocus>
                                <button type="button"
                                        onclick="togglePassword('current_password')"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 focus:outline-none"
                                        aria-label="Show current password">
                                    <svg id="eye-current_password" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2.06 12.35a1 1 0 0 1 0-.7C3.64 7.68 7.51 5 12 5c4.49 0 8.36 2.68 9.94 6.65a1 1 0 0 1 0 .7C20.36 16.32 16.49 19 12 19c-4.49 0-8.36-2.68-9.94-6.65Z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <svg id="eye-slash-current_password" class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 3l18 18"></path>
                                        <path d="M10.58 10.58a2 2 0 0 0 2.83 2.83"></path>
                                        <path d="M9.88 4.24A10.94 10.94 0 0 1 12 4c4.49 0 8.36 2.68 9.94 6.65a1 1 0 0 1 0 .7 10.97 10.97 0 0 1-4.12 5.05"></path>
                                        <path d="M6.61 6.61A10.98 10.98 0 0 0 2.06 11.65a1 1 0 0 0 0 .7C3.64 16.32 7.51 19 12 19c1.61 0 3.13-.35 4.5-.98"></path>
                                    </svg>
                                </button>
                            </div>
                            @error('current_password')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                New Password
                            </label>
                            <div class="relative">
                                <input type="password"
                                       id="password"
                                       name="password"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-12 text-sm focus:ring-red-500 focus:border-red-500"
                                       required>
                                <button type="button"
                                        onclick="togglePassword('password')"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 focus:outline-none"
                                        aria-label="Show new password">
                                    <svg id="eye-password" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2.06 12.35a1 1 0 0 1 0-.7C3.64 7.68 7.51 5 12 5c4.49 0 8.36 2.68 9.94 6.65a1 1 0 0 1 0 .7C20.36 16.32 16.49 19 12 19c-4.49 0-8.36-2.68-9.94-6.65Z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <svg id="eye-slash-password" class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 3l18 18"></path>
                                        <path d="M10.58 10.58a2 2 0 0 0 2.83 2.83"></path>
                                        <path d="M9.88 4.24A10.94 10.94 0 0 1 12 4c4.49 0 8.36 2.68 9.94 6.65a1 1 0 0 1 0 .7 10.97 10.97 0 0 1-4.12 5.05"></path>
                                        <path d="M6.61 6.61A10.98 10.98 0 0 0 2.06 11.65a1 1 0 0 0 0 .7C3.64 16.32 7.51 19 12 19c1.61 0 3.13-.35 4.5-.98"></path>
                                    </svg>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Minimum 8 characters</p>
                            @error('password')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm New Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Confirm New Password
                            </label>
                            <div class="relative">
                                <input type="password"
                                       id="password_confirmation"
                                       name="password_confirmation"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 pr-12 text-sm focus:ring-red-500 focus:border-red-500"
                                       required>
                                <button type="button"
                                        onclick="togglePassword('password_confirmation')"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 focus:outline-none"
                                        aria-label="Show confirm password">
                                    <svg id="eye-password_confirmation" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2.06 12.35a1 1 0 0 1 0-.7C3.64 7.68 7.51 5 12 5c4.49 0 8.36 2.68 9.94 6.65a1 1 0 0 1 0 .7C20.36 16.32 16.49 19 12 19c-4.49 0-8.36-2.68-9.94-6.65Z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <svg id="eye-slash-password_confirmation" class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 3l18 18"></path>
                                        <path d="M10.58 10.58a2 2 0 0 0 2.83 2.83"></path>
                                        <path d="M9.88 4.24A10.94 10.94 0 0 1 12 4c4.49 0 8.36 2.68 9.94 6.65a1 1 0 0 1 0 .7 10.97 10.97 0 0 1-4.12 5.05"></path>
                                        <path d="M6.61 6.61A10.98 10.98 0 0 0 2.06 11.65a1 1 0 0 0 0 .7C3.64 16.32 7.51 19 12 19c1.61 0 3.13-.35 4.5-.98"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="w-full bg-red-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-red-700 transition focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                            Change Password
                        </button>
                    </div>
                </form>

                <!-- Logout Link -->
                <div class="mt-6 text-center">
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 underline">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const input = document.getElementById(fieldId);
            const eyeIcon = document.getElementById('eye-' + fieldId);
            const eyeSlashIcon = document.getElementById('eye-slash-' + fieldId);

            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeSlashIcon.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeSlashIcon.classList.add('hidden');
            }
        }
    </script>
@endsection