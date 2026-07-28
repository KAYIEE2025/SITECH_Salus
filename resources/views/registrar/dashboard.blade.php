@extends('layouts.app')

@section('title', 'Registrar Dashboard')

@section('content')
    <div class="mb-8 overflow-hidden rounded-2xl bg-gradient-to-br from-[#14532d] via-[#1a5c1a] to-[#0f766e] p-6 text-white shadow-lg shadow-green-900/10 sm:p-8">
        <div class="flex flex-col justify-between gap-6 sm:flex-row sm:items-end">
            <div>
                <p class="text-sm font-medium text-green-100">Registrar workspace</p>
                <h2 class="mt-2 max-w-xl text-2xl font-bold tracking-tight sm:text-3xl">Keep every academic record moving.</h2>
                <p class="mt-2 max-w-lg text-sm leading-6 text-green-100">Manage student records, class schedules, study loads, and grade approvals from one organized workspace.</p>
            </div>
            <div class="rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm backdrop-blur-sm">
                <p class="text-xs text-green-100">Workspace status</p>
                <p class="mt-1 flex items-center gap-2 font-semibold"><span class="h-2 w-2 rounded-full bg-emerald-300"></span>Ready for today</p>
            </div>
        </div>
    </div>

    <div class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="ra-card p-5">
            <div class="flex items-center justify-between"><p class="text-sm text-gray-500">Total Students</p><span class="rounded-lg bg-green-50 p-2 text-green-700">◉</span></div>
            <p class="mt-4 text-3xl font-bold text-green-900">{{ $totalStudents }}</p>
            <p class="mt-1 text-xs text-gray-400">Student records in the system</p>
        </div>
        <div class="ra-card p-5">
            <div class="flex items-center justify-between"><p class="text-sm text-gray-500">Pending Grade Approvals</p><span class="rounded-lg bg-amber-50 p-2 text-amber-700">!</span></div>
            <p class="mt-4 text-3xl font-bold text-amber-700">{{ $pendingGrades }}</p>
            <p class="mt-1 text-xs text-gray-400">Submissions requiring review</p>
        </div>
        <div class="ra-card p-5">
            <div class="flex items-center justify-between"><p class="text-sm text-gray-500">Active Sections</p><span class="rounded-lg bg-emerald-50 p-2 text-emerald-700">✓</span></div>
            <p class="mt-4 text-3xl font-bold text-green-900">{{ $activeSections }}</p>
            <p class="mt-1 text-xs text-gray-400">Available class sections</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="ra-card p-6">
            <h2 class="text-base font-semibold text-gray-800">Common tasks</h2>
            <p class="mt-1 text-sm text-gray-500">Jump directly to the records you manage most.</p>
            <div class="mt-5 grid gap-3 sm:grid-cols-2">
                <a href="{{ route('registrar.students') }}" class="rounded-xl bg-green-50 px-4 py-3 text-sm font-semibold text-green-900 transition hover:bg-green-100">Student records</a>
                <a href="{{ route('registrar.study-load') }}" class="rounded-xl bg-green-50 px-4 py-3 text-sm font-semibold text-green-900 transition hover:bg-green-100">Study load</a>
                <a href="{{ route('registrar.grade-approval') }}" class="rounded-xl bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-800 transition hover:bg-amber-100">Grade approval</a>
                <a href="{{ route('registrar.study-load') }}" class="rounded-xl bg-green-50 px-4 py-3 text-sm font-semibold text-green-900 transition hover:bg-green-100">Class schedules</a>
            </div>
        </div>
        <div class="ra-card p-6">
            <h2 class="text-base font-semibold text-gray-800">Registrar checklist</h2>
            <div class="mt-5 space-y-4">
                <div class="flex gap-3"><span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-green-100 text-xs font-bold text-green-700">1</span><p class="text-sm text-gray-600">Keep student profiles and sections up to date.</p></div>
                <div class="flex gap-3"><span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-green-100 text-xs font-bold text-green-700">2</span><p class="text-sm text-gray-600">Review submitted grades before approval.</p></div>
                <div class="flex gap-3"><span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-green-100 text-xs font-bold text-green-700">3</span><p class="text-sm text-gray-600">Confirm schedules and study loads are accurate.</p></div>
            </div>
        </div>
    </div>
@endsection
