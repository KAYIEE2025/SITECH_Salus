@extends('layouts.app')

@section('title', 'Import Preview')

@section('content')
    <div class="mb-6">
        <a href="{{ route('teacher.classes.grades', $classSchedule) }}" class="text-sm font-medium text-[#1a5c1a] hover:text-green-900">
            Back to Manage Grades
        </a>
    </div>

    @php
        $importedClassInfo = session('imported_class_info_' . $classSchedule->id);
        $importedStudentRecords = session('imported_student_records_' . $classSchedule->id);
        $validationSummary = session('imported_validation_summary_' . $classSchedule->id);
    @endphp

    <div class="tc-card mb-6 p-6">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-800">Import Preview</h2>
            <p class="mt-2 text-sm text-gray-600">
                Review the extracted data from the uploaded SALUS grading sheet before proceeding.
            </p>
        </div>

        <!-- Import Status -->
        @if($validationSummary)
            <div class="rounded-lg bg-green-50 border border-green-200 p-4 mb-6">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <p class="text-sm font-medium text-green-800">
                        Official SALUS grading sheet successfully validated. {{ $validationSummary['total_students'] }} students detected.
                    </p>
                </div>
            </div>
        @endif

        <!-- Class Information from Excel -->
        @if($importedClassInfo)
            <div class="rounded-lg bg-gray-50 border border-gray-200 p-4 mb-6">
                <h3 class="text-sm font-medium text-gray-800 mb-3">Class Information (from Excel)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">School Year:</p>
                        <p class="font-medium text-gray-800">{{ $importedClassInfo['school_year'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Grade Level:</p>
                        <p class="font-medium text-gray-800">{{ $importedClassInfo['grade_level'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Section:</p>
                        <p class="font-medium text-gray-800">{{ $importedClassInfo['section'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Subject:</p>
                        <p class="font-medium text-gray-800">{{ $importedClassInfo['subject'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Subject Code:</p>
                        <p class="font-medium text-gray-800">{{ $importedClassInfo['subject_code'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Teacher Name:</p>
                        <p class="font-medium text-gray-800">{{ $importedClassInfo['teacher_name'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Total Students Imported:</p>
                        <p class="font-medium text-gray-800">{{ $validationSummary['total_students'] ?? count($importedStudentRecords) }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Student Records Table -->
        @if($importedStudentRecords && count($importedStudentRecords) > 0)
            <div class="mb-6">
                <h3 class="text-sm font-medium text-gray-800 mb-3">Student Records</h3>
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left py-3 px-4 font-medium text-gray-600">Student Number</th>
                                <th class="text-left py-3 px-4 font-medium text-gray-600">Student Name (Excel)</th>
                                <th class="text-left py-3 px-4 font-medium text-gray-600">Matched Name (DB)</th>
                                <th class="text-center py-3 px-4 font-medium text-gray-600">Initial Grade</th>
                                <th class="text-center py-3 px-4 font-medium text-gray-600">Quarterly Grade</th>
                                <th class="text-center py-3 px-4 font-medium text-gray-600">Descriptor</th>
                                <th class="text-center py-3 px-4 font-medium text-gray-600">Validation Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($importedStudentRecords as $record)
                                <tr class="border-b border-gray-100 {{ ($record['validation_status'] ?? 'valid') === 'invalid' ? 'bg-red-50' : 'hover:bg-gray-50' }}">
                                    <td class="py-3 px-4 text-gray-800">{{ $record['student_number'] ?? '-' }}</td>
                                    <td class="py-3 px-4 font-medium text-gray-800">{{ $record['student_name'] ?? '-' }}</td>
                                    <td class="py-3 px-4 text-gray-600 text-xs">{{ $record['matched_name'] ?? '-' }}</td>
                                    <td class="py-3 px-4 text-center text-gray-700">{{ $record['initial_grade'] ?? '-' }}</td>
                                    <td class="py-3 px-4 text-center font-semibold text-gray-800">{{ $record['quarterly_grade'] ?? '-' }}</td>
                                    <td class="py-3 px-4 text-center">
                                        @if(!empty($record['remarks']))
                                            <span class="text-xs px-2 py-1 rounded-full
                                                {{ $record['remarks'] == 'Passed' ? 'bg-green-50 text-green-700' : ($record['remarks'] == 'Failed' ? 'bg-red-50 text-red-600' : 'bg-gray-50 text-gray-600') }}">
                                                {{ $record['remarks'] }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if(($record['validation_status'] ?? 'valid') === 'valid')
                                            <span class="text-xs px-2 py-1 rounded-full bg-green-50 text-green-700">✓ Valid</span>
                                        @else
                                            <span class="text-xs px-2 py-1 rounded-full bg-red-50 text-red-600">
                                                ⚠ {{ implode(', ', $record['validation_messages'] ?? ['Invalid']) }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="rounded-lg bg-yellow-50 border border-yellow-200 p-4 mb-6">
                <p class="text-sm text-yellow-800">No student records found. Please upload a valid grading sheet.</p>
            </div>
        @endif

        <!-- Summary Section -->
        @if($validationSummary)
            <div class="rounded-lg bg-gray-50 border border-gray-200 p-4 mb-6">
                <h3 class="text-sm font-medium text-gray-800 mb-3">Import Summary</h3>
                <div class="grid grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Total Students:</p>
                        <p class="font-medium text-gray-800">{{ $validationSummary['total_students'] }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Valid:</p>
                        <p class="font-medium text-green-700">{{ $validationSummary['valid_count'] }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Invalid:</p>
                        <p class="font-medium text-red-600">{{ $validationSummary['invalid_count'] }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex justify-between">
            <div class="flex gap-3">
                <a href="{{ route('teacher.classes.grades', $classSchedule) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Back
                </a>
                <a href="{{ route('teacher.classes.grades', $classSchedule) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Import Another File
                </a>
            </div>
            <div class="ml-auto">
                <form action="{{ route('teacher.classes.save-grades', $classSchedule) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="px-6 py-2 bg-green-700 text-white text-sm font-medium rounded-lg hover:bg-green-800 transition disabled:bg-gray-300 disabled:cursor-not-allowed"
                            {{ $validationSummary['has_invalid_records'] ?? false ? 'disabled' : '' }}>
                        Save Grades
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Phase 3 Status -->
    <div class="rounded-lg bg-blue-50 border border-blue-200 p-4">
        <h3 class="text-sm font-medium text-blue-800 mb-2">Phase 3 Status</h3>
        <ul class="text-sm text-blue-700 space-y-1">
            <li>✓ Excel file validation implemented</li>
            <li>✓ Excel reading and parsing implemented</li>
            <li>✓ Class information extraction implemented</li>
            <li>✓ Student records extraction implemented</li>
            <li>✓ Data validation implemented</li>
            <li>✓ Validation status per student</li>
            <li>✓ Row highlighting for invalid records</li>
            <li>✓ Import summary (total, valid, invalid)</li>
            <li>✓ Continue button disabled if invalid records exist</li>
            <li>○ Database import (Phase 4)</li>
            <li>○ Grade submission (Phase 4)</li>
        </ul>
    </div>
@endsection
