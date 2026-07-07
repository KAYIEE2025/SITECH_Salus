@extends('layouts.app')
@section('title', 'Registrar Dashboard')
<!-- @section('sidebar-links')
    <x-nav-link href="{{ route('registrar.dashboard') }}" :active="true">Dashboard</x-nav-link>
    <x-nav-link href="#" :active="false">Student Records</x-nav-link>
    <x-nav-link href="#" :active="false">Study Load</x-nav-link>
    <x-nav-link href="#" :active="false">Grade Approval</x-nav-link>
    <x-nav-link href="#" :active="false">Activity Logs</x-nav-link>
    <x-nav-link href="#" :active="false">My Profile</x-nav-link>
@endsection -->
@section('content')
    <div class="grid grid-cols-3 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Total Students</p>
            <p class="text-3xl font-bold text-green-800">{{ $totalStudents }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Pending Grade Approvals</p>
            <p class="text-3xl font-bold text-green-800">{{ $pendingGrades }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Active Sections</p>
            <p class="text-3xl font-bold text-green-800">{{ $activeSections }}</p>
        </div>
    </div>
@endsection