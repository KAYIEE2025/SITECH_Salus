@extends('layouts.app')
@section('title', 'Admin Dashboard')
<!-- @section('sidebar-links')
    <x-nav-link href="{{ route('admin.dashboard') }}" :active="true">Dashboard</x-nav-link>
    <x-nav-link href="#" :active="false">Announcements</x-nav-link>
    <x-nav-link href="#" :active="false">School Calendar</x-nav-link>
    <x-nav-link href="#" :active="false">Reports</x-nav-link>
    <x-nav-link href="#" :active="false">Activity Logs</x-nav-link>
    <x-nav-link href="#" :active="false">My Profile</x-nav-link>
@endsection -->
@section('content')
    <div class="grid grid-cols-3 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Announcements</p>
            <p class="text-3xl font-bold text-green-800">{{ $totalAnnouncements }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">School Events</p>
            <p class="text-3xl font-bold text-green-800">{{ $totalEvents }}</p>
        </div>
    </div>
@endsection