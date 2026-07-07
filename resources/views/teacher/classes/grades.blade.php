@extends('layouts.app')

@section('title', 'Manage Grades')

@section('content')
    <div class="mb-6">
        <a href="{{ route('teacher.classes.index') }}" class="text-sm font-medium text-[#1a5c1a] hover:text-green-900">
            Back to My Classes
        </a>
    </div>

    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-sm text-gray-500">
                    {{ optional($classSchedule->section?->yearLevel)->name ?? 'No grade level' }} - {{ $classSchedule->section->name ?? 'No section' }}
                </p>
                <h2 class="mt-1 text-xl font-semibold text-gray-800">
                    {{ $classSchedule->subject->name ?? 'No subject assigned' }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $classSchedule->subject->code ?? 'No subject code' }}
                </p>
            </div>
            <div class="rounded-lg bg-gray-50 px-4 py-3 text-sm text-gray-600">
                <p>{{ $classSchedule->school_year }} - {{ $classSchedule->semester }} Semester</p>
                <p class="mt-1">{{ $students->count() }} enrolled student(s)</p>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white">
        <div class="border-b border-gray-100 px-6 py-4">
            <h3 class="text-base font-semibold text-gray-800">Class Roster</h3>
        </div>

        @if($students->isEmpty())
            <div class="px-6 py-10 text-center text-sm text-gray-400">
                No students enrolled in this subject.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-6 py-3 text-left">Student Number</th>
                            <th class="px-6 py-3 text-left">Name</th>
                            <th class="px-6 py-3 text-left">Gender</th>
                            <th class="px-6 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($students as $studyLoad)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 font-mono text-gray-700">
                                    {{ $studyLoad->student->student_number }}
                                </td>
                                <td class="px-6 py-3 font-medium text-gray-800">
                                    {{ $studyLoad->student->last_name }}, {{ $studyLoad->student->first_name }}
                                    {{ $studyLoad->student->middle_name ? $studyLoad->student->middle_name[0] . '.' : '' }}
                                </td>
                                <td class="px-6 py-3 text-gray-600">
                                    {{ $studyLoad->student->gender ?? 'Not specified' }}
                                </td>
                                <td class="px-6 py-3">
                                    <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700">
                                        Ready
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
