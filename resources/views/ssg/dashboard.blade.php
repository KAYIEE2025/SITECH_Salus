@extends('layouts.app')

@section('title', 'SSG Dashboard')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">SSG Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500">Event management overview for student government activities.</p>
        </div>
        <a href="{{ route('ssg.events.create') }}"
            class="rounded-lg bg-green-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-green-900">
            + Create Event
        </a>
    </div>

    <div class="grid gap-6 md:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <p class="mb-1 text-sm text-gray-500">Total Events</p>
            <p class="text-3xl font-bold text-green-800">{{ $totalEvents }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <p class="mb-1 text-sm text-gray-500">Upcoming Events</p>
            <p class="text-3xl font-bold text-green-800">{{ $upcomingEvents }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <p class="mb-1 text-sm text-gray-500">Ongoing Events</p>
            <p class="text-3xl font-bold text-green-800">{{ $ongoingEvents }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <p class="mb-1 text-sm text-gray-500">Total Fines Collected</p>
            <p class="text-3xl font-bold text-green-800">PHP {{ number_format($totalFinesCollected, 2) }}</p>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-xl border border-gray-200 bg-white">
        <div class="border-b border-gray-100 px-6 py-4">
            <h2 class="text-base font-semibold text-gray-800">Recent Events</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-6 py-3 text-left">Event Title</th>
                    <th class="px-6 py-3 text-left">Event Date</th>
                    <th class="px-6 py-3 text-left">Venue</th>
                    <th class="px-6 py-3 text-left">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($recentEvents as $event)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-800">{{ $event->title }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $event->event_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $event->venue ?: 'N/A' }}</td>
                        <td class="px-6 py-4">
                            <span class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700">{{ $event->status }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-400">No SSG events yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
