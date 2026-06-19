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
                </div>

                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs">
                        <tr>
                            <th class="text-left px-4 py-2">Student</th>
                            <th class="text-left px-4 py-2">Final Grade</th>
                            <th class="text-left px-4 py-2">Remarks</th>
                            <th class="text-left px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($grades as $grade)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 text-gray-800">
                                {{ $grade->student->last_name }}, {{ $grade->student->first_name }}
                            </td>
                            <td class="px-4 py-2 font-semibold text-green-800">{{ $grade->final_grade }}</td>
                            <td class="px-4 py-2">
                                <span class="text-xs px-2 py-1 rounded-full
                                    {{ $grade->remarks == 'Passed' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">
                                    {{ $grade->remarks }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                <div class="flex gap-2">
                                    <form method="POST" action="{{ route('registrar.grade-approval.approve', $grade) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                            class="text-xs bg-green-50 hover:bg-green-100 text-green-700 px-3 py-1.5 rounded-lg transition">
                                            Approve
                                        </button>
                                    </form>

                                    <button type="button"
                                        onclick="document.getElementById('reject-{{ $grade->id }}').classList.toggle('hidden')"
                                        class="text-xs bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-lg transition">
                                        Reject
                                    </button>
                                </div>

                                {{-- Reject reason form --}}
                                <div id="reject-{{ $grade->id }}" class="hidden mt-2">
                                    <form method="POST" action="{{ route('registrar.grade-approval.reject', $grade) }}" class="flex gap-2">
                                        @csrf @method('PATCH')
                                        <input type="text" name="rejection_reason" required placeholder="Reason for rejection..."
                                            class="flex-1 border border-gray-300 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-red-400">
                                        <button type="submit"
                                            class="text-xs bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg transition">
                                            Confirm
                                        </button>
                                    </form>
                                </div>
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
