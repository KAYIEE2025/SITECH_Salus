@extends('layouts.app')

@section('title', 'Super Admin Dashboard')

@section('content')
    @session('success')
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ $value }}
        </div>
    @endsession
    @session('error')
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $value }}
        </div>
    @endsession

    <div class="mb-6 md:mb-8 overflow-hidden rounded-2xl bg-gradient-to-br from-[#14532d] via-[#1a5c1a] to-[#0f766e] p-4 md:p-6 text-white shadow-lg shadow-green-900/10 sm:p-8">
        <div class="flex flex-col justify-between gap-4 md:gap-6 sm:flex-row sm:items-end">
            <div class="min-w-0">
                <p class="text-xs md:text-sm font-medium text-green-100">Welcome back, {{ auth()->user()->name }}</p>
                <h2 class="mt-1 md:mt-2 text-lg md:text-2xl font-bold tracking-tight sm:text-3xl">Your system at a glance.</h2>
                <p class="mt-1 md:mt-2 max-w-lg text-xs md:text-sm leading-5 md:leading-6 text-green-100">Monitor accounts, access roles, student onboarding, and important activity from one place.</p>
            </div>
            <div class="rounded-xl border border-white/20 bg-white/10 px-3 py-2 md:px-4 md:py-3 text-xs md:text-sm backdrop-blur-sm shrink-0">
                <p class="text-[10px] md:text-xs text-green-100">Access level</p>
                <p class="mt-1 font-semibold text-xs md:text-sm">Super Administrator</p>
            </div>
        </div>
    </div>

    <div class="mb-6 md:mb-8 grid grid-cols-1 gap-3 md:gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <div class="sa-card p-4 md:p-5">
            <div class="flex items-center justify-between"><p class="text-xs md:text-sm text-gray-500">Total Accounts</p><span class="rounded-lg bg-green-50 p-1.5 md:p-2 text-green-700 text-xs md:text-sm">◎</span></div>
            <p class="mt-3 md:mt-4 text-2xl md:text-3xl font-bold text-green-900">{{ $totalUsers }}</p>
            <p class="mt-1 text-[10px] md:text-xs text-gray-400">All registered users</p>
        </div>
        <div class="sa-card p-4 md:p-5">
            <div class="flex items-center justify-between"><p class="text-xs md:text-sm text-gray-500">Active Accounts</p><span class="rounded-lg bg-emerald-50 p-1.5 md:p-2 text-emerald-700 text-xs md:text-sm">✓</span></div>
            <p class="mt-3 md:mt-4 text-2xl md:text-3xl font-bold text-green-900">{{ $activeUsers }}</p>
            <p class="mt-1 text-[10px] md:text-xs text-gray-400">Currently enabled</p>
        </div>
        <div class="sa-card p-4 md:p-5">
            <div class="flex items-center justify-between"><p class="text-xs md:text-sm text-gray-500">Inactive Accounts</p><span class="rounded-lg bg-red-50 p-1.5 md:p-2 text-red-600 text-xs md:text-sm">!</span></div>
            <p class="mt-3 md:mt-4 text-2xl md:text-3xl font-bold text-red-600">{{ $inactiveUsers }}</p>
            <p class="mt-1 text-[10px] md:text-xs text-gray-400">Review when necessary</p>
        </div>
        <div class="sa-card p-4 md:p-5">
            <div class="flex items-center justify-between"><p class="text-xs md:text-sm text-gray-500">Students</p><span class="rounded-lg bg-amber-50 p-1.5 md:p-2 text-amber-700 text-xs md:text-sm">◉</span></div>
            <p class="mt-3 md:mt-4 text-2xl md:text-3xl font-bold text-amber-700">{{ $totalStudents }}</p>
            <p class="mt-1 text-[10px] md:text-xs text-gray-400">Student records</p>
        </div>
        <a href="{{ route('superadmin.student-accounts.index') }}" class="rounded-2xl border border-yellow-200 bg-gradient-to-br from-yellow-50 to-amber-100 p-4 md:p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex items-center justify-between"><p class="text-xs md:text-sm text-yellow-700">Pending Accounts</p><span class="rounded-lg bg-white/70 p-1.5 md:p-2 text-yellow-700 text-xs md:text-sm">→</span></div>
            <p class="mt-3 md:mt-4 text-2xl md:text-3xl font-bold text-yellow-800">{{ $pendingStudentAccounts }}</p>
            <p class="mt-1 text-[10px] md:text-xs text-yellow-700">Needs your attention</p>
        </a>
    </div>

    <div class="mb-6 md:mb-8 grid grid-cols-1 gap-3 md:gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="sa-card p-4 md:p-5 flex flex-col items-center justify-center text-center"><p class="text-xs md:text-sm text-gray-500">Admins</p><p class="mt-2 text-xl md:text-2xl font-bold text-gray-800">{{ $totalAdmins }}</p></div>
        <div class="sa-card p-4 md:p-5 flex flex-col items-center justify-center text-center"><p class="text-xs md:text-sm text-gray-500">Registrars</p><p class="mt-2 text-xl md:text-2xl font-bold text-gray-800">{{ $totalRegistrars }}</p></div>
        <div class="sa-card p-4 md:p-5 flex flex-col items-center justify-center text-center"><p class="text-xs md:text-sm text-gray-500">Teachers</p><p class="mt-2 text-xl md:text-2xl font-bold text-gray-800">{{ $totalTeachers }}</p></div>
        <div class="sa-card p-4 md:p-5 flex flex-col items-center justify-center text-center"><p class="text-xs md:text-sm text-gray-500">SSG Officers</p><p class="mt-2 text-xl md:text-2xl font-bold text-gray-800">{{ $totalSSG }}</p><p class="mt-1 text-[10px] md:text-xs text-gray-400">Manage via <a href="{{ route('superadmin.role-management') }}" class="font-semibold text-green-700 hover:underline">Role Management</a></p></div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:gap-6 xl:grid-cols-3">
        <div class="sa-card p-4 md:p-6">
            <div class="flex items-center justify-between"><h2 class="text-sm md:text-base font-semibold text-gray-800">Quick Actions</h2><span class="text-[10px] md:text-xs font-semibold uppercase tracking-wider text-green-700">Shortcuts</span></div>
            <div class="mt-3 md:mt-4 space-y-2 md:space-y-3">
                <a href="{{ route('superadmin.accounts') }}" class="block rounded-xl bg-gradient-to-r from-[#1a5c1a] to-[#0f766e] px-4 py-3 md:py-3 text-xs md:text-sm font-semibold text-white transition hover:shadow-md min-h-[48px] flex items-center justify-center">Create or Manage Accounts</a>
                <a href="{{ route('superadmin.student-accounts.index') }}" class="block rounded-xl bg-yellow-50 px-4 py-3 md:py-3 text-xs md:text-sm font-semibold text-yellow-800 transition hover:bg-yellow-100 min-h-[48px] flex items-center justify-center">Generate Student Accounts</a>
                <a href="{{ route('superadmin.school-years.index') }}" class="block rounded-xl bg-blue-50 px-4 py-3 md:py-3 text-xs md:text-sm font-semibold text-blue-800 transition hover:bg-blue-100 min-h-[48px] flex items-center justify-center">School Year Management</a>
                <a href="{{ route('superadmin.activity-logs') }}" class="block rounded-xl bg-green-50 px-4 py-3 md:py-3 text-xs md:text-sm font-semibold text-green-900 transition hover:bg-green-100 min-h-[48px] flex items-center justify-center">View System Activity</a>
                <a href="{{ route('superadmin.roles') }}" class="block rounded-xl bg-green-50 px-4 py-3 md:py-3 text-xs md:text-sm font-semibold text-green-900 transition hover:bg-green-100 min-h-[48px] flex items-center justify-center">Review Roles &amp; Access</a>
                <button onclick="openResetModal()" class="block w-full rounded-xl bg-red-50 px-4 py-3 md:py-3 text-xs md:text-sm font-semibold text-red-700 transition hover:bg-red-100 min-h-[48px] flex items-center justify-center">Archive &amp; Reset SSG Records</button>
            </div>
        </div>

        <div class="sa-card xl:col-span-2">
            <div class="sa-card-header">
                <h2 class="text-sm md:text-base font-semibold text-gray-800">Recent System Activity</h2>
                <p class="mt-1 text-[10px] md:text-xs text-gray-500">The latest changes recorded in your system.</p>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($recentLogs as $log)
                    <div class="flex items-start justify-between gap-3 md:gap-4 px-4 py-3 md:px-6 md:py-4">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs md:text-sm font-medium text-gray-800 break-words">{{ $log->description }}</p>
                            <p class="mt-1 text-[10px] md:text-xs text-gray-500 break-words">{{ optional($log->causer)->name ?? 'System' }} <span class="text-green-600">•</span> {{ $log->event ?? 'activity' }}</p>
                        </div>
                        <p class="shrink-0 text-[10px] md:text-xs text-gray-400">{{ $log->created_at->diffForHumans() }}</p>
                    </div>
                @empty
                    <div class="px-4 py-6 md:px-6 md:py-8 text-center text-xs md:text-sm text-gray-400">No activity logs yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- SSG Reset Confirmation Modal -->
    <div id="resetModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
        <div class="w-full max-w-md mx-4 rounded-xl bg-white p-6">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-800">Archive & Reset SSG Records</h2>
                <button onclick="closeResetModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="mb-6">
                <p class="text-sm text-gray-600">Have you already generated and printed the official SSG Fine Report? This action will reset the current SSG attendance and fine records.</p>
                <p class="mt-2 text-xs text-red-600 font-medium">This action cannot be undone.</p>
            </div>
            <div class="flex gap-3">
                <button onclick="closeResetModal()" class="flex-1 rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-200">
                    Cancel
                </button>
                <form method="POST" action="{{ route('superadmin.ssg-reset') }}" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700">
                        Confirm Reset
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openResetModal() {
            document.getElementById('resetModal').classList.remove('hidden');
            document.getElementById('resetModal').classList.add('flex');
        }

        function closeResetModal() {
            document.getElementById('resetModal').classList.add('hidden');
            document.getElementById('resetModal').classList.remove('flex');
        }

        // Close modal when clicking outside
        document.getElementById('resetModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeResetModal();
            }
        });
    </script>
@endsection
