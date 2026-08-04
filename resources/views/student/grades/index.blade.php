@extends('layouts.app')
@section('title', 'My Grades')
@section('content')
    <div class="st-card p-6 sm:p-8">
        <div class="mb-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">My Grades</h2>
                <p class="text-sm text-gray-500 mt-1">View your officially approved grades</p>
            </div>
        </div>

        <!-- Grade Summary -->
        @if($gradeSummary['total_subjects'] > 0)
            <div class="mb-6 rounded-2xl border border-green-100 bg-green-50/60 p-4">
                <h3 class="text-sm font-medium text-gray-800 mb-3">Grade Summary</h3>
                <div class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2 md:grid-cols-4">
                    <div>
                        <p class="text-gray-500">Total Subjects</p>
                        <p class="font-medium text-gray-800">{{ $gradeSummary['total_subjects'] }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Average Grade</p>
                        <p class="font-medium text-gray-800">{{ $gradeSummary['average_grade'] }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Passed</p>
                        <p class="font-medium text-green-700">{{ $gradeSummary['passed_subjects'] }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Failed</p>
                        <p class="font-medium text-red-600">{{ $gradeSummary['failed_subjects'] }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Filters -->
        @if($schoolYears->isNotEmpty())
            <div class="mb-4 flex flex-wrap items-center gap-3">
                <form method="GET" action="{{ route('student.grades.index') }}" class="flex flex-wrap items-center gap-3">
                    <div>
                        <select name="school_year" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                            <option value="">School Year</option>
                            @foreach($schoolYears as $year)
                                <option value="{{ $year }}" {{ $filterSchoolYear === $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-green-700 text-white text-sm rounded hover:bg-green-800 transition">
                        Filter
                    </button>
                    @if($filterSchoolYear)
                        <a href="{{ route('student.grades.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50 transition">
                            Clear
                        </a>
                    @endif
                </form>
            </div>
        @endif

        @if($groupedGrades->isEmpty())
            <div class="text-center py-12">
                <p class="text-gray-500">No approved grades are available yet.</p>
            </div>
        @else
            @foreach($groupedGrades as $group)
                <div class="mb-8">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4 flex items-center gap-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                            Approved
                        </span>
                        <h3 class="text-base font-semibold text-green-800">
                            {{ $group['school_year'] }}
                        </h3>
                    </div>
                    
                    <div class="overflow-x-auto rounded-xl border border-green-100 shadow-sm">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="text-left py-4 px-5 font-semibold text-gray-700 whitespace-nowrap">Subject Code</th>
                                    <th class="text-left py-4 px-5 font-semibold text-gray-700 whitespace-nowrap">Subject Name</th>
                                    <th class="text-left py-4 px-5 font-semibold text-gray-700 whitespace-nowrap">Teacher</th>
                                    <th class="text-center py-4 px-5 font-semibold text-gray-700 whitespace-nowrap">Term 1</th>
                                    <th class="text-center py-4 px-5 font-semibold text-gray-700 whitespace-nowrap">Term 2</th>
                                    <th class="text-center py-4 px-5 font-semibold text-gray-700 whitespace-nowrap">Term 3</th>
                                    <th class="text-center py-4 px-5 font-semibold text-gray-700 whitespace-nowrap">Final Rating</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($group['grades'] as $grade)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                        <td class="py-4 px-5 text-gray-800 font-medium whitespace-nowrap">
                                            {{ $grade->classSchedule->subject->code ?? 'N/A' }}
                                        </td>
                                        <td class="py-4 px-5 text-gray-700 whitespace-nowrap">
                                            {{ $grade->classSchedule->subject->name ?? 'N/A' }}
                                        </td>
                                        <td class="py-4 px-5 text-gray-600 whitespace-nowrap">
                                            {{ $grade->classSchedule->teacher->name ?? 'N/A' }}
                                        </td>
                                        <td class="py-4 px-5 text-center text-gray-800 font-mono">
                                            {{ $grade->term_1 ? number_format($grade->term_1, 2) : '-' }}
                                        </td>
                                        <td class="py-4 px-5 text-center text-gray-800 font-mono">
                                            {{ $grade->term_2 ? number_format($grade->term_2, 2) : '-' }}
                                        </td>
                                        <td class="py-4 px-5 text-center text-gray-800 font-mono">
                                            {{ $grade->term_3 ? number_format($grade->term_3, 2) : '-' }}
                                        </td>
                                        <td class="py-4 px-5 text-center font-bold text-green-700 font-mono bg-green-50/50">
                                            {{ $grade->final_rating ? number_format($grade->final_rating, 2) : '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        @endif

        <!-- GWA Section -->
        @if(isset($gwa))
            <div class="mt-8 rounded-2xl border border-green-200 bg-gradient-to-r from-green-50 to-emerald-50 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-green-800">General Weighted Average</h3>
                        <p class="text-xs text-green-600 mt-1">Based on approved final ratings</p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-green-700">{{ $gwa }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
