@extends('layouts.app')
@section('title', 'Class Schedules')
@section('content')

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg px-4 py-3 mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

        {{-- Create Schedule Form --}}
        <div class="ra-card p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-4">Add Class Schedule</h2>
            <form method="POST" action="{{ route('registrar.schedules.store') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                    <select name="subject_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                        <option value="">Select subject...</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->code }} — {{ $subject->name }}</option>
                        @endforeach
                    </select>
                    @error('subject_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teacher</label>
                    <select name="teacher_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                        <option value="">Select teacher...</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                    @error('teacher_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                    <select name="section_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                        <option value="">Select section...</option>
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}">
                                {{ $section->course->code ?? '' }} {{ $section->yearLevel->name ?? '' }} — Sec {{ $section->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('section_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Room</label>
                    <input type="text" name="room"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
                        placeholder="Room 301, Lab 2...">
                    @error('room') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
                    @error('days') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3 mb-4">
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

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">School Year</label>
                    <input type="text" name="school_year" value="2025-2026"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                </div>

                <button type="submit"
                    class="w-full bg-green-800 hover:bg-green-900 text-white text-sm font-semibold py-2.5 rounded-lg transition">
                    Add Schedule
                </button>
            </form>
        </div>

        {{-- Schedules Table --}}
        <div class="ra-card overflow-hidden xl:col-span-2">
            <div class="ra-card-header">
                <h2 class="text-base font-semibold text-gray-800">All Class Schedules</h2>
            </div>
            <div class="overflow-x-auto"><table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs">
                    <tr>
                        <th class="text-left px-6 py-3">Subject</th>
                        <th class="text-left px-6 py-3">Teacher</th>
                        <th class="text-left px-6 py-3">Section</th>
                        <th class="text-left px-6 py-3">Schedule</th>
                        <th class="text-left px-6 py-3">Room</th>
                        <th class="text-left px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($schedules as $schedule)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3">
                            <p class="font-medium text-gray-800">{{ $schedule->subject->code ?? '—' }}</p>
                            <p class="text-xs text-gray-500">{{ $schedule->subject->name ?? '' }}</p>
                        </td>
                        <td class="px-6 py-3 text-gray-600">{{ $schedule->teacher->name ?? '—' }}</td>
                        <td class="px-6 py-3 text-gray-600">
                            {{ $schedule->section->course->code ?? '' }}
                            {{ $schedule->section->yearLevel->name ?? '' }} —
                            Sec {{ $schedule->section->name ?? '' }}
                        </td>
                        <td class="px-6 py-3 text-gray-600">
                            {{ implode(', ', $schedule->days ?? []) }}<br>
                            <span class="text-xs">{{ $schedule->time_start }} – {{ $schedule->time_end }}</span>
                        </td>
                        <td class="px-6 py-3 text-gray-600">{{ $schedule->room }}</td>
                        <td class="px-6 py-3">
                            <form method="POST" action="{{ route('registrar.schedules.destroy', $schedule) }}"
                                onsubmit="return confirm('Delete this schedule?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="text-xs bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-lg transition">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-400">No class schedules yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table></div>
        </div>

    </div>

@endsection
