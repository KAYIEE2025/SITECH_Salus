@extends('layouts.app')

@section('title', 'Super Admin Dashboard')

@section('content')
    <div class="mb-8 overflow-hidden rounded-2xl bg-gradient-to-br from-[#14532d] via-[#1a5c1a] to-[#0f766e] p-6 text-white shadow-lg shadow-green-900/10 sm:p-8">
        <div class="flex flex-col justify-between gap-6 sm:flex-row sm:items-end">
            <div>
                <p class="text-sm font-medium text-green-100">Welcome back, {{ auth()->user()->name }}</p>
                <h2 class="mt-2 max-w-xl text-2xl font-bold tracking-tight sm:text-3xl">Your system at a glance.</h2>
                <p class="mt-2 max-w-lg text-sm leading-6 text-green-100">Monitor accounts, access roles, student onboarding, and important activity from one place.</p>
            </div>
            <div class="rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm backdrop-blur-sm">
                <p class="text-xs text-green-100">Access level</p>
                <p class="mt-1 font-semibold">Super Administrator</p>
            </div>
        </div>
    </div>

    <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <div class="sa-card p-5">
            <div class="flex items-center justify-between"><p class="text-sm text-gray-500">Total Accounts</p><span class="rounded-lg bg-green-50 p-2 text-green-700">◎</span></div>
            <p class="mt-4 text-3xl font-bold text-green-900">{{ $totalUsers }}</p>
            <p class="mt-1 text-xs text-gray-400">All registered users</p>
        </div>
        <div class="sa-card p-5">
            <div class="flex items-center justify-between"><p class="text-sm text-gray-500">Active Accounts</p><span class="rounded-lg bg-emerald-50 p-2 text-emerald-700">✓</span></div>
            <p class="mt-4 text-3xl font-bold text-green-900">{{ $activeUsers }}</p>
            <p class="mt-1 text-xs text-gray-400">Currently enabled</p>
        </div>
        <div class="sa-card p-5">
            <div class="flex items-center justify-between"><p class="text-sm text-gray-500">Inactive Accounts</p><span class="rounded-lg bg-red-50 p-2 text-red-600">!</span></div>
            <p class="mt-4 text-3xl font-bold text-red-600">{{ $inactiveUsers }}</p>
            <p class="mt-1 text-xs text-gray-400">Review when necessary</p>
        </div>
        <div class="sa-card p-5">
            <div class="flex items-center justify-between"><p class="text-sm text-gray-500">Students</p><span class="rounded-lg bg-amber-50 p-2 text-amber-700">◉</span></div>
            <p class="mt-4 text-3xl font-bold text-amber-700">{{ $totalStudents }}</p>
            <p class="mt-1 text-xs text-gray-400">Student records</p>
        </div>
        <a href="{{ route('superadmin.student-accounts.index') }}" class="rounded-2xl border border-yellow-200 bg-gradient-to-br from-yellow-50 to-amber-100 p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex items-center justify-between"><p class="text-sm text-yellow-700">Pending Accounts</p><span class="rounded-lg bg-white/70 p-2 text-yellow-700">→</span></div>
            <p class="mt-4 text-3xl font-bold text-yellow-800">{{ $pendingStudentAccounts }}</p>
            <p class="mt-1 text-xs text-yellow-700">Needs your attention</p>
        </a>
    </div>

    <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="sa-card p-5"><p class="text-sm text-gray-500">Admins</p><p class="mt-2 text-2xl font-bold text-gray-800">{{ $totalAdmins }}</p></div>
        <div class="sa-card p-5"><p class="text-sm text-gray-500">Registrars</p><p class="mt-2 text-2xl font-bold text-gray-800">{{ $totalRegistrars }}</p></div>
        <div class="sa-card p-5"><p class="text-sm text-gray-500">Teachers</p><p class="mt-2 text-2xl font-bold text-gray-800">{{ $totalTeachers }}</p></div>
        <div class="sa-card p-5"><p class="text-sm text-gray-500">SSG Officers</p><p class="mt-2 text-2xl font-bold text-gray-800">{{ $totalSSG }}</p><p class="mt-1 text-xs text-gray-400">Manage via <a href="{{ route('superadmin.role-management') }}" class="font-semibold text-green-700 hover:underline">Role Management</a></p></div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="sa-card p-6">
            <div class="flex items-center justify-between"><h2 class="text-base font-semibold text-gray-800">Quick Actions</h2><span class="text-xs font-semibold uppercase tracking-wider text-green-700">Shortcuts</span></div>
            <div class="mt-4 space-y-3">
                <a href="{{ route('superadmin.accounts') }}" class="block rounded-xl bg-gradient-to-r from-[#1a5c1a] to-[#0f766e] px-4 py-3 text-sm font-semibold text-white transition hover:shadow-md">Create or Manage Accounts</a>
                <a href="{{ route('superadmin.student-accounts.index') }}" class="block rounded-xl bg-yellow-50 px-4 py-3 text-sm font-semibold text-yellow-800 transition hover:bg-yellow-100">Generate Student Accounts</a>
                <a href="{{ route('superadmin.activity-logs') }}" class="block rounded-xl bg-green-50 px-4 py-3 text-sm font-semibold text-green-900 transition hover:bg-green-100">View System Activity</a>
                <a href="{{ route('superadmin.roles') }}" class="block rounded-xl bg-green-50 px-4 py-3 text-sm font-semibold text-green-900 transition hover:bg-green-100">Review Roles &amp; Access</a>
            </div>
        </div>

        <div class="sa-card xl:col-span-2">
            <div class="sa-card-header">
                <h2 class="text-base font-semibold text-gray-800">Recent System Activity</h2>
                <p class="mt-1 text-xs text-gray-500">The latest changes recorded in your system.</p>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($recentLogs as $log)
                    <div class="flex items-start justify-between gap-4 px-6 py-4">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $log->description }}</p>
                            <p class="mt-1 text-xs text-gray-500">{{ optional($log->causer)->name ?? 'System' }} <span class="text-green-600">•</span> {{ $log->event ?? 'activity' }}</p>
                        </div>
                        <p class="shrink-0 text-xs text-gray-400">{{ $log->created_at->diffForHumans() }}</p>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-sm text-gray-400">No activity logs yet.</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
