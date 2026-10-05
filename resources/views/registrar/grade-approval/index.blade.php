@extends('layouts.app')
@section('title', 'Grade Approval')
@section('content')

    @session('success')
        <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg px-4 py-3 mb-6">
            {{ $value }}
        </div>
    @endsession

    {{-- Pending Approvals --}}
    <div class="ra-card mb-6 overflow-hidden">
        <div class="ra-card-header flex flex-col items-start justify-between gap-3 sm:gap-4 sm:flex-row sm:items-center">
            <div class="flex items-center gap-2 md:gap-3">
                <input type="checkbox"
                       id="select-all-checkbox"
                       class="w-4 h-4 text-green-600 rounded border-gray-300 focus:ring-green-500"
                       onchange="toggleSelectAll()">
                <h2 class="text-sm md:text-base font-semibold text-gray-800">Pending Grade Submissions</h2>
            </div>
            <div class="flex gap-1.5 md:gap-2">
                <button id="approve-selected-btn" disabled onclick="approveSelected()" class="text-[10px] md:text-xs bg-green-600 hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white px-2 py-1.5 md:px-4 md:py-2 rounded-lg transition whitespace-nowrap">
                    Approve Selected
                </button>
                <button id="reject-selected-btn" disabled onclick="showRejectModal()" class="text-[10px] md:text-xs bg-red-600 hover:bg-red-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white px-2 py-1.5 md:px-4 md:py-2 rounded-lg transition whitespace-nowrap">
                    Reject Selected
                </button>
            </div>
        </div>

        @forelse($pendingGrades as $classScheduleId => $grades)
            @php $first = $grades->first(); @endphp
            <div class="px-4 py-3 md:px-6 md:py-4 border-b border-gray-100">
                <div class="flex items-center justify-between mb-2 md:mb-3">
                    <div class="flex items-center gap-2 md:gap-3">
                        <input type="checkbox"
                               class="class-checkbox w-4 h-4 text-green-600 rounded border-gray-300 focus:ring-green-500"
                               name="class_schedule_ids[]"
                               value="{{ $first->classSchedule->id }}"
                               data-grading-period="{{ $first->grading_period ?? '' }}"
                               onchange="updateButtons()">
                        <div>
                            <p class="font-semibold text-gray-800 text-sm md:text-base">
                                {{ $first->classSchedule->subject->code ?? '—' }} —
                                {{ $first->classSchedule->subject->name ?? '' }}
                            </p>
                            <p class="text-[10px] md:text-xs text-gray-500">
                                Teacher: {{ $first->classSchedule->teacher->name ?? '—' }} ·
                                {{ $first->classSchedule->section->yearLevel->name ?? '' }}
                                Sec {{ $first->classSchedule->section->name ?? '' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-1.5 md:gap-2">
                        <a href="{{ route('registrar.grade-approval.view', $first->classSchedule) }}" class="text-[10px] md:text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-2 py-1 md:px-3 md:py-1.5 rounded-lg transition whitespace-nowrap">
                            View Details
                        </a>
                        @if($first->status === 'submitted')
                            <form method="POST" action="{{ route('registrar.grade-approval.approve-class', $first->classSchedule) }}" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-[10px] md:text-xs bg-green-50 hover:bg-green-100 text-green-700 px-2 py-1 md:px-3 md:py-1.5 rounded-lg transition whitespace-nowrap">
                                    Approve All
                                </button>
                            </form>
                            <button type="button" onclick="document.getElementById('reject-form-{{ $first->classSchedule->id }}').classList.toggle('hidden')" class="text-[10px] md:text-xs bg-red-50 hover:bg-red-100 text-red-600 px-2 py-1 md:px-3 md:py-1.5 rounded-lg transition whitespace-nowrap">
                                Reject All
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Reject Form --}}
                @if($first->status === 'submitted')
                    <div id="reject-form-{{ $first->classSchedule->id }}" class="hidden mb-3 p-2 md:p-4 bg-red-50 border border-red-200 rounded-lg">
                        <form method="POST" action="{{ route('registrar.grade-approval.reject-class', $first->classSchedule) }}">
                            @csrf @method('PATCH')
                            <div class="mb-1.5 md:mb-2">
                                <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">
                                    Reason for Rejection <span class="text-red-600">*</span>
                                </label>
                                <textarea name="rejection_reason" required rows="2"
                                    class="w-full border border-gray-300 rounded-lg px-2 py-1 text-[10px] md:text-xs focus:outline-none focus:ring-2 focus:ring-red-400"
                                    placeholder="Please provide a reason for rejecting these grades..."></textarea>
                            </div>
                            <div class="flex gap-1.5 md:gap-2">
                                <button type="submit" class="text-[10px] md:text-xs bg-red-600 hover:bg-red-700 text-white px-2 py-1 md:px-3 md:py-1 rounded-lg transition whitespace-nowrap">
                                    Confirm Rejection
                                </button>
                                <button type="button" onclick="document.getElementById('reject-form-{{ $first->classSchedule->id }}').classList.add('hidden')" class="text-[10px] md:text-xs bg-gray-200 hover:bg-gray-300 text-gray-700 px-2 py-1 md:px-3 md:py-1 rounded-lg transition whitespace-nowrap">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                <div class="overflow-x-auto"><table class="w-full min-w-max text-xs md:text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-[10px] md:text-xs">
                        <tr>
                            <th class="text-left px-2 py-2 md:px-4 md:py-2">Student Name</th>
                            <th class="text-left px-2 py-2 md:px-4 md:py-2">Student Number</th>
                            <th class="text-center px-2 py-2 md:px-4 md:py-2">Account</th>
                            <th class="text-center px-2 py-2 md:px-4 md:py-2">Term 1</th>
                            <th class="text-center px-2 py-2 md:px-4 md:py-2">Term 2</th>
                            <th class="text-center px-2 py-2 md:px-4 md:py-2">Term 3</th>
                            <th class="text-center px-2 py-2 md:px-4 md:py-2">Final Rating</th>
                            <th class="text-center px-2 py-2 md:px-4 md:py-2">Status</th>
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
                                <td class="px-2 py-2 md:px-4 md:py-2 text-gray-800">
                                    @if($grade->student)
                                        {{ $grade->student->last_name }}, {{ $grade->student->first_name }}
                                    @else
                                        {{ $grade->student_name ?? 'Unknown' }}
                                    @endif
                                </td>
                                <td class="px-2 py-2 md:px-4 md:py-2 text-gray-600">
                                    @if($grade->student)
                                        {{ $grade->student->student_number }}
                                    @else
                                        {{ $grade->student_number ?? '-' }}
                                    @endif
                                </td>
                                <td class="px-2 py-2 md:px-4 md:py-2 text-center">
                                    @if($grade->student)
                                        <span class="text-[10px] md:text-xs px-1.5 py-0.5 rounded-full bg-green-50 text-green-700">Has Account</span>
                                    @else
                                        <span class="text-[10px] md:text-xs px-1.5 py-0.5 rounded-full bg-gray-50 text-gray-600">No Account</span>
                                    @endif
                                </td>
                                <td class="px-2 py-2 md:px-4 md:py-2 text-center text-gray-700">{{ $grade->term_1 ?? '-' }}</td>
                                <td class="px-2 py-2 md:px-4 md:py-2 text-center text-gray-700">{{ $grade->term_2 ?? '-' }}</td>
                                <td class="px-2 py-2 md:px-4 md:py-2 text-center text-gray-700">{{ $grade->term_3 ?? '-' }}</td>
                                <td class="px-2 py-2 md:px-4 md:py-2 text-center font-semibold text-gray-800">{{ $finalRating }}</td>
                                <td class="px-2 py-2 md:px-4 md:py-2 text-center">
                                    <span class="text-[10px] md:text-xs px-1.5 py-0.5 md:px-2 md:py-1 rounded-full
                                        {{ $grade->status === 'approved' ? 'bg-green-50 text-green-700' : 
                                           ($grade->status === 'rejected' ? 'bg-red-50 text-red-600' : 
                                           ($grade->status === 'submitted' ? 'bg-yellow-50 text-yellow-700' : 'bg-gray-50 text-gray-600')) }}">
                                        {{ ucfirst($grade->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table></div>
            </div>
        @empty
            <div class="px-4 py-6 md:px-6 md:py-8 text-center text-gray-400">
                No grade submissions pending approval.
            </div>
        @endforelse
    </div>

    {{-- Review History --}}
    <div class="ra-card overflow-hidden">
        <div class="ra-card-header">
            <h2 class="text-sm md:text-base font-semibold text-gray-800">Review History</h2>
        </div>
        <div class="overflow-x-auto"><table class="w-full min-w-max text-xs md:text-sm">
            <thead class="bg-gray-50 text-gray-500 text-[10px] md:text-xs">
                <tr>
                    <th class="text-left px-2 py-2 md:px-6 md:py-3">Student</th>
                    <th class="text-left px-2 py-2 md:px-6 md:py-3">Subject</th>
                    <th class="text-left px-2 py-2 md:px-6 md:py-3">Grade</th>
                    <th class="text-left px-2 py-2 md:px-6 md:py-3">Status</th>
                    <th class="text-left px-2 py-2 md:px-6 md:py-3">Reviewed</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($history as $grade)
                <tr class="hover:bg-gray-50">
                    <td class="px-2 py-2 md:px-6 md:py-3 text-gray-800">
                        @if($grade->student)
                            {{ $grade->student->last_name }}, {{ $grade->student->first_name }}
                        @else
                            {{ $grade->student_name ?? 'Unknown' }}
                        @endif
                    </td>
                    <td class="px-2 py-2 md:px-6 md:py-3 text-gray-600">{{ $grade->classSchedule->subject->code ?? '—' }}</td>
                    <td class="px-2 py-2 md:px-6 md:py-3 font-semibold text-green-800">{{ $grade->final_grade }}</td>
                    <td class="px-2 py-2 md:px-6 md:py-3">
                        <span class="text-[10px] md:text-xs px-1.5 py-0.5 md:px-2 md:py-1 rounded-full
                            {{ $grade->status == 'approved' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">
                            {{ ucfirst($grade->status) }}
                        </span>
                        @if($grade->status == 'rejected')
                            <p class="text-[10px] md:text-xs text-gray-400 mt-1">{{ $grade->rejection_reason }}</p>
                        @endif
                    </td>
                    <td class="px-2 py-2 md:px-6 md:py-3 text-gray-400 text-[10px] md:text-xs">
                        {{ $grade->reviewed_at?->format('M d, Y h:i A') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-2 py-4 md:px-6 md:py-8 text-center text-gray-400">No reviewed grades yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table></div>
    </div>

    {{-- Reject Selected Modal --}}
    <div id="reject-selected-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-4 md:p-6 w-full max-w-md mx-4">
            <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-4">Reject Selected Classes</h3>
            <div class="mb-3 md:mb-4">
                <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">
                    Reason for Rejection <span class="text-red-600">*</span>
                </label>
                <textarea id="rejection-reason" required rows="3"
                    class="w-full border border-gray-300 rounded-lg px-2 py-1.5 md:px-3 md:py-2 text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-red-400"
                    placeholder="Please provide a reason for rejecting these grades..."></textarea>
            </div>
            <div class="flex gap-1.5 md:gap-2 justify-end">
                <button onclick="hideRejectModal()" class="text-xs md:text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1.5 md:px-4 md:py-2 rounded-lg transition whitespace-nowrap">
                    Cancel
                </button>
                <button onclick="rejectSelected()" class="text-xs md:text-sm bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 md:px-4 md:py-2 rounded-lg transition whitespace-nowrap">
                    Confirm Rejection
                </button>
            </div>
        </div>
    </div>

    <script>
        function toggleSelectAll() {
            const selectAllCheckbox = document.getElementById('select-all-checkbox');
            const classCheckboxes = document.querySelectorAll('.class-checkbox');

            classCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });

            updateButtons();
        }

        function updateButtons() {
            const checkboxes = document.querySelectorAll('.class-checkbox:checked');
            const approveBtn = document.getElementById('approve-selected-btn');
            const rejectBtn = document.getElementById('reject-selected-btn');

            approveBtn.disabled = checkboxes.length === 0;
            rejectBtn.disabled = checkboxes.length === 0;
        }

        function getSelectedData() {
            const checkboxes = document.querySelectorAll('.class-checkbox:checked');
            const data = {
                class_schedule_ids: []
            };

            checkboxes.forEach(checkbox => {
                const classId = checkbox.value;
                data.class_schedule_ids.push(classId);
            });

            return data;
        }

        function approveSelected() {
            const data = getSelectedData();

            console.log('APPROVE SELECTED - Data being sent:', data);

            if (data.class_schedule_ids.length === 0) {
                alert('Please select at least one class to approve.');
                return;
            }

            if (!confirm(`Are you sure you want to approve ${data.class_schedule_ids.length} selected class(es)?`)) {
                return;
            }

            console.log('APPROVE SELECTED - Sending to route:', '{{ route('registrar.grade-approval.approve-selected') }}');

            fetch('{{ route('registrar.grade-approval.approve-selected') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                console.log('APPROVE SELECTED - Response received:', data);
                if (data.success) {
                    console.log('APPROVE SELECTED - Success, reloading page');
                    window.location.href = '{{ route('registrar.grade-approval') }}';
                } else {
                    console.error('APPROVE SELECTED - Server error:', data.message);
                    alert(data.message || 'Error approving selected classes.');
                }
            })
            .catch(error => {
                console.error('APPROVE SELECTED - Network error:', error);
                alert('Error approving selected classes.');
            });
        }

        function showRejectModal() {
            const checkboxes = document.querySelectorAll('.class-checkbox:checked');

            if (checkboxes.length === 0) {
                alert('Please select at least one class to reject.');
                return;
            }

            document.getElementById('reject-selected-modal').classList.remove('hidden');
        }

        function hideRejectModal() {
            document.getElementById('reject-selected-modal').classList.add('hidden');
            document.getElementById('rejection-reason').value = '';
        }

        function rejectSelected() {
            const data = getSelectedData();
            const rejectionReason = document.getElementById('rejection-reason').value.trim();

            console.log('REJECT SELECTED - Data being sent:', data);
            console.log('REJECT SELECTED - Rejection reason:', rejectionReason);

            if (!rejectionReason) {
                alert('Please provide a rejection reason.');
                return;
            }

            data.rejection_reason = rejectionReason;

            console.log('REJECT SELECTED - Sending to route:', '{{ route('registrar.grade-approval.reject-selected') }}');

            fetch('{{ route('registrar.grade-approval.reject-selected') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                console.log('REJECT SELECTED - Response received:', data);
                if (data.success) {
                    console.log('REJECT SELECTED - Success, reloading page');
                    window.location.href = '{{ route('registrar.grade-approval') }}';
                } else {
                    console.error('REJECT SELECTED - Server error:', data.message);
                    alert(data.message || 'Error rejecting selected classes.');
                }
            })
            .catch(error => {
                console.error('REJECT SELECTED - Network error:', error);
                alert('Error rejecting selected classes.');
            });
        }
    </script>
@endsection
