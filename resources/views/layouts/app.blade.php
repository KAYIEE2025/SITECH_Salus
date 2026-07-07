<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SITech - @yield('title', $title ?? 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50">
    <aside class="fixed left-0 top-0 flex min-h-screen w-64 flex-col bg-[#1a5c1a]">
        <div class="border-b border-green-800 px-6 py-5">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/salus-logo.png') }}" alt="Salus Institute of Technology seal" class="h-11 w-11 rounded-full bg-white object-contain p-1">
                <div>
                    <p class="text-sm font-bold text-white">SITech</p>
                    <p class="text-xs text-green-200">Salus Institute of Technology</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4">
            @if(auth()->user()->hasRole('Super Admin'))
                <x-nav-link href="{{ route('superadmin.dashboard') }}" :active="request()->routeIs('superadmin.dashboard')">Dashboard</x-nav-link>
                <x-nav-link href="{{ route('superadmin.accounts') }}" :active="request()->routeIs('superadmin.accounts*')">Manage Accounts</x-nav-link>
                <x-nav-link href="{{ route('superadmin.student-accounts.index') }}" :active="request()->routeIs('superadmin.student-accounts*')">Student Accounts</x-nav-link>
                <x-nav-link href="{{ route('superadmin.roles') }}" :active="request()->routeIs('superadmin.roles')">Roles & Access</x-nav-link>
                <x-nav-link href="{{ route('superadmin.activity-logs') }}" :active="request()->routeIs('superadmin.activity-logs')">Activity Logs</x-nav-link>
                <x-nav-link href="{{ route('superadmin.profile') }}" :active="request()->routeIs('superadmin.profile')">My Profile</x-nav-link>
            @elseif(auth()->user()->hasRole('Admin'))
                @yield('sidebar-links')
            @elseif(auth()->user()->hasRole('Registrar'))
                <x-nav-link href="{{ route('registrar.dashboard') }}" :active="request()->routeIs('registrar.dashboard')">Dashboard</x-nav-link>
                <x-nav-link href="{{ route('registrar.sections') }}" :active="request()->routeIs('registrar.sections*')">Sections</x-nav-link>
                <x-nav-link href="{{ route('registrar.students') }}" :active="request()->routeIs('registrar.students*')">Student Records</x-nav-link>
                <x-nav-link href="{{ route('registrar.study-load') }}" :active="request()->routeIs('registrar.study-load*')">Study Load</x-nav-link>
                <x-nav-link href="{{ route('registrar.grade-approval') }}" :active="request()->routeIs('registrar.grade-approval*')">Grade Approval</x-nav-link>
                <x-nav-link href="{{ route('registrar.profile') }}" :active="request()->routeIs('registrar.profile')">My Profile</x-nav-link>
            @elseif(auth()->user()->hasRole('Teacher'))
                <x-nav-link href="{{ route('teacher.dashboard') }}" :active="request()->routeIs('teacher.dashboard')">Dashboard</x-nav-link>
                <x-nav-link href="{{ route('teacher.schedule.index') }}" :active="request()->routeIs('teacher.schedule*')">My Schedule</x-nav-link>
                <x-nav-link href="{{ route('teacher.classes.index') }}" :active="request()->routeIs('teacher.classes*')">Class List</x-nav-link>
                <x-nav-link href="{{ route('teacher.announcements.index') }}" :active="request()->routeIs('teacher.announcements*')">Announcements</x-nav-link>
                <x-nav-link href="{{ route('teacher.profile.index') }}" :active="request()->routeIs('teacher.profile*')">My Profile</x-nav-link>
            @elseif(auth()->user()->hasRole('SSG'))
                <x-nav-link href="{{ route('ssg.dashboard') }}" :active="request()->routeIs('ssg.dashboard')">Dashboard</x-nav-link>
                <x-nav-link href="{{ route('ssg.events.index') }}" :active="request()->routeIs('ssg.events*') || request()->routeIs('ssg.attendance*')">Events</x-nav-link>
                <x-nav-link href="{{ route('ssg.fines.index') }}" :active="request()->routeIs('ssg.fines*')">Fine Records</x-nav-link>
                <x-nav-link href="{{ route('ssg.profile.index') }}" :active="request()->routeIs('ssg.profile*')">My Profile</x-nav-link>
            @elseif(auth()->user()->hasRole('Student'))
                <x-nav-link href="{{ route('student.dashboard') }}" :active="request()->routeIs('student.dashboard')">Dashboard</x-nav-link>
                <x-nav-link href="{{ route('student.profile.index') }}" :active="request()->routeIs('student.profile*')">My Profile</x-nav-link>
                <x-nav-link href="{{ route('student.study-load.index') }}" :active="request()->routeIs('student.study-load*')">Study Load</x-nav-link>
                <x-nav-link href="{{ route('student.grades.index') }}" :active="request()->routeIs('student.grades*')">Grades</x-nav-link>
                <x-nav-link href="{{ route('student.announcements.index') }}" :active="request()->routeIs('student.announcements*')">Announcements</x-nav-link>
                <x-nav-link href="{{ route('student.school-calendar.index') }}" :active="request()->routeIs('student.school-calendar*')">School Calendar</x-nav-link>
                <x-nav-link href="{{ route('student.ssg-events.index') }}" :active="request()->routeIs('student.ssg-events*')">SSG Events & Fines</x-nav-link>
            @endif
        </nav>

        <div class="border-t border-green-800 px-4 py-4">
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[#c8a000]">
                    <span class="text-xs font-bold text-green-950">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </span>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-xs font-semibold text-white">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-green-200">{{ auth()->user()->getRoleNames()->first() }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="w-full text-left text-xs text-green-200 transition hover:text-white">
                    Log out
                </button>
            </form>
        </div>
    </aside>

    <main class="ml-64 min-h-screen">
        <div class="border-b border-gray-200 bg-white px-8 py-4">
            <h1 class="text-lg font-semibold text-gray-800">@yield('title', $title ?? 'Dashboard')</h1>
        </div>

        <div class="p-8">
            @yield('content')
        </div>
    </main>
</body>
</html>
