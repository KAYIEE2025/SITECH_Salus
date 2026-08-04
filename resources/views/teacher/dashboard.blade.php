@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('content')
    <div class="mb-6 md:mb-8 overflow-hidden rounded-2xl bg-gradient-to-br from-[#14532d] via-[#1a5c1a] to-[#0f766e] p-4 md:p-6 text-white shadow-lg shadow-green-900/10 sm:p-8">
        <div class="flex flex-col justify-between gap-4 md:gap-6 sm:flex-row sm:items-end">
            <div class="min-w-0">
                <p class="text-xs md:text-sm font-medium text-green-100">Teacher workspace</p>
                <h2 class="mt-1 md:mt-2 text-lg md:text-2xl font-bold tracking-tight sm:text-3xl">Teach, track, and submit with confidence.</h2>
                <p class="mt-1 md:mt-2 max-w-lg text-xs md:text-sm leading-5 md:leading-6 text-green-100">Manage your classes, student lists, announcements, and grade submissions from one focused workspace.</p>
            </div>
            <a href="{{ route('teacher.classes.index') }}" class="rounded-xl bg-gradient-to-r from-[#f4d35e] to-[#d6ad22] px-4 py-3 text-xs md:text-sm font-semibold text-green-950 shadow-md transition hover:shadow-lg min-h-[48px] flex items-center justify-center shrink-0">View my classes</a>
        </div>
    </div>

    <div class="mb-6 md:mb-8 grid grid-cols-1 gap-3 md:gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="tc-card p-4 md:p-5"><div class="flex items-center justify-between"><p class="text-xs md:text-sm text-gray-500">Assigned Classes</p><span class="rounded-lg bg-green-50 p-1.5 md:p-2 text-green-700 text-xs md:text-sm">◉</span></div><p class="mt-3 md:mt-4 text-2xl md:text-3xl font-bold text-green-900">{{ $totalClasses }}</p><p class="mt-1 text-[10px] md:text-xs text-gray-400">Your current classes</p></div>
        <div class="tc-card p-4 md:p-5"><div class="flex items-center justify-between"><p class="text-xs md:text-sm text-gray-500">Assigned Students</p><span class="rounded-lg bg-green-50 p-1.5 md:p-2 text-green-700 text-xs md:text-sm">◎</span></div><p class="mt-3 md:mt-4 text-2xl md:text-3xl font-bold text-green-900">{{ $totalStudents }}</p><p class="mt-1 text-[10px] md:text-xs text-gray-400">Students under your classes</p></div>
        <div class="tc-card p-4 md:p-5"><div class="flex items-center justify-between"><p class="text-xs md:text-sm text-gray-500">Pending Submissions</p><span class="rounded-lg bg-amber-50 p-1.5 md:p-2 text-amber-700 text-xs md:text-sm">!</span></div><p class="mt-3 md:mt-4 text-2xl md:text-3xl font-bold text-amber-700">{{ $pendingSubmissions }}</p><p class="mt-1 text-[10px] md:text-xs text-gray-400">Grades needing attention</p></div>
        <div class="tc-card p-4 md:p-5"><div class="flex items-center justify-between"><p class="text-xs md:text-sm text-gray-500">Announcements</p><span class="rounded-lg bg-emerald-50 p-1.5 md:p-2 text-emerald-700 text-xs md:text-sm">→</span></div><p class="mt-3 md:mt-4 text-2xl md:text-3xl font-bold text-green-900">{{ $announcements->count() }}</p><p class="mt-1 text-[10px] md:text-xs text-gray-400">Latest notices</p></div>
    </div>

    @if($announcements->isNotEmpty())
        <div class="tc-card p-4 md:p-6">
            <div class="flex flex-col gap-2 md:gap-0 sm:flex-row sm:items-center sm:justify-between"><div><h2 class="text-sm md:text-lg font-semibold text-gray-800">Latest Announcements</h2><p class="mt-1 text-xs md:text-sm text-gray-500">Stay up to date with school notices.</p></div><a href="{{ route('teacher.announcements.index') }}" class="text-xs md:text-xs font-semibold text-green-700 hover:text-green-900">View all</a></div>
            <div class="mt-4 md:mt-5 grid gap-3 md:gap-4 grid-cols-1 md:grid-cols-2">
                @foreach($announcements as $announcement)
                    <div class="rounded-xl border border-green-100 bg-green-50/30 p-4 md:p-5"><h3 class="font-medium text-gray-800 text-sm md:text-base">{{ $announcement->title }}</h3><p class="mt-2 text-xs md:text-sm leading-5 md:leading-6 text-gray-600">{{ Str::limit($announcement->content, 150) }}</p><p class="mt-3 text-[10px] md:text-xs text-gray-400">{{ $announcement->created_at->format('M d, Y') }}</p></div>
                @endforeach
            </div>
        </div>
    @endif
@endsection
