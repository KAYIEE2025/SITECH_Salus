@extends('layouts.app')
@section('title', 'SSG Dashboard')
<!-- @section('sidebar-links')
    <x-nav-link href="{{ route('ssg.dashboard') }}" :active="true">Dashboard</x-nav-link>
    <x-nav-link href="#" :active="false">Events</x-nav-link>
    <x-nav-link href="#" :active="false">Scan Attendance</x-nav-link>
    <x-nav-link href="#" :active="false">Fine Records</x-nav-link>
    <x-nav-link href="#" :active="false">My Profile</x-nav-link>
@endsection -->
@section('content')
    <div class="grid grid-cols-3 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Total Events</p>
            <p class="text-3xl font-bold text-green-800">{{ $totalEvents }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Upcoming Events</p>
            <p class="text-3xl font-bold text-green-800">{{ $upcomingEvents }}</p>
        </div>
    </div>
@endsection