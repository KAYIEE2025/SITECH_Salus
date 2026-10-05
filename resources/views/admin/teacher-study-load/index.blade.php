@extends('layouts.app')
@section('title', 'Teacher Study Load')
@section('sidebar-links')
    <x-nav-link href="{{ route('admin.dashboard') }}" :active="false">Dashboard</x-nav-link>
    <x-nav-link href="{{ route('admin.announcements.index') }}" :active="false">Announcements</x-nav-link>
    <x-nav-link href="{{ route('admin.calendar.index') }}" :active="false">School Calendar</x-nav-link>
    <x-nav-link href="{{ route('admin.reports.index') }}" :active="false">Reports</x-nav-link>
    <x-nav-link href="{{ route('admin.teacher-study-load') }}" :active="true">Teacher Study Load</x-nav-link>
    <x-nav-link href="{{ route('admin.activity-logs.index') }}" :active="false">Activity Logs</x-nav-link>
    <x-nav-link href="{{ route('admin.profile.index') }}" :active="false">My Profile</x-nav-link>
@endsection
@section('content')
    <div class="mb-4 md:mb-6">
        <h1 class="text-xl md:text-2xl font-bold text-gray-800">Teacher Study Load</h1>
        <p class="text-xs md:text-sm text-gray-600 mt-1">View teacher schedules and assigned study loads.</p>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 md:p-6 mb-6">
        <form method="GET" action="{{ route('admin.teacher-study-load') }}" class="flex flex-col gap-4" x-data="{ loading: false }">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">Search Teacher Name</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search by teacher name..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
                        x-model.debounce.500ms="search"
                        @input="$el.closest('form').submit()">
                </div>
                <div class="flex-1">
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">School Year</label>
                    <select name="school_year"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                        <option value="">All School Years</option>
                        @foreach($schoolYears as $schoolYear)
                            <option value="{{ $schoolYear }}" {{ request('school_year') == $schoolYear ? 'selected' : '' }}>
                                {{ $schoolYear }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit"
                    class="bg-green-800 hover:bg-green-900 text-white text-xs md:text-sm font-medium px-4 py-2 rounded-lg transition">
                    Search
                </button>
                <a href="{{ route('admin.teacher-study-load') }}"
                    class="border border-gray-300 text-gray-700 text-xs md:text-sm font-medium px-4 py-2 rounded-lg hover:bg-gray-50 transition">
                    Clear
                </a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-max text-xs md:text-sm">
                <thead class="bg-gray-50 text-gray-500 text-[10px] md:text-xs uppercase">
                    <tr>
                        <th class="text-left px-2 py-2 md:px-6 md:py-3">Teacher</th>
                        <th class="text-left px-2 py-2 md:px-6 md:py-3">Subject</th>
                        <th class="text-left px-2 py-2 md:px-6 md:py-3">Section</th>
                        <th class="text-left px-2 py-2 md:px-6 md:py-3">Room</th>
                        <th class="text-left px-2 py-2 md:px-6 md:py-3">Day</th>
                        <th class="text-left px-2 py-2 md:px-6 md:py-3">Time</th>
                        <th class="text-left px-2 py-2 md:px-6 md:py-3">School Year</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($schedules as $schedule)
                    <tr class="hover:bg-gray-50">
                        <td class="px-2 py-2 md:px-6 md:py-4 text-gray-800 font-medium">
                            {{ $schedule->teacher?->name ?? '—' }}
                        </td>
                        <td class="px-2 py-2 md:px-6 md:py-4">
                            <p class="font-medium text-gray-800">{{ $schedule->subject?->code ?? '—' }}</p>
                            <p class="text-[10px] md:text-xs text-gray-500">{{ $schedule->subject?->name ?? '' }}</p>
                        </td>
                        <td class="px-2 py-2 md:px-6 md:py-4 text-gray-600">
                            {{ $schedule->section?->yearLevel?->name ?? '' }} — Sec {{ $schedule->section?->name ?? '—' }}
                        </td>
                        <td class="px-2 py-2 md:px-6 md:py-4 text-gray-600">
                            {{ $schedule->room ?? '—' }}
                        </td>
                        <td class="px-2 py-2 md:px-6 md:py-4 text-gray-600">
                            {{ implode(', ', $schedule->days ?? []) }}
                        </td>
                        <td class="px-2 py-2 md:px-6 md:py-4 text-gray-600 text-[10px] md:text-xs">
                            @if($schedule->time_start && $schedule->time_end)
                                {{ \Carbon\Carbon::parse($schedule->time_start)->format('g:i A') }} – 
                                {{ \Carbon\Carbon::parse($schedule->time_end)->format('g:i A') }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-2 py-2 md:px-6 md:py-4 text-gray-600">
                            {{ $schedule->school_year ?? '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-2 py-4 md:px-6 md:py-8 text-center text-gray-400 text-[10px] md:text-xs">
                            No teacher study load records found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($schedules->hasPages())
        <div class="px-4 py-3 md:px-6 md:py-4 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-2 md:gap-0">
            <p class="text-xs md:text-sm text-gray-600">
                Showing {{ $schedules->firstItem() }} to {{ $schedules->lastItem() }} of {{ $schedules->total() }} entries
            </p>
            {{ $schedules->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
@endsection
