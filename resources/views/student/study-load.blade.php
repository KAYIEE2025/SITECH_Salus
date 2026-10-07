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
                        <!-- <p class="text-xs md:text-sm text-gray-500">Student Number</p> -->
                        <!-- <p class="font-medium text-gray-800 text-sm md:text-base">{{ $student->student_number }}</p> -->
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

        <!-- Filters -->
        <div class="st-card p-4 md:p-6 mb-4 md:mb-6">
            <form method="GET" action="{{ route('student.study-load.index') }}" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">School Year</label>
                    <select name="school_year"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                        <option value="">All School Years</option>
                        @foreach($schoolYears as $schoolYear)
                            <option value="{{ $schoolYear }}" {{ request('school_year') == $schoolYear ? 'selected' : '' }}>
                                {{ $schoolYear }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">Term</label>
                    <select name="term"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                        <option value="">All Terms</option>
                        @foreach($terms as $term)
                            <option value="{{ $term }}" {{ request('term') == $term ? 'selected' : '' }}>
                                {{ $term }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="bg-green-800 hover:bg-green-900 text-white text-xs md:text-sm font-medium px-4 py-2 rounded-lg transition">
                        Filter
                    </button>
                    <a href="{{ route('student.study-load.index') }}"
                        class="border border-gray-300 text-gray-700 text-xs md:text-sm font-medium px-4 py-2 rounded-lg hover:bg-gray-50 transition">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Study Load Table -->
        <div class="st-card overflow-hidden p-4 md:p-6">
            <h2 class="text-base md:text-lg font-semibold text-gray-800 mb-3 md:mb-4">Class Schedule</h2>

            @if($schedules->isEmpty())
                <div class="text-center py-8 md:py-12">
                    <p class="text-gray-500 text-xs md:text-sm">No study load records found for the selected filters.</p>
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
                                <th class="text-left py-2 px-2 md:py-3 md:px-4 font-medium text-gray-600 text-[10px] md:text-xs">Term</th>
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
                                    <td class="py-2 px-2 md:py-3 md:px-4 text-gray-700">{{ $schedule->term ?? '—' }}</td>
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
