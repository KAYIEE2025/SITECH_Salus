@extends('layouts.app')
@section('title', 'School Calendar')
@section('sidebar-links')
    <x-nav-link href="{{ route('admin.dashboard') }}" :active="false">Dashboard</x-nav-link>
    <x-nav-link href="{{ route('admin.announcements.index') }}" :active="false">Announcements</x-nav-link>
    <x-nav-link href="{{ route('admin.calendar.index') }}" :active="true">School Calendar</x-nav-link>
    <x-nav-link href="{{ route('admin.reports.index') }}" :active="false">Reports</x-nav-link>
    <x-nav-link href="{{ route('admin.teacher-study-load') }}" :active="false">Teacher Study Load</x-nav-link>
    <x-nav-link href="{{ route('admin.activity-logs.index') }}" :active="false">Activity Logs</x-nav-link>
    <x-nav-link href="{{ route('admin.profile.index') }}" :active="false">My Profile</x-nav-link>
@endsection
@section('content')
    <x-delete-confirm-modal
        name="delete-calendar-modal"
        title="Delete School Event"
        message="Are you sure you want to delete this school event?"
        recordName=""
        recordDetails=""
    />
    @session('success')
        <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg px-4 py-3 mb-6">
            {{ $value }}
        </div>
    @endsession
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3 mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">School Calendar</h1>
        <a href="{{ route('admin.calendar.create') }}"
            class="bg-green-800 hover:bg-green-900 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            + Add Event
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="text-left px-6 py-3">Title</th>
                        <th class="text-left px-6 py-3">Description</th>
                        <th class="text-left px-6 py-3">Date</th>
                        <th class="text-left px-6 py-3">End Date</th>
                        <th class="text-left px-6 py-3">Created By</th>
                        <th class="text-right px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($events as $event)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 rounded-full" style="background-color: {{ $event->color }};"></div>
                                <p class="font-medium text-gray-800">{{ $event->title }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600 truncate max-w-xs">{{ $event->description ?? '—' }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $event->event_date ? $event->event_date->format('M d, Y') : 'N/A' }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $event->event_end_date ? $event->event_end_date->format('M d, Y') : '—' }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $event->creator ? $event->creator->name : 'System' }}</td>
                        <td class="px-6 py-4">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.calendar.edit', $event) }}"
                                    class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-600 px-3 py-1.5 rounded-lg transition">
                                    Edit
                                </a>
                                <button
                                    type="button"
                                    x-on:click="$dispatch('open-delete-modal', {
                                        modalName: 'delete-calendar-modal',
                                        id: {{ $event->id }},
                                        name: '{{ $event->title }}',
                                        details: 'Date: {{ $event->event_date ? $event->event_date->format('M d, Y') : 'N/A' }}',
                                        action: '/admin/calendar/{{ $event->id }}'
                                    })"
                                    class="text-xs bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-lg transition"
                                >
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                            No school events yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($events->hasPages())
        <div class="px-4 py-3 md:px-6 md:py-4 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-2 md:gap-0">
            <p class="text-xs md:text-sm text-gray-600">
                Showing {{ $events->firstItem() }} to {{ $events->lastItem() }} of {{ $events->total() }} entries
            </p>
            {{ $events->links() }}
        </div>
        @endif
    </div>
@endsection
