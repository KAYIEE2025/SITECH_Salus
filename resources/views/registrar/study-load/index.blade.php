@extends('layouts.app')
@section('title', 'Study Load Management')
@section('content')

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg px-4 py-3 mb-6">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3 mb-6">
            {{ session('error') }}
        </div>
    @endif

    {{-- Step 1: Select Section --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
        <h2 class="text-base font-semibold text-gray-800 mb-4">Select Section</h2>
        <form method="GET" action="{{ route('registrar.study-load') }}" class="flex gap-3 flex-wrap">
            <select name="section_id"
            class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
            <option value="">Select a section...</option>
            @foreach($sections as $section)
                <option value="{{ $section->id }}"
                    {{ optional($selected)->id == $section->id ? 'selected' : '' }}>
                    {{ $section->yearLevel->name ?? '' }} — Section {{ $section->name }}
                </option>
            @endforeach
        </select>
            <select name="semester"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                <option value="1st" {{ request('semester','1st') == '1st' ? 'selected' : '' }}>1st Semester</option>
                <option value="2nd" {{ request('semester') == '2nd' ? 'selected' : '' }}>2nd Semester</option>
                <option value="Summer" {{ request('semester') == 'Summer' ? 'selected' : '' }}>Summer</option>
            </select>
            <input type="text" name="school_year" value="{{ request('school_year','2025-2026') }}"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
                placeholder="School Year">
            <button type="submit"
                class="bg-green-800 hover:bg-green-900 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                View Study Load
            </button>
        </form>
    </div>

    @if($selected)
    <div class="grid grid-cols-3 gap-6">

        {{-- Add Subject to Study Load --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-base font-semibold text-gray-800">
                Study Load — {{ $selected->yearLevel->name ?? '' }} Section {{ $selected->name }}
            </h2>

            <form method="POST" action="{{ route('registrar.study-load.store') }}">
                @csrf
                <input type="hidden" name="section_id" value="{{ $selected->id }}">
                <input type="hidden" name="semester" value="{{ request('semester', '1st') }}">
                <input type="hidden" name="school_year" value="{{ request('school_year', '2025-2026') }}">

               <div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Subject Code <span class="text-red-500">*</span>
    </label>
    <input type="text" name="subject_code"
        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
        placeholder="e.g. IT 401, MATH 101...">
    <p class="text-xs text-gray-400 mt-1">Short code for the subject.</p>
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Subject Name <span class="text-red-500">*</span>
    </label>
    <input type="text" name="subject_name"
        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
        placeholder="e.g. Web Systems & Technology...">
</div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teacher</label>
                    <select name="teacher_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                        <option value="">Select teacher...</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Room</label>
                    <input type="text" name="room" placeholder="Room 301, Lab 2..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Days</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['Mon','Tue','Wed','Thu','Fri','Sat'] as $day)
                            <label class="flex items-center gap-1 text-sm text-gray-700">
                                <input type="checkbox" name="days[]" value="{{ $day }}"
                                    class="rounded border-gray-300 text-green-600">
                                {{ $day }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Time Start</label>
                        <input type="time" name="time_start"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Time End</label>
                        <input type="time" name="time_end"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-green-800 hover:bg-green-900 text-white text-sm font-semibold py-2.5 rounded-lg transition">
                    Add to Study Load
                </button>
            </form>
        </div>

        {{-- Study Load Table (sorted by time) --}}
        <div class="col-span-2 bg-white rounded-xl border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-800">
                    Study Load — {{ $selected->course->code ?? '' }} {{ $selected->yearLevel->name ?? '' }} Section {{ $selected->name }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">{{ request('school_year','2025-2026') }} · {{ request('semester','1st') }} Semester · Sorted by time</p>
            </div>

            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs">
                    <tr>
                        <th class="text-left px-6 py-3">Subject</th>
                        <th class="text-left px-6 py-3">Teacher</th>
                        <th class="text-left px-6 py-3">Days</th>
                        <th class="text-left px-6 py-3">Time</th>
                        <th class="text-left px-6 py-3">Room</th>
                        <th class="text-left px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($schedules as $schedule)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3">
                            <p class="font-medium text-gray-800">{{ $schedule->subject->code ?? '—' }}</p>
                            <p class="text-xs text-gray-400">{{ $schedule->subject->name ?? '' }}</p>
                        </td>
                        <td class="px-6 py-3 text-gray-600">{{ $schedule->teacher->name ?? '—' }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ implode(', ', $schedule->days ?? []) }}</td>
                        <td class="px-6 py-3 text-gray-600 text-xs">
                            {{ \Carbon\Carbon::parse($schedule->time_start)->format('h:i A') }} –
                            {{ \Carbon\Carbon::parse($schedule->time_end)->format('h:i A') }}
                        </td>
                        <td class="px-6 py-3 text-gray-600">{{ $schedule->room }}</td>
                        <td class="px-6 py-3">
                            <form method="POST" action="{{ route('registrar.study-load.destroy', $schedule) }}"
                                onsubmit="return confirm('Remove this subject from study load?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="text-xs bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-lg transition">
                                    Remove
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                            No subjects in this study load yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
    @endif

@endsection
