<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SITech - Salus Institute of Technology</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-green-50 px-4">
    <div class="w-full max-w-md rounded-2xl border border-green-100 bg-white p-8 shadow-lg">
        <div class="mb-8 text-center">
            <img src="{{ asset('images/salus-logo.png') }}" alt="Salus Institute of Technology seal" class="mx-auto mb-4 h-24 w-24 rounded-full object-contain">
            <h1 class="text-2xl font-bold text-[#1a5c1a]">Salus Institute of Technology</h1>
            <p class="mt-1 text-sm text-gray-500">Student Information System</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="mb-1 block text-sm font-medium text-gray-700">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-[#1a5c1a]"
                    placeholder="you@salus.edu">
            </div>

            <div class="mb-6">
                <label for="password" class="mb-1 block text-sm font-medium text-gray-700">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-[#1a5c1a]"
                    placeholder="Enter your password">
            </div>

            <label class="mb-6 flex items-center gap-2 text-sm text-gray-600">
                <input type="checkbox" name="remember" class="rounded border-gray-300 text-[#1a5c1a] focus:ring-[#1a5c1a]">
                Remember me
            </label>

            <button type="submit" class="w-full rounded-lg bg-[#1a5c1a] py-2.5 text-sm font-semibold text-white transition hover:bg-green-900">
                Log In
            </button>
        </form>

        <p class="mt-6 text-center text-xs text-gray-400">
            AY 2025-2026 - SITech
        </p>
    </div>
</body>
</html>
