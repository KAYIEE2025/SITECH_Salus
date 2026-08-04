@extends('layouts.app')
@section('title', 'My Study Load')
@section('content')
    @if(!$student)
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 md:p-8 text-center">
            <h2 class="text-lg md:text-xl font-semibold text-yellow-800 mb-2">Student Profile Not Found</h2>
            <p class="text-yellow-700 text-sm md:text-base">Your student profile has not yet been created. Please contact the Registrar.</p>
        </div>
    @else
        <!-- Student Information Header -->
        <div class="st-card mb-4 md:mb-6 p-4 md:p-6 sm:p-8">
            <div class="flex items-center gap-3 md:gap-4 mb-3 md:mb-4">
                @if(file_exists(public_path('images/salus-logo.png')))
                    <img src="{{ asset('images/salus-logo.png') }}" alt="School Logo" class="h-12 w-12 md:h-16 md:w-16 object-contain">
                @endif
                <div>
                    <h1 class="text-lg md:text-xl font-bold text-gray-800">SALUS INSTITUTE OF TECHNOLOGY</h1>
                    <p class="text-xs md:text-sm text-gray-500">Student Information System</p>
                </div>
            </div>
            <div class="border-t border-gray-100 pt-3 md:pt-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-4">
                    <div>
                        <p class="text-xs md:text-sm text-gray-500">Student Name</p>
                        <p class="font-medium text-gray-800 text-sm md:text-base">{{ $student->last_name }}, {{ $student->first_name }} {{ $student->middle_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs md:text-sm text-gray-500">Student Number</p>
                        <p class="font-medium text-gray-800 text-sm md:text-base">{{ $student->student_number }}</p>
                    </div>
                    <div>
                        <p class="text-xs md:text-sm text-gray-500">Grade Level & Section</p>
                        <p class="font-medium text-gray-800 text-sm md:text-base">{{ $student->yearLevel->name ?? 'N/A' }} - {{ $student->section->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs md:text-sm text-gray-500">School Year</p>
                        <p class="font-medium text-gray-800 text-sm md:text-base">{{ $student->school_year }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Study Load Table -->
        <div class="st-card overflow-hidden p-4 md:p-6">
            <h2 class="text-base md:text-lg font-semibold text-gray-800 mb-3 md:mb-4">Class Schedule</h2>

            @if($schedules->isEmpty())
                <div class="text-center py-8 md:py-12">
                    <p class="text-gray-500 text-xs md:text-sm">No study load has been assigned yet.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-max text-xs md:text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-2 px-2 md:py-3 md:px-4 font-medium text-gray-600 text-[10px] md:text-xs">Subject Code</th>
                                <th class="text-left py-2 px-2 md:py-3 md:px-4 font-medium text-gray-600 text-[10px] md:text-xs">Subject Name</th>
                                <th class="text-left py-2 px-2 md:py-3 md:px-4 font-medium text-gray-600 text-[10px] md:text-xs">Teacher</th>
                                <th class="text-left py-2 px-2 md:py-3 md:px-4 font-medium text-gray-600 text-[10px] md:text-xs">Days</th>
                                <th class="text-left py-2 px-2 md:py-3 md:px-4 font-medium text-gray-600 text-[10px] md:text-xs">Time</th>
                                <th class="text-left py-2 px-2 md:py-3 md:px-4 font-medium text-gray-600 text-[10px] md:text-xs">Room</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($schedules as $schedule)
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-2 px-2 md:py-3 md:px-4 text-gray-800 font-medium">{{ $schedule->subject->code ?? 'N/A' }}</td>
                                    <td class="py-2 px-2 md:py-3 md:px-4 text-gray-700">{{ $schedule->subject->name ?? 'N/A' }}</td>
                                    <td class="py-2 px-2 md:py-3 md:px-4 text-gray-700">{{ $schedule->teacher->name ?? 'N/A' }}</td>
                                    <td class="py-2 px-2 md:py-3 md:px-4 text-gray-700">{{ implode(', ', $schedule->days ?? []) }}</td>
                                    <td class="py-2 px-2 md:py-3 md:px-4 text-gray-700">
                                        {{ \Carbon\Carbon::parse($schedule->time_start)->format('h:i A') }} –
                                        {{ \Carbon\Carbon::parse($schedule->time_end)->format('h:i A') }}
                                    </td>
                                    <td class="py-2 px-2 md:py-3 md:px-4 text-gray-700">{{ $schedule->room ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 md:mt-4 text-xs md:text-sm text-gray-500">
                    Total Subjects: {{ $schedules->count() }}
                </div>
            @endif
        </div>
    @endif
@endsection
