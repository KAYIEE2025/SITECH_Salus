@extends('layouts.app')
@section('title', 'Grade Approval')
@section('content')

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg px-4 py-3 mb-6">
            {{ session('success') }}
        </div>
    @endif

    {{-- Pending Approvals --}}
    <div class="bg-white rounded-xl border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-800">Pending Grade Submissions</h2>
        </div>

        @forelse($pendingGrades as $classScheduleId => $grades)
            @php $first = $grades->first(); @endphp
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="font-semibold text-gray-800">
                            {{ $first->classSchedule->subject->code ?? '—' }} —
                            {{ $first->classSchedule->subject->name ?? '' }}
                        </p>
                        <p class="text-xs text-gray-500">
                            Teacher: {{ $first->classSchedule->teacher->name ?? '—' }} ·
                            {{ $first->classSchedule->section->yearLevel->name ?? '' }}
                            Sec {{ $first->classSchedule->section->name ?? '' }}
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('registrar.grade-approval.view', $first->classSchedule) }}" class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1.5 rounded-lg transition">
                            View Details
                        </a>
                        @if($first->status === 'submitted')
                            <form method="POST" action="{{ route('registrar.grade-approval.approve-class', $first->classSchedule) }}" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs bg-green-50 hover:bg-green-100 text-green-700 px-3 py-1.5 rounded-lg transition">
                                    Approve All
                                </button>
                            </form>
                            <button type="button" onclick="document.getElementById('reject-form-{{ $first->classSchedule->id }}').classList.toggle('hidden')" class="text-xs bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-lg transition">
                                Reject All
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Reject Form --}}
                @if($first->status === 'submitted')
                    <div id="reject-form-{{ $first->classSchedule->id }}" class="hidden mb-3 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <form method="POST" action="{{ route('registrar.grade-approval.reject-class', $first->classSchedule) }}">
                            @csrf @method('PATCH')
                            <div class="mb-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Reason for Rejection <span class="text-red-600">*</span>
                                </label>
                                <textarea name="rejection_reason" required rows="2"
                                    class="w-full border border-gray-300 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-red-400"
                                    placeholder="Please provide a reason for rejecting these grades..."></textarea>
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="text-xs bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg transition">
                                    Confirm Rejection
                                </button>
                                <button type="button" onclick="document.getElementById('reject-form-{{ $first->classSchedule->id }}').classList.add('hidden')" class="text-xs bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1 rounded-lg transition">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs">
                        <tr>
                            <th class="text-left px-4 py-2">Student Name</th>
                            <th class="text-center px-4 py-2">Term 1</th>
                            <th class="text-center px-4 py-2">Term 2</th>
                            <th class="text-center px-4 py-2">Term 3</th>
                            <th class="text-center px-4 py-2">Final Rating</th>
                            <th class="text-center px-4 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($grades as $grade)
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
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-gray-800">
                                    {{ $grade->student->last_name }}, {{ $grade->student->first_name }}
                                </td>
                                <td class="px-4 py-2 text-center text-gray-700">{{ $grade->term_1 ?? '-' }}</td>
                                <td class="px-4 py-2 text-center text-gray-700">{{ $grade->term_2 ?? '-' }}</td>
                                <td class="px-4 py-2 text-center text-gray-700">{{ $grade->term_3 ?? '-' }}</td>
                                <td class="px-4 py-2 text-center font-semibold text-gray-800">{{ $finalRating }}</td>
                                <td class="px-4 py-2 text-center">
                                    <span class="text-xs px-2 py-1 rounded-full
                                        {{ $grade->status === 'approved' ? 'bg-green-50 text-green-700' : 
                                           ($grade->status === 'rejected' ? 'bg-red-50 text-red-600' : 
                                           ($grade->status === 'submitted' ? 'bg-yellow-50 text-yellow-700' : 'bg-gray-50 text-gray-600')) }}">
                                        {{ ucfirst($grade->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @empty
            <div class="px-6 py-8 text-center text-gray-400">
                No grade submissions pending approval.
            </div>
        @endforelse
    </div>

    {{-- Review History --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-800">Review History</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs">
                <tr>
                    <th class="text-left px-6 py-3">Student</th>
                    <th class="text-left px-6 py-3">Subject</th>
                    <th class="text-left px-6 py-3">Grade</th>
                    <th class="text-left px-6 py-3">Status</th>
                    <th class="text-left px-6 py-3">Reviewed</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($history as $grade)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3 text-gray-800">{{ $grade->student->last_name }}, {{ $grade->student->first_name }}</td>
                    <td class="px-6 py-3 text-gray-600">{{ $grade->classSchedule->subject->code ?? '—' }}</td>
                    <td class="px-6 py-3 font-semibold text-green-800">{{ $grade->final_grade }}</td>
                    <td class="px-6 py-3">
                        <span class="text-xs px-2 py-1 rounded-full
                            {{ $grade->status == 'approved' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">
                            {{ ucfirst($grade->status) }}
                        </span>
                        @if($grade->status == 'rejected')
                            <p class="text-xs text-gray-400 mt-1">{{ $grade->rejection_reason }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-3 text-gray-400 text-xs">
                        {{ $grade->reviewed_at?->format('M d, Y h:i A') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-400">No reviewed grades yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection
