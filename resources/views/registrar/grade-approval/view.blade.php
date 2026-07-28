@extends('layouts.app')

@section('title', 'View Grades')

@section('content')
    <div class="mb-6">
        <a href="{{ route('registrar.grade-approval') }}" class="text-sm font-medium text-[#1a5c1a] hover:text-green-900">
            Back to Grade Approval
        </a>
    </div>

    <!-- Class Information -->
    <div class="ra-card mb-6 p-5 sm:p-6">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">School Year</p>
                <p class="mt-1 text-sm font-medium text-gray-800">{{ $classSchedule->school_year ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Grade Level</p>
                <p class="mt-1 text-sm font-medium text-gray-800">{{ $classSchedule->section->yearLevel->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Section</p>
                <p class="mt-1 text-sm font-medium text-gray-800">{{ $classSchedule->section->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Subject</p>
                <p class="mt-1 text-sm font-medium text-gray-800">{{ $classSchedule->subject->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Subject Code</p>
                <p class="mt-1 text-sm font-medium text-gray-800">{{ $classSchedule->subject->code ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Teacher Name</p>
                <p class="mt-1 text-sm font-medium text-gray-800">{{ $classSchedule->teacher->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Total Students</p>
                <p class="mt-1 text-sm font-medium text-gray-800">{{ $grades->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Student Grades Table -->
    <div class="ra-card mb-6 p-5 sm:p-6">
        <div class="mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Student Grades</h2>
            <p class="mt-1 text-sm text-gray-500">Review the imported grades submitted by the teacher.</p>
        </div>

        <div class="overflow-x-auto border border-gray-200 rounded-lg">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left py-3 px-4 font-medium text-gray-600">Student Name</th>
                        <th class="text-center py-3 px-4 font-medium text-gray-600">Term 1</th>
                        <th class="text-center py-3 px-4 font-medium text-gray-600">Term 2</th>
                        <th class="text-center py-3 px-4 font-medium text-gray-600">Term 3</th>
                        <th class="text-center py-3 px-4 font-medium text-gray-600">Final Rating</th>
                        <th class="text-center py-3 px-4 font-medium text-gray-600">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($grades as $grade)
                        @php
                            // Calculate Final Rating only if all three terms are approved
                            $finalRating = '-';
                            if ($grade->term_1 && $grade->term_2 && $grade->term_3) {
                                // Check if all terms are approved (status is approved and term fields are not null)
                                $term1Approved = $grade->status === 'approved' && $grade->term_1;
                                $term2Approved = $grade->status === 'approved' && $grade->term_2;
                                $term3Approved = $grade->status === 'approved' && $grade->term_3;
                                
                                if ($term1Approved && $term2Approved && $term3Approved) {
                                    $finalRating = round(($grade->term_1 + $grade->term_2 + $grade->term_3) / 3, 2);
                                }
                            }
                        @endphp
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3 px-4 font-medium text-gray-800">{{ $grade->student->last_name }}, {{ $grade->student->first_name }}</td>
                            <td class="py-3 px-4 text-center text-gray-700">{{ $grade->term_1 ?? '-' }}</td>
                            <td class="py-3 px-4 text-center text-gray-700">{{ $grade->term_2 ?? '-' }}</td>
                            <td class="py-3 px-4 text-center text-gray-700">{{ $grade->term_3 ?? '-' }}</td>
                            <td class="py-3 px-4 text-center font-semibold text-gray-800">{{ $finalRating }}</td>
                            <td class="py-3 px-4 text-center text-gray-600">{{ $grade->remarks ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">No grades found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Status Banner --}}
    @if($grades->isNotEmpty() && $grades->first()->status === 'approved')
        <div class="rounded-xl border border-green-200 bg-green-50 p-6 mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-green-800">Grades Approved</h3>
                    <p class="text-sm text-green-600">These grades have been approved and are now official.</p>
                </div>
            </div>
        </div>
    @elseif($grades->isNotEmpty() && $grades->first()->status === 'rejected')
        <div class="rounded-xl border border-red-200 bg-red-50 p-6 mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-red-800">Grades Rejected</h3>
                    <p class="text-sm text-red-600">These grades were rejected and returned to the teacher for revision.</p>
                    @if($grades->first()->rejection_reason)
                        <p class="text-sm text-red-700 mt-1"><strong>Reason:</strong> {{ $grades->first()->rejection_reason }}</p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Grade Timeline --}}
    @if($gradeTimeline && $gradeTimeline->isNotEmpty())
    <div class="ra-card p-5 sm:p-6">
            <div class="mb-4">
                <h3 class="text-sm font-medium text-gray-800">Grade Timeline</h3>
                <p class="text-xs text-gray-500 mt-1">Track the history of grade changes for this class</p>
            </div>
            <div class="space-y-4">
                @foreach($gradeTimeline as $event)
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-3 h-3 rounded-full
                                {{ $event['status'] === 'Approved' ? 'bg-green-500' : 
                                   ($event['status'] === 'Rejected' ? 'bg-red-500' : 
                                   ($event['status'] === 'Submitted' ? 'bg-yellow-500' : 'bg-gray-400')) }}">
                            </div>
                            @if(!$loop->last)
                                <div class="w-0.5 h-full bg-gray-200 mt-1"></div>
                            @endif
                        </div>
                        <div class="flex-1 pb-4">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-medium text-gray-800">{{ $event['action'] }}</span>
                                <span class="text-xs px-2 py-0.5 rounded-full
                                    {{ $event['status'] === 'Approved' ? 'bg-green-100 text-green-700' : 
                                       ($event['status'] === 'Rejected' ? 'bg-red-100 text-red-600' : 
                                       ($event['status'] === 'Submitted' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600')) }}">
                                    {{ $event['status'] }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500">{{ $event['timestamp']->format('M d, Y - g:i A') }}</p>
                            <p class="text-xs text-gray-600 mt-1">{{ $event['description'] }}</p>
                            <p class="text-xs text-gray-400 mt-1">By: {{ $event['user'] }}</p>
                            @if($event['rejection_reason'])
                                <div class="mt-2 p-2 bg-red-50 border border-red-200 rounded">
                                    <p class="text-xs text-red-600"><strong>Rejection Reason:</strong> {{ $event['rejection_reason'] }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endsection
