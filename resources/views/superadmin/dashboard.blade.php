@extends('layouts.app')

@section('title', 'Super Admin Dashboard')

<!-- @section('sidebar-links')
    <x-nav-link href="{{ route('superadmin.dashboard') }}" :active="request()->routeIs('superadmin.dashboard')">
        Dashboard
    </x-nav-link>
    <x-nav-link href="{{ route('superadmin.accounts') }}" :active="false">
        Manage Accounts
    </x-nav-link>
    <x-nav-link href="#" :active="false">
        Roles & Access
    </x-nav-link>
    <x-nav-link href="{{ route('superadmin.activity-logs') }}" :active="false">Activity Logs</x-nav-link>
    <x-nav-link href="{{ route('superadmin.profile') }}" :active="false">
        My Profile
    </x-nav-link>
@endsection -->

@section('content')

    {{-- Stat Cards --}}
    <div class="grid grid-cols-3 gap-6 mb-8">

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Total Accounts</p>
            <p class="text-3xl font-bold text-green-800">{{ $totalUsers }}</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Teachers</p>
            <p class="text-3xl font-bold text-green-800">{{ $totalTeachers }}</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Students</p>
            <p class="text-3xl font-bold text-green-800">{{ $totalStudents }}</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Admins</p>
            <p class="text-3xl font-bold text-green-800">{{ $totalAdmins }}</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Registrars</p>
            <p class="text-3xl font-bold text-green-800">{{ $totalRegistrars }}</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">SSG</p>
            <p class="text-3xl font-bold text-green-800">{{ $totalSSG }}</p>
        </div>

    </div>

    {{-- Quick Actions --}}
    <div class="flex gap-3">
    <a href="{{ route('superadmin.accounts') }}"
        class="bg-green-800 hover:bg-green-900 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition">
        Create Account
    </a>
    <a href="{{ route('superadmin.activity-logs') }}"
        class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-4 py-2.5 rounded-lg transition">
        View Activity Logs
    </a>
</div>
    </div>

@endsection


