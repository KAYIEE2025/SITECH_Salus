@extends('layouts.app')
@section('title', 'Student List')
@section('content')
    <div class="mb-6">
        <a href="{{ route('teacher.classes.index') }}" class="text-green-700 hover:text-green-800 text-sm">
            ← Back to Classes
        </a>
    </div>

    <div class="tc-card p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-semibold text-gray-800">
                {{ $classSchedule->subject->name ?? 'Subject' }} - 
                {{ $classSchedule->section->name ?? 'Section' }}
            </h2>
            <span class="text-sm text-gray-500">
                {{ $classSchedule->school_year }}
            </span>
        </div>

        @if($students->isEmpty())
            <div class="text-center py-12">
                <p class="text-gray-500">No students enrolled in this class.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <!-- <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Student Number</th> -->
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Name</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Gender</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $studyLoad)
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <!-- <td class="py-3 px-4 text-sm text-gray-800">
                                    {{ $studyLoad->student->student_number }} -->
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-800">
                                    {{ $studyLoad->student->last_name }}, {{ $studyLoad->student->first_name }}
                                    {{ $studyLoad->student->middle_name ? $studyLoad->student->middle_name[0] . '.' : '' }}
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-800">
                                    {{ $studyLoad->student->gender }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
