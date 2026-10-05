@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('sidebar-links')
    <x-nav-link href="{{ route('admin.dashboard') }}" :active="true">Dashboard</x-nav-link>
    <x-nav-link href="{{ route('admin.announcements.index') }}" :active="false">Announcements</x-nav-link>
    <x-nav-link href="{{ route('admin.calendar.index') }}" :active="false">School Calendar</x-nav-link>
    <x-nav-link href="{{ route('admin.reports.index') }}" :active="false">Reports</x-nav-link>
    <x-nav-link href="{{ route('admin.teacher-study-load') }}" :active="false">Teacher Study Load</x-nav-link>
    <x-nav-link href="{{ route('admin.activity-logs.index') }}" :active="false">Activity Logs</x-nav-link>
    <x-nav-link href="{{ route('admin.profile.index') }}" :active="false">My Profile</x-nav-link>
@endsection
@section('content')
    <div class="relative mb-6 overflow-hidden rounded-3xl bg-gradient-to-br from-[#14532d] via-[#166534] to-[#0f766e] p-5 text-white shadow-lg shadow-green-900/15 sm:mb-8 sm:p-8">
        <div class="pointer-events-none absolute -right-16 -top-20 h-56 w-56 rounded-full bg-white/10"></div>
        <div class="relative flex flex-col justify-between gap-6 sm:flex-row sm:items-end">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-green-100">Admin workspace</p>
                <h2 class="mt-2 text-2xl font-bold tracking-tight sm:text-3xl">Keep the school community in sync.</h2>
                <p class="mt-2 max-w-xl text-sm leading-6 text-green-100">Publish announcements, maintain the school calendar, review reports, and monitor system activity.</p>
            </div>
            <a href="{{ route('admin.announcements.create') }}" class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-r from-[#f4d35e] to-[#d6ad22] px-5 py-3 text-sm font-bold text-green-950 shadow-md transition hover:-translate-y-0.5 hover:shadow-lg">Create announcement</a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="admin-stat-card"><div class="flex items-center justify-between"><p class="text-sm font-medium text-gray-500">Announcements</p><span class="admin-stat-icon"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 13.5V10a2 2 0 0 1 2-2h7l4-3v13l-4-3H6a2 2 0 0 1-2-2.5ZM9 16l1.5 4h2L11 16"/></svg></span></div><p class="relative z-10 mt-5 text-3xl font-bold text-green-950">{{ $totalAnnouncements }}</p><p class="relative z-10 mt-1 text-xs text-gray-400">Published notices</p></div>
        <div class="admin-stat-card"><div class="flex items-center justify-between"><p class="text-sm font-medium text-gray-500">School Events</p><span class="admin-stat-icon"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></span></div><p class="relative z-10 mt-5 text-3xl font-bold text-green-950">{{ $totalEvents }}</p><p class="relative z-10 mt-1 text-xs text-gray-400">Calendar activities</p></div>
        <div class="admin-stat-card"><div class="flex items-center justify-between"><p class="text-sm font-medium text-gray-500">Total Students</p><span class="admin-stat-icon"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M16 20v-1.5a3.5 3.5 0 0 0-3.5-3.5h-5A3.5 3.5 0 0 0 4 18.5V20M10 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM16 4.5a3.5 3.5 0 0 1 0 6.8M18 15a3.5 3.5 0 0 1 2 3.2V20"/></svg></span></div><p class="relative z-10 mt-5 text-3xl font-bold text-green-950">{{ $totalStudents }}</p><p class="relative z-10 mt-1 text-xs text-gray-400">Enrolled learners</p></div>
        <div class="admin-stat-card"><div class="flex items-center justify-between"><p class="text-sm font-medium text-gray-500">Pending Grades</p><span class="relative z-10 flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-700"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 8v4l2.5 2.5M20 12a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"/></svg></span></div><p class="relative z-10 mt-5 text-3xl font-bold text-amber-700">{{ $pendingGrades }}</p><p class="relative z-10 mt-1 text-xs text-gray-400">Awaiting approval</p></div>
    </div>
@endsection
