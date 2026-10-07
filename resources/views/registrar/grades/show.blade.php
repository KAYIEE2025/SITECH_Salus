@extends('layouts.app')
@section('title', 'Student Grades')
@section('content')

    <div class="ra-card p-6 sm:p-8 mb-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-base font-semibold text-gray-800">Student Grades</h2>
            <a href="{{ route('registrar.grades.index') }}"
                class="text-sm text-gray-600 hover:text-gray-800 font-medium">
                ← Back to Search
            </a>
        </div>

        {{-- Student Information --}}
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 mb-8">
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Student Information</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Student Name:</span>
                        <span class="font-medium text-gray-800">{{ $student->last_name }}, {{ $student->first_name }} {{ $student->middle_name }}</span>
                    </div>
                    <!-- <div class="flex justify-between">
                        <span class="text-gray-500">Student Number:</span>
                        <span class="font-medium text-gray-800">{{ $student->student_number ?? '—' }}</span>
                    </div> -->
                    <!-- <div class="flex justify-between">
                        <span class="text-gray-500">QR Value:</span>
                        <span class="font-medium text-gray-800">{{ $student->qr_code_value ?? '—' }}</span>
                    </div> -->
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Enrollment Information</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Grade Level:</span>
                        <span class="font-medium text-gray-800">{{ $student->yearLevel->name ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Section:</span>
                        <span class="font-medium text-gray-800">{{ $student->section->name ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        @if($schoolYears->isNotEmpty())
            <div class="mb-4 flex flex-wrap items-center gap-3">
                <form method="GET" action="{{ route('registrar.grades.show', $student) }}" class="flex flex-wrap items-center gap-3">
                    <div>
                        <select name="school_year" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                            <option value="">All School Years</option>
                            @foreach($schoolYears as $year)
                                <option value="{{ $year }}" {{ $filterSchoolYear === $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- <div>
                        <select name="term" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                            <option value="">All Terms</option>
                            <option value="1" {{ $filterTerm === '1' ? 'selected' : '' }}>Term 1</option>
                            <option value="2" {{ $filterTerm === '2' ? 'selected' : '' }}>Term 2</option>
                            <option value="3" {{ $filterTerm === '3' ? 'selected' : '' }}>Term 3</option>
                        </select>
                    </div> -->
                    <button type="submit" class="px-4 py-2 bg-green-800 text-white text-sm rounded hover:bg-green-900 transition">
                        Filter
                    </button>
                    @if($filterSchoolYear || $filterTerm)
                        <a href="{{ route('registrar.grades.show', $student) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50 transition">
                            Clear
                        </a>
                    @endif
                </form>
            </div>
        @endif
    </div>

    {{-- Grades Display --}}
    @if($groupedGrades->isEmpty())
        <div class="ra-card p-6 sm:p-8">
            <div class="text-center py-12">
                <p class="text-gray-500">No approved grades found for this student.</p>
            </div>
        </div>
    @else
        @foreach($groupedGrades as $group)
            <div class="ra-card overflow-hidden mb-6">
                <div class="ra-card-header">
                    <h2 class="text-base font-semibold text-gray-800">
                        {{ $group['school_year'] }}
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">Approved Grades</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-xs">
                            <tr>
                                <th class="text-left px-4 py-3">Subject Code</th>
                                <th class="text-left px-4 py-3">Subject Name</th>
                                <th class="text-left px-4 py-3">Teacher</th>
                                <th class="text-center px-4 py-3">Term 1</th>
                                <th class="text-center px-4 py-3">Term 2</th>
                                <th class="text-center px-4 py-3">Term 3</th>
                                <th class="text-center px-4 py-3">Final Grade</th>
                                <th class="text-left px-4 py-3">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($group['grades'] as $grade)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-800 font-medium">
                                    {{ $grade->classSchedule->subject->code ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    {{ $grade->classSchedule->subject->name ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $grade->classSchedule->teacher->name ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-800 font-mono">
                                    {{ $grade->term_1 ? number_format($grade->term_1, 2) : '-' }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-800 font-mono">
                                    {{ $grade->term_2 ? number_format($grade->term_2, 2) : '-' }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-800 font-mono">
                                    {{ $grade->term_3 ? number_format($grade->term_3, 2) : '-' }}
                                </td>
                                <td class="px-4 py-3 text-center font-bold text-green-700 font-mono bg-green-50/50">
                                    {{ $grade->final_rating ? number_format($grade->final_rating, 2) : '-' }}
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $grade->remarks ?? '—' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    @endif

@endsection
