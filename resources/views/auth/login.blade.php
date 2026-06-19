<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SITech — Salus Institute of Technology</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-green-50 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">

        {{-- Logo & Title --}}
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-green-800 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-white font-bold text-xl">SIT</span>
            </div>
            <h1 class="text-2xl font-bold text-green-900">Salus Institute of Technology</h1>
            <p class="text-sm text-gray-500 mt-1">Student Information System</p>
        </div>

        {{-- Error Messages --}}
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg p-3 mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Login Form --}}
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-transparent"
                    placeholder="you@salus.edu">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-transparent"
                    placeholder="••••••••">
            </div>

            <button type="submit"
                class="w-full bg-green-800 hover:bg-green-900 text-white font-semibold py-2.5 rounded-lg text-sm transition">
                Log In
            </button>

        </form>

        <p class="text-center text-xs text-gray-400 mt-6">
            AY 2025–2026 &nbsp;·&nbsp; Mater Dei College Capstone Project
        </p>

    </div>

</body>
</html>