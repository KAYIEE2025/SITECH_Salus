@extends('layouts.app')

@section('title', 'SSG Fine Records')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Fine Records</h1>
        <p class="mt-1 text-sm text-gray-500">Computed from SSG event attendance records.</p>
    </div>

    <div class="sg-card mb-6 p-6">
        <form method="GET" action="{{ route('ssg.fines.index') }}" class="grid gap-4 lg:grid-cols-5 lg:items-end" x-data="{ loading: false }">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">School Year</label>
                @if($activeSchoolYear)
                    <select name="school_year" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                        <option value="">All school years</option>
                        @foreach($schoolYears as $schoolYear)
                            <option value="{{ $schoolYear }}" @selected(($filters['school_year'] ?? $activeSchoolYear) === $schoolYear)>{{ $schoolYear }}</option>
                        @endforeach
                    </select>
                @else
                    <select name="school_year" class="w-full rounded-lg border border-red-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
                        <option value="">All school years</option>
                        @foreach($schoolYears as $schoolYear)
                            <option value="{{ $schoolYear }}" @selected(($filters['school_year'] ?? '') === $schoolYear)>{{ $schoolYear }}</option>
                        @endforeach
                    </select>
                    <p class="text-red-500 text-xs mt-1">No active school year set. Please contact Super Admin to set an active school year.</p>
                @endif
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Grade Level</label>
                <select name="year_level_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    <option value="">All grade levels</option>
                    @foreach($yearLevels as $yearLevel)
                        <option value="{{ $yearLevel->id }}" @selected((string) ($filters['year_level_id'] ?? '') === (string) $yearLevel->id)>{{ $yearLevel->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Section</label>
                <select name="section_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    <option value="">All sections</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}" @selected((string) ($filters['section_id'] ?? '') === (string) $section->id)>
                            {{ $section->yearLevel->name ?? 'Grade' }} - Section {{ $section->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Search Student</label>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
                    placeholder="Name or student number"
                    x-model.debounce.500ms="search"
                    @input="$el.closest('form').submit()">
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 rounded-lg bg-green-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-green-900">
                    Apply
                </button>
                <a href="{{ route('ssg.fines.index') }}" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-200">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="sg-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <!-- <th class="px-6 py-3 text-left">Student Number</th> -->
                        <th class="px-6 py-3 text-left">Student Name</th>
                        <th class="px-6 py-3 text-left">Grade Level</th>
                        <th class="px-6 py-3 text-left">Section</th>
                        <th class="px-6 py-3 text-left">Events Attended</th>
                        <th class="px-6 py-3 text-left">Events Absent</th>
                        <th class="px-6 py-3 text-left">Total Fine Balance</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($students as $student)
                        <tr class="hover:bg-gray-50">
                            <!-- <td class="px-6 py-4 font-medium text-gray-800">{{ $student->student_number }}</td> -->
                            <td class="px-6 py-4 text-gray-700">{{ $student->full_name }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $student->yearLevel->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $student->section ? 'Section ' . $student->section->name : 'N/A' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $student->events_attended_count }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $student->events_absent_count }}</td>
                            <td class="px-6 py-4 font-semibold text-gray-800">PHP {{ number_format($student->total_fine_balance ?? 0, 2) }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('ssg.fines.show', $student) }}"
                                    class="rounded-lg bg-green-50 px-3 py-1.5 text-xs font-medium text-green-700 transition hover:bg-green-100">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-400">No students found for the selected filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
            <div class="border-t border-gray-100 px-6 py-4">{{ $students->links() }}</div>
        @endif
    </div>
@endsection
