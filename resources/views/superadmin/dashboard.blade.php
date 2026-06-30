@extends('layouts.app')

@section('title', 'Super Admin Dashboard')

@section('content')
    <div class="mb-8 grid grid-cols-4 gap-4">
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Total Accounts</p>
            <p class="mt-1 text-3xl font-bold text-[#1a5c1a]">{{ $totalUsers }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Active Accounts</p>
            <p class="mt-1 text-3xl font-bold text-[#1a5c1a]">{{ $activeUsers }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Inactive Accounts</p>
            <p class="mt-1 text-3xl font-bold text-red-600">{{ $inactiveUsers }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Students</p>
            <p class="mt-1 text-3xl font-bold text-[#c8a000]">{{ $totalStudents }}</p>
        </div>
    </div>

    <div class="mb-8 grid grid-cols-4 gap-4">
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Admins</p>
            <p class="mt-1 text-2xl font-bold text-gray-800">{{ $totalAdmins }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Registrars</p>
            <p class="mt-1 text-2xl font-bold text-gray-800">{{ $totalRegistrars }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Teachers</p>
            <p class="mt-1 text-2xl font-bold text-gray-800">{{ $totalTeachers }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">SSG Officers</p>
            <p class="mt-1 text-2xl font-bold text-gray-800">{{ $totalSSG }}</p>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-6">
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="text-base font-semibold text-gray-800">Quick Actions</h2>
            <div class="mt-4 space-y-3">
                <a href="{{ route('superadmin.accounts') }}" class="block rounded-lg bg-[#1a5c1a] px-4 py-3 text-sm font-semibold text-white transition hover:bg-green-900">
                    Create or Manage Accounts
                </a>
                <a href="{{ route('superadmin.activity-logs') }}" class="block rounded-lg bg-gray-100 px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-200">
                    View System Activity
                </a>
                <a href="{{ route('superadmin.roles') }}" class="block rounded-lg bg-yellow-50 px-4 py-3 text-sm font-semibold text-yellow-800 transition hover:bg-yellow-100">
                    Review Roles & Access
                </a>
            </div>
        </div>

        <div class="col-span-2 rounded-xl border border-gray-200 bg-white">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="text-base font-semibold text-gray-800">Recent System Activity</h2>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($recentLogs as $log)
                    <div class="flex items-start justify-between gap-4 px-6 py-4">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $log->description }}</p>
                            <p class="mt-1 text-xs text-gray-500">
                                {{ optional($log->causer)->name ?? 'System' }} - {{ $log->event ?? 'activity' }}
                            </p>
                        </div>
                        <p class="shrink-0 text-xs text-gray-400">{{ $log->created_at->diffForHumans() }}</p>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-sm text-gray-400">
                        No activity logs yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
