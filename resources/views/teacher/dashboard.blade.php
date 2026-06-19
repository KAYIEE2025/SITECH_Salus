@extends('layouts.app')
@section('title', 'Teacher Dashboard')
<!-- @section('sidebar-links')
    <x-nav-link href="{{ route('teacher.dashboard') }}" :active="true">Dashboard</x-nav-link>
    <x-nav-link href="#" :active="false">Grade Management</x-nav-link>
    <x-nav-link href="#" :active="false">Class List</x-nav-link>
    <x-nav-link href="#" :active="false">Announcements</x-nav-link>
    <x-nav-link href="#" :active="false">My Profile</x-nav-link>
@endsection -->
@section('content')
    <div class="grid grid-cols-3 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Classes Handled</p>
            <p class="text-3xl font-bold text-green-800">{{ $classes }}</p>
        </div>
    </div>
@endsection