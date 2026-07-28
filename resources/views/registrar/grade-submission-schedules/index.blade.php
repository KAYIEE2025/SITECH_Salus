@extends('layouts.app')
@section('title', 'Grade Submission Schedules')
@section('content')

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg px-4 py-3 mb-6">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3 mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <div>
                <h2 class="text-base font-semibold text-gray-800">Grade Submission Schedules</h2>
                <p class="text-xs text-gray-500 mt-0.5">Total: {{ $schedules->count() }} schedule(s)</p>
            </div>
            <a href="{{ route('registrar.grade-submission-schedules.create') }}"
                class="bg-green-800 hover:bg-green-900 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                Add Schedule
            </a>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs">
                <tr>
                    <th class="text-left px-6 py-3">School Year</th>
                    <th class="text-left px-6 py-3">Grading Period</th>
                    <th class="text-left px-6 py-3">Start</th>
                    <th class="text-left px-6 py-3">End</th>
                    <th class="text-left px-6 py-3">Status</th>
                    <th class="text-left px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($schedules as $schedule)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3 text-gray-600 font-medium">{{ $schedule->school_year }}</td>
                    <td class="px-6 py-3">
                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-1 rounded-full">
                            Term {{ $schedule->grading_period }}
                        </span>
                    </td>
                    <td class="px-6 py-3 text-gray-500">
                        {{ $schedule->start_at->format('M d, Y g:i A') }}
                    </td>
                    <td class="px-6 py-3 text-gray-500">
                        {{ $schedule->end_at->format('M d, Y g:i A') }}
                    </td>
                    <td class="px-6 py-3">
                        @if($schedule->status === 'Upcoming')
                            <span class="bg-gray-100 text-gray-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                                Upcoming
                            </span>
                        @elseif($schedule->status === 'Open')
                            <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-1 rounded-full">
                                Open
                            </span>
                        @else
                            <span class="bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-1 rounded-full">
                                Closed
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-3 flex gap-2">
                        <a href="{{ route('registrar.grade-submission-schedules.edit', $schedule) }}"
                            class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-600 px-3 py-1.5 rounded-lg transition">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('registrar.grade-submission-schedules.destroy', $schedule) }}"
                            onsubmit="return confirm('Delete this schedule?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="text-xs bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-lg transition">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                        No schedules yet. Add one using the button above.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection
