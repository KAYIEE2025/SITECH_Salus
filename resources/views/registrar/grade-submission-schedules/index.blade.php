@extends('layouts.app')
@section('title', 'Grade Submission Schedules')
@section('content')

    @session('success')
        <div class="bg-green-50 border border-green-200 text-green-800 text-xs md:text-sm rounded-lg px-3 py-2 md:px-4 md:py-3 mb-4 md:mb-6">
            {{ $value }}
        </div>
    @endsession
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-xs md:text-sm rounded-lg px-3 py-2 md:px-4 md:py-3 mb-4 md:mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="ra-card overflow-hidden">
        <div class="ra-card-header flex flex-col items-start justify-between gap-3 md:gap-4 sm:flex-row sm:items-center">
            <div class="flex flex-col gap-3 md:gap-4">
                <div>
                    <h2 class="text-base font-semibold text-gray-800">Grade Submission Schedules</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Total: {{ $schedules->count() }} schedule(s)</p>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <label class="text-xs md:text-sm font-medium text-gray-700">School Year:</label>
                    <select id="schoolYearFilter" onchange="filterBySchoolYear()"
                        class="border border-gray-300 rounded-lg px-2 py-1 md:px-3 md:py-1.5 text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                        @foreach($availableSchoolYears as $schoolYear)
                            <option value="{{ $schoolYear }}" {{ $schoolYear === $selectedSchoolYear ? 'selected' : '' }}>
                                {{ $schoolYear }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <a href="{{ route('registrar.grade-submission-schedules.create', ['school_year' => $selectedSchoolYear]) }}"
                class="bg-green-800 hover:bg-green-900 text-white text-xs md:text-sm font-semibold px-3 py-1.5 md:px-4 md:py-2 rounded-lg transition whitespace-nowrap">
                Add Schedule
            </a>
        </div>
        <div class="overflow-x-auto"><table class="w-full min-w-max text-xs md:text-sm">
            <thead class="bg-gray-50 text-gray-500 text-[10px] md:text-xs">
                <tr>
                    <th class="text-left px-2 py-2 md:px-6 md:py-3">School Year</th>
                    <th class="text-left px-2 py-2 md:px-6 md:py-3">Grading Period</th>
                    <th class="text-left px-2 py-2 md:px-6 md:py-3">Start</th>
                    <th class="text-left px-2 py-2 md:px-6 md:py-3">Deadline</th>
                    <th class="text-left px-2 py-2 md:px-6 md:py-3">Status</th>
                    <th class="text-left px-2 py-2 md:px-6 md:py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($schedules as $schedule)
                <tr class="hover:bg-gray-50">
                    <td class="px-2 py-2 md:px-6 md:py-3 text-gray-600 font-medium">{{ $schedule->school_year }}</td>
                    <td class="px-2 py-2 md:px-6 md:py-3">
                        <span class="bg-blue-100 text-blue-800 text-[10px] md:text-xs font-semibold px-2 py-0.5 md:px-2.5 md:py-1 rounded-full">
                            Term {{ $schedule->grading_period }}
                        </span>
                    </td>
                    <td class="px-2 py-2 md:px-6 md:py-3 text-gray-500">
                        {{ $schedule->start_at->format('M d, Y g:i A') }}
                    </td>
                    <td class="px-2 py-2 md:px-6 md:py-3 text-gray-500">
                        {{ $schedule->end_at->format('M d, Y g:i A') }}
                    </td>
                    <td class="px-2 py-2 md:px-6 md:py-3">
                        @if($schedule->status === 'Scheduled')
                            <span class="bg-yellow-100 text-yellow-800 text-[10px] md:text-xs font-semibold px-2 py-0.5 md:px-2.5 md:py-1 rounded-full">
                                Scheduled
                            </span>
                        @elseif($schedule->status === 'Open')
                            <span class="bg-green-100 text-green-800 text-[10px] md:text-xs font-semibold px-2 py-0.5 md:px-2.5 md:py-1 rounded-full">
                                Open
                            </span>
                        @else
                            <span class="bg-red-100 text-red-800 text-[10px] md:text-xs font-semibold px-2 py-0.5 md:px-2.5 md:py-1 rounded-full">
                                Closed
                            </span>
                        @endif
                    </td>
                    <td class="px-2 py-2 md:px-6 md:py-3 flex gap-1.5 md:gap-2">
                        <a href="{{ route('registrar.grade-submission-schedules.edit', ['schedule' => $schedule, 'school_year' => $selectedSchoolYear]) }}"
                            class="text-[10px] md:text-xs bg-blue-50 hover:bg-blue-100 text-blue-600 px-2 py-1 md:px-3 md:py-1.5 rounded-lg transition whitespace-nowrap">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('registrar.grade-submission-schedules.destroy', $schedule) }}"
                            onsubmit="return confirm('Delete this schedule?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="text-[10px] md:text-xs bg-red-50 hover:bg-red-100 text-red-600 px-2 py-1 md:px-3 md:py-1.5 rounded-lg transition whitespace-nowrap">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-2 py-4 md:px-6 md:py-8 text-center text-gray-400 text-[10px] md:text-xs">
                        No schedules yet. Add one using the button above.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table></div>
    </div>

    <script>
        function filterBySchoolYear() {
            const selectedSchoolYear = document.getElementById('schoolYearFilter').value;
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('school_year', selectedSchoolYear);
            window.location.href = currentUrl.toString();
        }
    </script>

@endsection
