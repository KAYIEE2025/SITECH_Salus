@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('sidebar-links')
    <x-nav-link href="{{ route('admin.dashboard') }}" :active="true">Dashboard</x-nav-link>
    <x-nav-link href="{{ route('admin.announcements.index') }}" :active="false">Announcements</x-nav-link>
    <x-nav-link href="{{ route('admin.calendar.index') }}" :active="false">School Calendar</x-nav-link>
    <x-nav-link href="{{ route('admin.reports.index') }}" :active="false">Reports</x-nav-link>
    <x-nav-link href="{{ route('admin.activity-logs.index') }}" :active="false">Activity Logs</x-nav-link>
    <x-nav-link href="{{ route('admin.profile.index') }}" :active="false">My Profile</x-nav-link>
@endsection
@section('content')
    <div class="grid grid-cols-1 gap-4 md:gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4 md:p-5">
            <p class="text-xs md:text-sm text-gray-500 mb-1">Total Announcements</p>
            <p class="text-2xl md:text-3xl font-bold text-green-800">{{ $totalAnnouncements }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 md:p-5">
            <p class="text-xs md:text-sm text-gray-500 mb-1">School Events</p>
            <p class="text-2xl md:text-3xl font-bold text-green-800">{{ $totalEvents }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 md:p-5">
            <p class="text-xs md:text-sm text-gray-500 mb-1">Total Students</p>
            <p class="text-2xl md:text-3xl font-bold text-green-800">{{ $totalStudents }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 md:p-5">
            <p class="text-xs md:text-sm text-gray-500 mb-1">Pending Grade Approvals</p>
            <p class="text-2xl md:text-3xl font-bold text-green-800">{{ $pendingGrades }}</p>
        </div>
    </div>
@endsection
