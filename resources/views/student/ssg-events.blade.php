@extends('layouts.app')
@section('title', 'SSG Events & Fines')
@section('content')
    @if(!$student)
        <div class="rounded-2xl border border-yellow-200 bg-gradient-to-br from-yellow-50 to-amber-50 p-8 text-center">
            <h2 class="text-xl font-semibold text-yellow-800 mb-2">Student Profile Not Found</h2>
            <p class="text-yellow-700">Your student profile has not yet been created. Please contact the Registrar.</p>
        </div>
    @else
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="st-card p-6">
                <p class="text-sm text-gray-500 mb-1">Total SSG Events</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalEvents }}</p>
            </div>
            <div class="st-card p-6">
                <p class="text-sm text-gray-500 mb-1">Events Attended</p>
                <p class="text-2xl font-bold text-green-700">{{ $eventsAttended }}</p>
            </div>
            <div class="st-card p-6">
                <p class="text-sm text-gray-500 mb-1">Events Missed</p>
                <p class="text-2xl font-bold text-red-600">{{ $eventsMissed }}</p>
            </div>
            <div class="rounded-2xl border border-green-200 bg-gradient-to-br from-green-50 to-emerald-50 p-6">
                <p class="text-sm text-green-700 mb-1">Outstanding Balance</p>
                <p class="text-2xl font-bold text-green-800">₱{{ number_format($outstandingBalance, 2) }}</p>
            </div>
        </div>

        <!-- Attendance Records Table -->
        <div class="st-card overflow-hidden p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">SSG Event Records</h2>
            
            @if($attendances->isEmpty())
                <div class="text-center py-12">
                    <p class="text-gray-500">No SSG event records available.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 font-medium text-gray-600">Event Name</th>
                                <th class="text-left py-3 px-4 font-medium text-gray-600">Event Date</th>
                                <th class="text-center py-3 px-4 font-medium text-gray-600">Attendance Status</th>
                                <th class="text-center py-3 px-4 font-medium text-gray-600">Fine Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendances as $attendance)
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-3 px-4 text-gray-800 font-medium">
                                        {{ $attendance->ssgEvent->title ?? 'N/A' }}
                                    </td>
                                    <td class="py-3 px-4 text-gray-600">
                                        {{ $attendance->ssgEvent->event_date ? $attendance->ssgEvent->event_date->format('M d, Y') : 'N/A' }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if($attendance->is_present)
                                            <span class="px-2 py-1 rounded-full text-xs bg-green-50 text-green-700 font-medium">Present</span>
                                        @else
                                            <span class="px-2 py-1 rounded-full text-xs bg-red-50 text-red-600 font-medium">Absent</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center text-gray-700">
                                        ₱{{ number_format($attendance->actual_fine ?? 0, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif
@endsection
