<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>SITech - @yield('title', $title ?? 'Dashboard')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
@php($isSuperAdminPanel = request()->routeIs('superadmin.*'))
@php($isRegistrarPanel = request()->routeIs('registrar.*'))
@php($isStudentPanel = request()->routeIs('student.*'))
@php($isSsgPanel = request()->routeIs('ssg.*'))
@php($isTeacherPanel = request()->routeIs('teacher.*'))

<body class="min-h-screen {{ $isSuperAdminPanel || $isRegistrarPanel || $isStudentPanel || $isSsgPanel || $isTeacherPanel ? 'bg-[#f5faf6]' : 'bg-gray-50' }}">
    <aside class="fixed left-0 top-0 z-20 flex h-screen w-64 flex-col {{ $isSuperAdminPanel || $isRegistrarPanel || $isStudentPanel || $isSsgPanel || $isTeacherPanel ? 'bg-gradient-to-b from-[#14532d] via-[#1a5c1a] to-[#064e3b] shadow-xl shadow-green-950/20' : 'bg-[#1a5c1a]' }}">
        <div class="border-b px-6 py-5 {{ $isSuperAdminPanel || $isRegistrarPanel || $isStudentPanel || $isSsgPanel || $isTeacherPanel ? 'border-green-400/20' : 'border-green-600' }}">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/salus-logo.png') }}" alt="Salus Institute of Technology seal" class="h-20 w-20 rounded-full object-contain p-1">
                <div>
                    <p class="text-sm font-bold tracking-wide text-white">SITech</p>
                    <p class="text-xs text-green-200">Salus Institute of Technology</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4">
            <x-dynamic-sidebar />
        </nav>

        <div class="border-t px-4 py-4 {{ $isSuperAdminPanel || $isRegistrarPanel || $isStudentPanel || $isSsgPanel || $isTeacherPanel ? 'border-green-300/20' : 'border-green-800' }}">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-[#f5d76e] to-[#c8a000] shadow-sm">
                    <span class="text-xs font-bold text-green-950">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </span>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-xs font-semibold text-white">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-green-200">{{ auth()->user()->getRoleNames()->implode(' | ') }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="w-full rounded-lg px-2 py-1.5 text-left text-xs text-green-200 transition hover:bg-green-950/30 hover:text-white">
                    Log out
                </button>
            </form>
        </div>
    </aside>

    <main class="ml-64 min-h-screen">
        <div class="border-b bg-white {{ $isSuperAdminPanel || $isRegistrarPanel || $isStudentPanel || $isSsgPanel || $isTeacherPanel ? 'border-green-100 px-8 py-5' : 'border-gray-200 px-8 py-4' }}">
            <div class="flex items-center justify-between gap-4">
                <div>
                    @if($isSuperAdminPanel || $isRegistrarPanel || $isStudentPanel || $isSsgPanel || $isTeacherPanel)
                        <p class="mb-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-green-700">{{ $isSuperAdminPanel ? 'System administration' : ($isRegistrarPanel ? 'Registrar workspace' : ($isStudentPanel ? 'Student portal' : ($isSsgPanel ? 'SSG workspace' : 'Teacher workspace'))) }}</p>
                    @endif
                    <h1 class="{{ $isSuperAdminPanel || $isRegistrarPanel || $isStudentPanel || $isSsgPanel || $isTeacherPanel ? 'text-xl font-bold text-green-950' : 'text-lg font-semibold text-gray-800' }}">@yield('title', $title ?? 'Dashboard')</h1>
                </div>
                @if($isSuperAdminPanel || $isRegistrarPanel || $isStudentPanel || $isSsgPanel || $isTeacherPanel)
                    <div class="hidden items-center gap-2 text-xs text-green-700 sm:flex">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        Control center online
                    </div>
                @endif
            </div>
        </div>

        <div class="{{ $isSuperAdminPanel ? 'p-5 sm:p-8 superadmin-panel' : ($isRegistrarPanel ? 'p-5 sm:p-8 registrar-panel' : ($isStudentPanel ? 'p-5 sm:p-8 student-panel' : (($isSsgPanel || $isTeacherPanel) ? 'p-5 sm:p-8 ' . ($isSsgPanel ? 'ssg-panel' : 'teacher-panel') : 'p-8'))) }}">
            @yield('content')
        </div>
    </main>

    @yield('scripts')
</body>
</html>
