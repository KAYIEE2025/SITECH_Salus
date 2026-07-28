@extends('layouts.app')

@section('title', 'SSG Dashboard')

@section('content')
    <div class="mb-8 overflow-hidden rounded-2xl bg-gradient-to-br from-[#14532d] via-[#1a5c1a] to-[#0f766e] p-6 text-white shadow-lg shadow-green-900/10 sm:p-8">
        <div class="flex flex-col justify-between gap-6 sm:flex-row sm:items-end">
            <div>
                <p class="text-sm font-medium text-green-100">Student Supreme Government</p>
                <h2 class="mt-2 max-w-xl text-2xl font-bold tracking-tight sm:text-3xl">Make every campus activity count.</h2>
                <p class="mt-2 max-w-lg text-sm leading-6 text-green-100">Coordinate events, monitor attendance, and keep fine records organized for the student community.</p>
            </div>
            <a href="{{ route('ssg.events.create') }}" class="rounded-xl bg-gradient-to-r from-[#f4d35e] to-[#d6ad22] px-4 py-3 text-sm font-semibold text-green-950 shadow-md transition hover:shadow-lg">+ Create Event</a>
        </div>
    </div>

    <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="sg-card p-5"><div class="flex items-center justify-between"><p class="text-sm text-gray-500">Total Events</p><span class="rounded-lg bg-green-50 p-2 text-green-700">◉</span></div><p class="mt-4 text-3xl font-bold text-green-900">{{ $totalEvents }}</p><p class="mt-1 text-xs text-gray-400">All recorded activities</p></div>
        <div class="sg-card p-5"><div class="flex items-center justify-between"><p class="text-sm text-gray-500">Upcoming Events</p><span class="rounded-lg bg-amber-50 p-2 text-amber-700">→</span></div><p class="mt-4 text-3xl font-bold text-amber-700">{{ $upcomingEvents }}</p><p class="mt-1 text-xs text-gray-400">Planning ahead</p></div>
        <div class="sg-card p-5"><div class="flex items-center justify-between"><p class="text-sm text-gray-500">Ongoing Events</p><span class="rounded-lg bg-emerald-50 p-2 text-emerald-700">✓</span></div><p class="mt-4 text-3xl font-bold text-green-900">{{ $ongoingEvents }}</p><p class="mt-1 text-xs text-gray-400">Currently active</p></div>
        <div class="sg-card p-5"><div class="flex items-center justify-between"><p class="text-sm text-gray-500">Fines Collected</p><span class="rounded-lg bg-green-50 p-2 text-green-700">₱</span></div><p class="mt-4 text-2xl font-bold text-green-900">PHP {{ number_format($totalFinesCollected, 2) }}</p><p class="mt-1 text-xs text-gray-400">Recorded collections</p></div>
    </div>

    <div class="sg-card overflow-hidden">
        <div class="border-b border-green-50 px-5 py-4 sm:px-6"><h2 class="text-base font-semibold text-gray-800">Recent Events</h2><p class="mt-1 text-xs text-gray-500">Your latest student government activities.</p></div>
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead><tr><th class="px-6 py-3 text-left">Event Title</th><th class="px-6 py-3 text-left">Event Date</th><th class="px-6 py-3 text-left">Venue</th><th class="px-6 py-3 text-left">Status</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($recentEvents as $event)
                    <tr><td class="px-6 py-4 font-medium text-gray-800">{{ $event->title }}</td><td class="px-6 py-4 text-gray-600">{{ $event->event_date->format('M d, Y') }}</td><td class="px-6 py-4 text-gray-600">{{ $event->venue ?: 'N/A' }}</td><td class="px-6 py-4"><span class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700">{{ $event->status }}</span></td></tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400">No SSG events yet.</td></tr>
                @endforelse
            </tbody>
        </table></div>
    </div>
@endsection
