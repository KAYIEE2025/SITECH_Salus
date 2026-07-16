@extends('layouts.app')

@section('title', 'My Teaching Schedule')

@section('content')
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">My Teaching Schedule</h2>
            <p class="mt-1 text-sm text-gray-500">Your assigned classes for the selected school year.</p>
        </div>

        <button type="button"
            onclick="window.open('{{ route('teacher.schedule.print', ['school_year' => $schoolYear]) }}', '_blank')"
            class="inline-flex items-center justify-center rounded-lg bg-green-800 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-green-900">
            Print Schedule
        </button>
    </div>

    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-6">
        <form method="GET" action="{{ route('teacher.schedule.index') }}" class="grid gap-3 md:grid-cols-[1fr_auto] md:items-end">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">School Year</label>
                <select name="school_year" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    @forelse($schoolYears as $year)
                        <option value="{{ $year }}" {{ $schoolYear === $year ? 'selected' : '' }}>{{ $year }}</option>
                    @empty
                        <option value="">No school year available</option>
                    @endforelse
                </select>
            </div>
            <button type="submit" class="rounded-lg bg-green-800 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-green-900">
                View Schedule
            </button>
        </form>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <div class="border-b border-gray-100 px-6 py-4">
            <h3 class="text-base font-semibold text-gray-800">Teacher Information</h3>
        </div>
        <div class="grid gap-4 px-6 py-5 text-sm md:grid-cols-2">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Teacher Name</p>
                <p class="mt-1 font-medium text-gray-800">{{ $teacher->name }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">School Year</p>
                <p class="mt-1 font-medium text-gray-800">{{ $schoolYear ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-xl border border-gray-200 bg-white">
        <div class="border-b border-gray-100 px-6 py-4">
            <h3 class="text-base font-semibold text-gray-800">Assigned Classes</h3>
            <p class="mt-0.5 text-xs text-gray-500">{{ $schoolYear ?? 'N/A' }}</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-left">Subject Code</th>
                        <th class="px-6 py-3 text-left">Subject Name</th>
                        <th class="px-6 py-3 text-left">Grade Level</th>
                        <th class="px-6 py-3 text-left">Section</th>
                        <th class="px-6 py-3 text-left">Day</th>
                        <th class="px-6 py-3 text-left">Time</th>
                        <th class="px-6 py-3 text-left">Room</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($schedules as $schedule)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 font-medium text-gray-800">{{ $schedule->subject->code ?? 'N/A' }}</td>
                            <td class="px-6 py-3 text-gray-700">{{ $schedule->subject->name ?? 'No subject assigned' }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $schedule->section?->yearLevel?->name ?? 'N/A' }}</td>
                            <td class="px-6 py-3 text-gray-600">Section {{ $schedule->section->name ?? 'N/A' }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ implode(', ', $schedule->days ?? []) }}</td>
                            <td class="px-6 py-3 text-xs text-gray-600">
                                {{ \Carbon\Carbon::parse($schedule->time_start)->format('h:i A') }} -
                                {{ \Carbon\Carbon::parse($schedule->time_end)->format('h:i A') }}
                            </td>
                            <td class="px-6 py-3 text-gray-600">{{ $schedule->room ?: 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-400">
                                No assigned classes for the selected school year.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-100 bg-gray-50 px-6 py-4 text-sm">
            <span class="font-semibold text-gray-800">Total Classes Handled:</span>
            <span class="text-gray-700">{{ $schedules->count() }}</span>
        </div>
    </div>
@endsection
