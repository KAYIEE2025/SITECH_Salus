<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SITech — {{ $title ?? 'Dashboard' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex">

    <aside class="w-64 bg-green-900 min-h-screen flex flex-col fixed top-0 left-0">

        {{-- Logo --}}
        <div class="px-6 py-5 border-b border-green-800">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-yellow-500 rounded-lg flex items-center justify-center">
                    <span class="text-green-900 font-bold text-xs">SIT</span>
                </div>
                <div>
                    <p class="text-white font-bold text-sm">Salus SIS</p>
                    <p class="text-green-400 text-xs">Student Info System</p>
                </div>
            </div>
        </div>

        {{-- Navigation based on role --}}
        <nav class="flex-1 px-3 py-4 overflow-y-auto">

            @if(auth()->user()->hasRole('Super Admin'))
                <x-nav-link href="{{ route('superadmin.dashboard') }}" :active="request()->routeIs('superadmin.dashboard')">Dashboard</x-nav-link>
                <x-nav-link href="{{ route('superadmin.accounts') }}" :active="request()->routeIs('superadmin.accounts*')">Manage Accounts</x-nav-link>
                <x-nav-link href="{{ route('superadmin.roles') }}" :active="request()->routeIs('superadmin.roles')">Roles & Access</x-nav-link>
                <x-nav-link href="{{ route('superadmin.activity-logs') }}" :active="request()->routeIs('superadmin.activity-logs')">Activity Logs</x-nav-link>
                <x-nav-link href="{{ route('superadmin.profile') }}" :active="request()->routeIs('superadmin.profile')">My Profile</x-nav-link>

            @elseif(auth()->user()->hasRole('Admin'))
                <x-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')">Dashboard</x-nav-link>
                <x-nav-link href="#" :active="false">Announcements</x-nav-link>
                <x-nav-link href="#" :active="false">School Calendar</x-nav-link>
                <x-nav-link href="#" :active="false">Reports</x-nav-link>
                <x-nav-link href="#" :active="false">Activity Logs</x-nav-link>
                <x-nav-link href="#" :active="false">My Profile</x-nav-link>

           @elseif(auth()->user()->hasRole('Registrar'))
            <x-nav-link href="{{ route('registrar.dashboard') }}" :active="request()->routeIs('registrar.dashboard')">Dashboard</x-nav-link>
            <x-nav-link href="{{ route('registrar.sections') }}" :active="request()->routeIs('registrar.sections*')">Sections</x-nav-link>
            <x-nav-link href="{{ route('registrar.students') }}" :active="request()->routeIs('registrar.students*')">Student Records</x-nav-link>
            <x-nav-link href="{{ route('registrar.study-load') }}" :active="request()->routeIs('registrar.study-load*')">Study Load</x-nav-link>
            <x-nav-link href="{{ route('registrar.grade-approval') }}" :active="request()->routeIs('registrar.grade-approval*')">Grade Approval</x-nav-link>
            <x-nav-link href="{{ route('registrar.profile') }}" :active="request()->routeIs('registrar.profile')">My Profile</x-nav-link>

            @elseif(auth()->user()->hasRole('Teacher'))
                <x-nav-link href="{{ route('teacher.dashboard') }}" :active="request()->routeIs('teacher.dashboard')">Dashboard</x-nav-link>
                <x-nav-link href="#" :active="false">Grade Management</x-nav-link>
                <x-nav-link href="#" :active="false">Class List</x-nav-link>
                <x-nav-link href="#" :active="false">Announcements</x-nav-link>
                <x-nav-link href="#" :active="false">My Profile</x-nav-link>

            @elseif(auth()->user()->hasRole('SSG'))
                <x-nav-link href="{{ route('ssg.dashboard') }}" :active="request()->routeIs('ssg.dashboard')">Dashboard</x-nav-link>
                <x-nav-link href="#" :active="false">Events</x-nav-link>
                <x-nav-link href="#" :active="false">Scan Attendance</x-nav-link>
                <x-nav-link href="#" :active="false">Fine Records</x-nav-link>
                <x-nav-link href="#" :active="false">My Profile</x-nav-link>

            @elseif(auth()->user()->hasRole('Student'))
                <x-nav-link href="{{ route('student.dashboard') }}" :active="request()->routeIs('student.dashboard')">Dashboard</x-nav-link>
                <x-nav-link href="#" :active="false">My Profile</x-nav-link>
                <x-nav-link href="#" :active="false">Study Load</x-nav-link>
                <x-nav-link href="#" :active="false">Grades</x-nav-link>
                <x-nav-link href="#" :active="false">Announcements</x-nav-link>
                <x-nav-link href="#" :active="false">School Calendar</x-nav-link>
                <x-nav-link href="#" :active="false">SSG Events & Fines</x-nav-link>
            @endif

        </nav>

        {{-- User Info --}}
        <div class="px-4 py-4 border-t border-green-800">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                    <span class="text-green-900 font-bold text-xs">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-white text-xs font-semibold truncate">{{ auth()->user()->name }}</p>
                    <p class="text-green-400 text-xs">{{ auth()->user()->getRoleNames()->first() }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="w-full text-left text-xs text-green-400 hover:text-white transition">
                    Log out
                </button>
            </form>
        </div>

    </aside>

    <main class="ml-64 flex-1 min-h-screen">
        <div class="bg-white border-b border-gray-200 px-8 py-4">
            <h1 class="text-lg font-semibold text-gray-800">{{ $title ?? 'Dashboard' }}</h1>
        </div>
        <div class="p-8">
            @yield('content')
        </div>
    </main>

</body>
</html>
