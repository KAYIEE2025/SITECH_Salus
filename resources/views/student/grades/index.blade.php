@extends('layouts.app')
@section('title', 'My Grades')
@section('content')
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-semibold text-gray-800">My Grades</h2>
            @if($groupedGrades->isNotEmpty())
                <button onclick="window.open('{{ route('student.grades.print') }}', '_blank')" class="px-4 py-2 bg-green-700 text-white text-sm rounded hover:bg-green-800 transition">
                    Print Grades
                </button>
            @endif
        </div>

        @if($groupedGrades->isEmpty())
            <div class="text-center py-12">
                <p class="text-gray-500">Your grades are not yet available. Please wait for the Registrar's approval.</p>
            </div>
        @else
            @foreach($groupedGrades as $group)
                <div class="mb-8">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                        <h3 class="text-base font-semibold text-green-800">
                            {{ $group['school_year'] }} — {{ $group['semester'] }} Semester
                        </h3>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-3 px-4 font-medium text-gray-600">Subject Code</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-600">Subject Name</th>
                                    <th class="text-center py-3 px-4 font-medium text-gray-600">Final Grade</th>
                                    <th class="text-center py-3 px-4 font-medium text-gray-600">Remarks</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-600">Date Approved</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($group['grades'] as $grade)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                                        <td class="py-3 px-4 text-gray-800 font-medium">
                                            {{ $grade->classSchedule->subject->code ?? 'N/A' }}
                                        </td>
                                        <td class="py-3 px-4 text-gray-700">
                                            {{ $grade->classSchedule->subject->name ?? 'N/A' }}
                                        </td>
                                        <td class="py-3 px-4 text-center font-bold text-gray-800">
                                            {{ $grade->final_grade }}
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="text-xs px-2 py-1 rounded-full
                                                {{ $grade->remarks == 'Passed' ? 'bg-green-50 text-green-700' : ($grade->remarks == 'Failed' ? 'bg-red-50 text-red-600' : 'bg-gray-50 text-gray-600') }}">
                                                {{ $grade->remarks }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-gray-600">
                                            {{ $grade->reviewed_at ? $grade->reviewed_at->format('M d, Y') : '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- GWA Display --}}
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">General Weighted Average (GWA):</span>
                            <span class="text-lg font-bold text-gray-800">
                                {{-- GWA computation will be added in a later phase --}}
                                GWA not yet available.
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endsection
