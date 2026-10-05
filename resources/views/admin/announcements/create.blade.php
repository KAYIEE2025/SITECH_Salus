@extends('layouts.app')
@section('title', 'Create Announcement')
@section('sidebar-links')
    <x-nav-link href="{{ route('admin.dashboard') }}" :active="false">Dashboard</x-nav-link>
    <x-nav-link href="{{ route('admin.announcements.index') }}" :active="true">Announcements</x-nav-link>
    <x-nav-link href="{{ route('admin.calendar.index') }}" :active="false">School Calendar</x-nav-link>
    <x-nav-link href="{{ route('admin.reports.index') }}" :active="false">Reports</x-nav-link>
    <x-nav-link href="{{ route('admin.teacher-study-load') }}" :active="false">Teacher Study Load</x-nav-link>
    <x-nav-link href="{{ route('admin.activity-logs.index') }}" :active="false">Activity Logs</x-nav-link>
    <x-nav-link href="{{ route('admin.profile.index') }}" :active="false">My Profile</x-nav-link>
@endsection
@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Create Announcement</h1>
        <a href="{{ route('admin.announcements.index') }}"
            class="text-sm text-gray-600 hover:text-gray-800">
            ← Back to Announcements
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ route('admin.announcements.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Title <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
                    placeholder="Announcement title">
                @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Body <span class="text-red-500">*</span>
                </label>
                <textarea name="body" rows="5"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
                    placeholder="Announcement content">{{ old('body') }}</textarea>
                @error('body') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Target Audience <span class="text-red-500">*</span>
                </label>
                <select name="target_type" id="target_type"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
                    onchange="toggleTargetFields()">
                    <option value="all" {{ old('target_type') == 'all' ? 'selected' : '' }}>All Users</option>
                    <option value="grade_level" {{ old('target_type') == 'grade_level' ? 'selected' : '' }}>Specific Grade Level</option>
                    <option value="section" {{ old('target_type') == 'section' ? 'selected' : '' }}>Specific Section</option>
                </select>
                @error('target_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4" id="grade_level_field" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Grade Level
                </label>
                <select name="target_id"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    <option value="">Select grade level...</option>
                    @foreach($yearLevels as $yearLevel)
                        <option value="{{ $yearLevel->id }}" {{ old('target_id') == $yearLevel->id ? 'selected' : '' }}>
                            {{ $yearLevel->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4" id="section_field" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Section
                </label>
                <select name="target_id"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    <option value="">Select section...</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}" {{ old('target_id') == $section->id ? 'selected' : '' }}>
                            {{ $section->yearLevel->name ?? '' }} — Section {{ $section->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" checked
                        class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-600">
                    <span class="ml-2 text-sm text-gray-700">Active</span>
                </label>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="flex-1 bg-green-800 hover:bg-green-900 text-white text-sm font-medium py-2.5 rounded-lg transition">
                    Create Announcement
                </button>
                <a href="{{ route('admin.announcements.index') }}"
                    class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <script>
        function toggleTargetFields() {
            const targetType = document.getElementById('target_type').value;
            const gradeLevelField = document.getElementById('grade_level_field');
            const sectionField = document.getElementById('section_field');

            gradeLevelField.style.display = targetType === 'grade_level' ? 'block' : 'none';
            sectionField.style.display = targetType === 'section' ? 'block' : 'none';
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleTargetFields();
        });
    </script>
@endsection
