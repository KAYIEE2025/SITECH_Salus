@extends('layouts.app')
@section('title', 'Add Grade Submission Schedule')
@section('content')

    <div class="max-w-2xl">
        <div class="mb-4">
            <a href="{{ route('registrar.grade-submission-schedules.index', ['school_year' => $activeSchoolYear]) }}"
                class="text-sm text-gray-500 hover:text-gray-700">
                ← Back to Schedules
            </a>
        </div>

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3 mb-6">
                {{ session('error') }}
            </div>
        @endif

        <div class="ra-card p-6 sm:p-8">
            <h2 class="text-base font-semibold text-gray-800 mb-6">Add Grade Submission Schedule</h2>

            <form method="POST" action="{{ route('registrar.grade-submission-schedules.store') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        School Year <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="school_year" value="{{ old('school_year', $activeSchoolYear) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
                        placeholder="e.g., {{ $activeSchoolYear }}">
                    @error('school_year') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Grading Period <span class="text-red-500">*</span>
                    </label>
                    <select name="grading_period"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                        <option value="">Select grading period...</option>
                        <option value="1" {{ old('grading_period') == 1 ? 'selected' : '' }}>Term 1</option>
                        <option value="2" {{ old('grading_period') == 2 ? 'selected' : '' }}>Term 2</option>
                        <option value="3" {{ old('grading_period') == 3 ? 'selected' : '' }}>Term 3</option>
                    </select>
                    @error('grading_period') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Start Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                        @error('start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Start Time <span class="text-red-500">*</span>
                        </label>
                        <input type="time" name="start_time" value="{{ old('start_time') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                        @error('start_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Deadline Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="deadline_date" value="{{ old('deadline_date') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                        @error('deadline_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Deadline Time <span class="text-red-500">*</span>
                        </label>
                        <input type="time" name="deadline_time" value="{{ old('deadline_time') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                        @error('deadline_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit"
                        class="flex-1 bg-green-800 hover:bg-green-900 text-white text-sm font-semibold py-2.5 rounded-lg transition">
                        Save Schedule
                    </button>
                    <a href="{{ route('registrar.grade-submission-schedules.index') }}"
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold py-2.5 rounded-lg transition text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

@endsection
