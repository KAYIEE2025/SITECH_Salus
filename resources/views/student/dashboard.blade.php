@extends('layouts.app')
@section('title', 'Student Dashboard')
<!-- @section('sidebar-links')
    <x-nav-link href="{{ route('student.dashboard') }}" :active="true">Dashboard</x-nav-link>
    <x-nav-link href="#" :active="false">My Profile</x-nav-link>
    <x-nav-link href="#" :active="false">Study Load</x-nav-link>
    <x-nav-link href="#" :active="false">Grades</x-nav-link>
    <x-nav-link href="#" :active="false">Announcements</x-nav-link>
    <x-nav-link href="#" :active="false">School Calendar</x-nav-link>
    <x-nav-link href="#" :active="false">SSG Events & Fines</x-nav-link>
@endsection -->
@section('content')
    <div class="grid grid-cols-3 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Approved Grades</p>
            <p class="text-3xl font-bold text-green-800">{{ $approvedGrades }}</p>
        </div>
        @if($student)
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Student Number</p>
            <p class="text-xl font-bold text-green-800">{{ $student->student_number }}</p>
        </div>
        @endif
    </div>
@endsection