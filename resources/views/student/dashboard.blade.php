@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
    @if(!$student)
        <div class="rounded-2xl border border-yellow-200 bg-gradient-to-br from-yellow-50 to-amber-50 p-8 text-center">
            <h2 class="text-xl font-semibold text-yellow-800">Student Profile Not Found</h2>
            <p class="mt-2 text-sm leading-6 text-yellow-700">Your student profile has not been encoded yet. Please contact the Registrar's office to complete your profile setup.</p>
        </div>
    @else
        <div class="mb-8 overflow-hidden rounded-2xl bg-gradient-to-br from-[#14532d] via-[#1a5c1a] to-[#0f766e] p-6 text-white shadow-lg shadow-green-900/10 sm:p-8">
            <div class="flex flex-col justify-between gap-6 sm:flex-row sm:items-end">
                <div>
                    <p class="text-sm font-medium text-green-100">Welcome back, {{ $student->first_name }}</p>
                    <h2 class="mt-2 max-w-xl text-2xl font-bold tracking-tight sm:text-3xl">Your student life, in one place.</h2>
                    <p class="mt-2 max-w-lg text-sm leading-6 text-green-100">Check your study load, approved grades, school events, and important student information.</p>
                </div>
                <div class="rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm backdrop-blur-sm">
                    <p class="text-xs text-green-100">School year</p>
                    <p class="mt-1 font-semibold">{{ $student->school_year }}</p>
                </div>
            </div>
        </div>

        <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="st-card p-5"><div class="flex items-center justify-between"><p class="text-sm text-gray-500">Subjects Enrolled</p><span class="rounded-lg bg-green-50 p-2 text-green-700">◉</span></div><p class="mt-4 text-3xl font-bold text-green-900">{{ $totalSubjects }}</p><p class="mt-1 text-xs text-gray-400">Current study load</p></div>
            <div class="st-card p-5"><div class="flex items-center justify-between"><p class="text-sm text-gray-500">Approved Grades</p><span class="rounded-lg bg-emerald-50 p-2 text-emerald-700">✓</span></div><p class="mt-4 text-3xl font-bold text-green-900">{{ $approvedGrades }}</p><p class="mt-1 text-xs text-gray-400">Officially released</p></div>
            <div class="st-card p-5"><div class="flex items-center justify-between"><p class="text-sm text-gray-500">Outstanding Balance</p><span class="rounded-lg bg-amber-50 p-2 text-amber-700">₱</span></div><p class="mt-4 text-3xl font-bold {{ $outstandingFineBalance > 0 ? 'text-red-600' : 'text-green-900' }}">₱{{ number_format($outstandingFineBalance, 2) }}</p><p class="mt-1 text-xs text-gray-400">SSG fine balance</p></div>
            <div class="st-card p-5"><div class="flex items-center justify-between"><p class="text-sm text-gray-500">Upcoming Events</p><span class="rounded-lg bg-green-50 p-2 text-green-700">→</span></div><p class="mt-4 text-3xl font-bold text-green-900">{{ $upcomingSsgEvents->count() }}</p><p class="mt-1 text-xs text-gray-400">SSG activities</p></div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="st-card p-6 lg:col-span-2">
                <div class="flex items-center justify-between"><h2 class="text-lg font-semibold text-gray-800">Student Information</h2><a href="{{ route('student.profile.index') }}" class="text-xs font-semibold text-green-700 hover:text-green-900">View profile</a></div>
                <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div><p class="text-xs font-semibold uppercase tracking-wide text-green-800/70">Student number</p><p class="mt-1 text-base font-medium text-gray-800">{{ $student->student_number }}</p></div>
                    <div><p class="text-xs font-semibold uppercase tracking-wide text-green-800/70">Student name</p><p class="mt-1 text-base font-medium text-gray-800">{{ $student->last_name }}, {{ $student->first_name }} {{ $student->middle_name }}</p></div>
                    <div><p class="text-xs font-semibold uppercase tracking-wide text-green-800/70">Grade level</p><p class="mt-1 text-base font-medium text-gray-800">{{ $student->yearLevel->name ?? 'N/A' }}</p></div>
                    <div><p class="text-xs font-semibold uppercase tracking-wide text-green-800/70">Section</p><p class="mt-1 text-base font-medium text-gray-800">{{ $student->section->name ?? 'N/A' }}</p></div>
                </div>
            </div>

            <div class="st-card p-6">
                <div class="flex items-center justify-between"><h2 class="text-lg font-semibold text-gray-800">Student QR Code</h2><span class="rounded-full bg-green-50 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wider text-green-700">ID</span></div>
                <div class="mt-5 flex min-h-48 items-center justify-center rounded-xl border border-green-100 bg-green-50/40 p-4">
                    @if($student && $student->qr_code_path)
                        <img src="{{ asset('storage/' . $student->qr_code_path) }}" alt="Student QR Code" class="h-44 w-44 object-contain">
                    @else
                        <p class="text-center text-sm text-gray-500">No QR Code Available</p>
                    @endif
                </div>
            </div>
        </div>

        @if($upcomingSsgEvents->isNotEmpty())
            <div class="st-card mt-6 p-6">
                <div class="flex items-center justify-between"><div><h2 class="text-lg font-semibold text-gray-800">Upcoming SSG Events</h2><p class="mt-1 text-sm text-gray-500">Stay informed about upcoming activities.</p></div><a href="{{ route('student.ssg-events.index') }}" class="text-xs font-semibold text-green-700 hover:text-green-900">View all</a></div>
                <div class="mt-5 grid gap-3 md:grid-cols-2">
                    @foreach($upcomingSsgEvents as $event)
                        <div class="flex items-center justify-between gap-4 rounded-xl bg-green-50/70 p-4"><div><p class="font-medium text-gray-800">{{ $event->title }}</p><p class="mt-1 text-sm text-gray-500">{{ $event->event_date->format('M d, Y - g:i A') }}</p></div><span class="shrink-0 rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">{{ $event->event_date->diffForHumans() }}</span></div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif
@endsection
