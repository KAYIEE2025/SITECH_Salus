@extends('layouts.app')

@section('title', 'My Advisory Students')

@section('content')
    <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">My Advisory Students</h2>
            <p class="mt-1 text-sm text-gray-500">Students enrolled in the section(s) assigned to you as adviser.</p>
        </div>
    </div>

    @if($advisorySections->isEmpty())
        <div class="tc-card px-6 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <p class="mt-4 text-sm text-gray-500">You are not currently assigned as an adviser to any section.</p>
        </div>
    @else
        <div class="space-y-6">
            @foreach($advisorySections as $section)
                <div class="tc-card overflow-hidden">
                    <div class="border-b border-gray-100 bg-gray-50 px-6 py-4">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 class="text-base font-semibold text-gray-800">
                                    {{ $section->yearLevel->name ?? 'No grade level' }} - Section {{ $section->name }}
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Adviser: {{ auth()->user()->name }}
                                </p>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="text-sm">
                                    <span class="text-gray-500">Students:</span>
                                    <span class="ml-1 font-semibold text-gray-800">{{ $section->students->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($section->students->isEmpty())
                        <div class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <p class="mt-4 text-sm text-gray-500">No students are currently enrolled in your advisory section.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 text-gray-500 text-xs">
                                    <tr>
                                        <th class="px-6 py-3 text-left">Student Number</th>
                                        <th class="px-6 py-3 text-left">Student Name</th>
                                        <th class="px-6 py-3 text-left">Grade Level</th>
                                        <th class="px-6 py-3 text-left">Section</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($section->students->sortBy('last_name') as $student)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-3 text-gray-600">{{ $student->student_number ?? '—' }}</td>
                                            <td class="px-6 py-3 font-medium text-gray-800">
                                                {{ $student->last_name }}, {{ $student->first_name }} {{ $student->middle_name }}
                                            </td>
                                            <td class="px-6 py-3 text-gray-600">{{ $student->yearLevel->name ?? '—' }}</td>
                                            <td class="px-6 py-3 text-gray-600">Section {{ $section->name ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
@endsection
