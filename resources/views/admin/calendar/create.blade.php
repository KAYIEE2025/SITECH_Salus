@extends('layouts.app')
@section('title', 'Add School Event')
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
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3 md:gap-0 mb-4 md:mb-6">
        <h1 class="text-xl md:text-2xl font-bold text-gray-800">Add School Event</h1>
        <a href="{{ route('admin.calendar.index') }}"
            class="text-xs md:text-sm text-gray-600 hover:text-gray-800 whitespace-nowrap">
            ← Back to Calendar
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-4 md:p-6">
        <form method="POST" action="{{ route('admin.calendar.store') }}">
            @csrf

            <div class="mb-3 md:mb-4">
                <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">
                    Event Title <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title') }}"
                    class="w-full border border-gray-300 rounded-lg px-2 py-1.5 md:px-3 md:py-2 text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
                    placeholder="Event title">
                @error('title') <p class="text-red-500 text-[10px] md:text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-3 md:mb-4">
                <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">
                    Description
                </label>
                <textarea name="description" rows="3"
                    class="w-full border border-gray-300 rounded-lg px-2 py-1.5 md:px-3 md:py-2 text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
                    placeholder="Event description">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-500 text-[10px] md:text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 gap-3 mb-3 sm:grid-cols-2 md:gap-4 md:mb-4">
                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">
                        Event Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="event_date" value="{{ old('event_date') }}"
                        class="w-full border border-gray-300 rounded-lg px-2 py-1.5 md:px-3 md:py-2 text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    @error('event_date') <p class="text-red-500 text-[10px] md:text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">
                        End Date (optional)
                    </label>
                    <input type="date" name="event_end_date" value="{{ old('event_end_date') }}"
                        class="w-full border border-gray-300 rounded-lg px-2 py-1.5 md:px-3 md:py-2 text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
                        placeholder="For multi-day events">
                    @error('event_end_date') <p class="text-red-500 text-[10px] md:text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mb-4 md:mb-6">
                <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">
                    Color
                </label>
                <div class="flex flex-wrap gap-2 md:gap-3">
                    <label class="flex items-center">
                        <input type="radio" name="color" value="#3b82f6" {{ old('color', '#3b82f6') == '#3b82f6' ? 'checked' : '' }}
                            class="w-4 h-4 md:w-4 md:h-4 text-blue-600 border-gray-300 focus:ring-blue-600">
                        <span class="ml-2 text-xs md:text-sm text-gray-700">Blue</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="color" value="#10b981" {{ old('color') == '#10b981' ? 'checked' : '' }}
                            class="w-4 h-4 md:w-4 md:h-4 text-green-600 border-gray-300 focus:ring-green-600">
                        <span class="ml-2 text-xs md:text-sm text-gray-700">Green</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="color" value="#f59e0b" {{ old('color') == '#f59e0b' ? 'checked' : '' }}
                            class="w-4 h-4 md:w-4 md:h-4 text-yellow-600 border-gray-300 focus:ring-yellow-600">
                        <span class="ml-2 text-xs md:text-sm text-gray-700">Orange</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="color" value="#ef4444" {{ old('color') == '#ef4444' ? 'checked' : '' }}
                            class="w-4 h-4 md:w-4 md:h-4 text-red-600 border-gray-300 focus:ring-red-600">
                        <span class="ml-2 text-xs md:text-sm text-gray-700">Red</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="color" value="#8b5cf6" {{ old('color') == '#8b5cf6' ? 'checked' : '' }}
                            class="w-4 h-4 md:w-4 md:h-4 text-purple-600 border-gray-300 focus:ring-purple-600">
                        <span class="ml-2 text-xs md:text-sm text-gray-700">Purple</span>
                    </label>
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-2 md:gap-3">
                <button type="submit"
                    class="flex-1 bg-green-800 hover:bg-green-900 text-white text-xs md:text-sm font-medium py-2.5 rounded-lg transition min-h-[48px] flex items-center justify-center">
                    Add Event
                </button>
                <a href="{{ route('admin.calendar.index') }}"
                    class="px-4 md:px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs md:text-sm font-medium rounded-lg transition min-h-[48px] flex items-center justify-center whitespace-nowrap">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
