@extends('layouts.app')

@section('title', 'Grade Reopening Requests')

@section('content')
    <div class="mb-4 md:mb-6">
        <h1 class="text-xl md:text-2xl font-semibold text-gray-800">Grade Reopening Requests</h1>
        <p class="text-xs md:text-sm text-gray-600 mt-1">Manage teacher requests to reopen grade submission periods.</p>
    </div>

    <div class="tc-card p-4 md:p-6">
        <div class="overflow-x-auto">
            <table class="w-full min-w-max text-xs md:text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left py-2 px-2 md:py-3 md:px-4 font-medium text-gray-600 text-[10px] md:text-xs">Teacher</th>
                        <th class="text-left py-2 px-2 md:py-3 md:px-4 font-medium text-gray-600 text-[10px] md:text-xs">School Year</th>
                        <th class="text-left py-2 px-2 md:py-3 md:px-4 font-medium text-gray-600 text-[10px] md:text-xs">Grading Period</th>
                        <th class="text-left py-2 px-2 md:py-3 md:px-4 font-medium text-gray-600 text-[10px] md:text-xs">Reason</th>
                        <th class="text-left py-2 px-2 md:py-3 md:px-4 font-medium text-gray-600 text-[10px] md:text-xs">Requested At</th>
                        <th class="text-left py-2 px-2 md:py-3 md:px-4 font-medium text-gray-600 text-[10px] md:text-xs">Status</th>
                        <th class="text-left py-2 px-2 md:py-3 md:px-4 font-medium text-gray-600 text-[10px] md:text-xs">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($requests as $reopeningRequest)
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 px-2 md:py-3 md:px-4">
                                <div class="font-medium text-gray-800 text-xs md:text-sm">{{ $reopeningRequest->teacher->name }}</div>
                            </td>
                            <td class="py-2 px-2 md:py-3 md:px-4 text-gray-600">{{ $reopeningRequest->school_year }}</td>
                            <td class="py-2 px-2 md:py-3 md:px-4 text-gray-600">Term {{ $reopeningRequest->grading_period }}</td>
                            <td class="py-2 px-2 md:py-3 md:px-4 text-gray-600 max-w-xs md:max-w-sm truncate">{{ $reopeningRequest->reason }}</td>
                            <td class="py-2 px-2 md:py-3 md:px-4 text-gray-600">{{ $reopeningRequest->requested_at ? $reopeningRequest->requested_at->format('M d, Y g:i A') : '-' }}</td>
                            <td class="py-2 px-2 md:py-3 md:px-4">
                                @if($reopeningRequest->status === 'Pending')
                                    <span class="inline-flex items-center px-2 py-0.5 md:px-2.5 md:py-0.5 rounded-full text-[10px] md:text-xs font-medium bg-yellow-100 text-yellow-800 whitespace-nowrap">
                                        {{ $reopeningRequest->status }}
                                    </span>
                                @elseif($reopeningRequest->status === 'Approved')
                                    <span class="inline-flex items-center px-2 py-0.5 md:px-2.5 md:py-0.5 rounded-full text-[10px] md:text-xs font-medium bg-green-100 text-green-800 whitespace-nowrap">
                                        {{ $reopeningRequest->status }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 md:px-2.5 md:py-0.5 rounded-full text-[10px] md:text-xs font-medium bg-red-100 text-red-800 whitespace-nowrap">
                                        {{ $reopeningRequest->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-2 px-2 md:py-3 md:px-4">
                                <div class="flex gap-1.5 md:gap-2">
                                    <a href="{{ route('registrar.grade-reopening-requests.show', $reopeningRequest) }}" 
                                       class="text-blue-600 hover:text-blue-800 text-xs md:text-sm font-medium whitespace-nowrap">
                                        View
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 md:py-8 text-gray-400 text-[10px] md:text-xs">
                                No reopening requests found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
