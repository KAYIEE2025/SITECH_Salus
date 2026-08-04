@extends('layouts.app')

@section('title', 'Import Summary Preview')

@section('content')
    <div class="mb-6">
        <a href="{{ route('teacher.classes.grades', $classSchedule) }}" class="text-sm font-medium text-[#1a5c1a] hover:text-green-900">
            Back to Manage Grades
        </a>
    </div>

    <!-- Class Information Card -->
    <div class="tc-card mb-6 p-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3 flex-1">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">School Year</p>
                    <p class="mt-1 text-sm font-medium text-gray-800">{{ $classSchedule->school_year ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Section</p>
                    <p class="mt-1 text-sm font-medium text-gray-800">{{ $classSchedule->section->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Subject</p>
                    <p class="mt-1 text-sm font-medium text-gray-800">{{ $classSchedule->subject->name ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Summary Card -->
    <div class="tc-card mb-6 p-6">
        <div class="mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Import Summary Preview</h2>
            <p class="mt-1 text-sm text-gray-500">Review the parsed data before importing grades for Quarter {{ $gradingPeriod }}.</p>
        </div>

        <!-- Summary Statistics -->
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg bg-blue-50 border border-blue-200 p-4">
                <p class="text-xs text-blue-600 uppercase tracking-wide">Total Parsed</p>
                <p class="mt-2 text-2xl font-bold text-blue-700">{{ $totalParsed }}</p>
            </div>
            <div class="rounded-lg bg-green-50 border border-green-200 p-4">
                <p class="text-xs text-green-600 uppercase tracking-wide">Matched</p>
                <p class="mt-2 text-2xl font-bold text-green-700">{{ $matchedCount }}</p>
            </div>
            <div class="rounded-lg bg-red-50 border border-red-200 p-4">
                <p class="text-xs text-red-600 uppercase tracking-wide">Unmatched</p>
                <p class="mt-2 text-2xl font-bold text-red-700">{{ $unmatchedCount }}</p>
            </div>
            <div class="rounded-lg bg-yellow-50 border border-yellow-200 p-4">
                <p class="text-xs text-yellow-600 uppercase tracking-wide">Ambiguous</p>
                <p class="mt-2 text-2xl font-bold text-yellow-700">{{ $ambiguousCount ?? 0 }}</p>
            </div>
        </div>

        <!-- Data Table -->
        <div class="overflow-x-auto border border-gray-200 rounded-lg">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Student Name (Excel)</th>
                        <th class="px-4 py-3 text-left">Matched Student</th>
                        <th class="px-4 py-3 text-left">Quarter {{ $gradingPeriod }} Grade</th>
                        <th class="px-4 py-3 text-left">Remarks</th>
                        <th class="px-4 py-3 text-left">Match Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($displayData as $row)
                        <tr class="{{ $row['match_status'] === 'unmatched' ? 'bg-red-50' : ($row['match_status'] === 'ambiguous' ? 'bg-yellow-50' : 'hover:bg-gray-50') }}">
                            <td class="px-4 py-3 font-medium text-gray-800">
                                {{ $row['student_name'] }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                @if($row['match_status'] === 'ambiguous')
                                    <div class="text-xs">
                                        <p class="font-medium text-yellow-700">Multiple matches:</p>
                                        @foreach($row['ambiguous_matches'] as $match)
                                            <p class="text-gray-600">{{ $match['student_name'] }}</p>
                                        @endforeach
                                    </div>
                                @else
                                    {{ $row['matched_student'] ?? 'Not matched' }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-700">
                                {{ $row['quarter_grade'] ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $row['remarks'] ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($row['match_status'] === 'matched')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                        Matched
                                    </span>
                                @elseif($row['match_status'] === 'ambiguous')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                        Ambiguous
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                        Unmatched
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Action Buttons -->
        <div class="mt-6 flex justify-end gap-3">
            <form action="{{ route('teacher.classes.cancel-import-summary', $classSchedule) }}" method="POST">
                @csrf
                <button type="submit" class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </button>
            </form>
            <form action="{{ route('teacher.classes.confirm-import-summary', $classSchedule) }}" method="POST">
                @csrf
                <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-green-700 rounded-lg hover:bg-green-800 transition">
                    Import Grades
                </button>
            </form>
        </div>
    </div>
@endsection
