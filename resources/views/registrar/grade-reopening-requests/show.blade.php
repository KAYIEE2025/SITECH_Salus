@extends('layouts.app')

@section('title', 'Grade Reopening Request Details')

@section('content')
    <div class="mb-6">
        <a href="{{ route('registrar.grade-reopening-requests.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-800">
            ← Back to Requests
        </a>
    </div>

    <div class="tc-card p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Reopening Request Details</h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-medium text-gray-800 mb-4">Request Information</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm text-gray-500">Teacher:</span>
                        <p class="font-medium text-gray-800">{{ $reopeningRequest->teacher->name }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">School Year:</span>
                        <p class="font-medium text-gray-800">{{ $reopeningRequest->school_year }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Grading Period:</span>
                        <p class="font-medium text-gray-800">Term {{ $reopeningRequest->grading_period }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Status:</span>
                        <p class="font-medium text-gray-800">
                            @if($reopeningRequest->status === 'Pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    {{ $reopeningRequest->status }}
                                </span>
                            @elseif($reopeningRequest->status === 'Approved')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $reopeningRequest->status }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    {{ $reopeningRequest->status }}
                                </span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Requested At:</span>
                        <p class="font-medium text-gray-800">{{ $reopeningRequest->requested_at ? $reopeningRequest->requested_at->format('F j, Y g:i A') : '-' }}</p>
                    </div>
                    @if($reopeningRequest->reviewed_at)
                        <div>
                            <span class="text-sm text-gray-500">Reviewed At:</span>
                            <p class="font-medium text-gray-800">{{ $reopeningRequest->reviewed_at->format('F j, Y g:i A') }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Reviewed By:</span>
                            <p class="font-medium text-gray-800">{{ $reopeningRequest->reviewer ? $reopeningRequest->reviewer->name : '-' }}</p>
                        </div>
                    @endif
                    @if($reopeningRequest->approved_at)
                        <div>
                            <span class="text-sm text-gray-500">Approved At:</span>
                            <p class="font-medium text-gray-800">{{ $reopeningRequest->approved_at->format('F j, Y g:i A') }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Approved By:</span>
                            <p class="font-medium text-gray-800">{{ $reopeningRequest->approver ? $reopeningRequest->approver->name : '-' }}</p>
                        </div>
                    @endif
                    @if($reopeningRequest->temporary_deadline)
                        <div>
                            <span class="text-sm text-gray-500">Temporary Deadline:</span>
                            <p class="font-medium text-gray-800">{{ $reopeningRequest->temporary_deadline->format('F j, Y g:i A') }}</p>
                        </div>
                    @endif
                    @if($reopeningRequest->remarks)
                        <div>
                            <span class="text-sm text-gray-500">Registrar Remarks:</span>
                            <p class="font-medium text-gray-800">{{ $reopeningRequest->remarks }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div>
                <h3 class="text-lg font-medium text-gray-800 mb-4">Reason</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-gray-700">{{ $reopeningRequest->reason }}</p>
                </div>

                @if($classSchedule)
                    <h3 class="text-lg font-medium text-gray-800 mb-4 mt-6">Class Information</h3>
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm text-gray-500">Subject:</span>
                            <p class="font-medium text-gray-800">{{ $classSchedule->subject->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Section:</span>
                            <p class="font-medium text-gray-800">{{ $classSchedule->section->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @if($reopeningRequest->status === 'Pending')
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Actions</h3>
                <div class="flex gap-3">
                    <button type="button" 
                            onclick="openApprovalModal()"
                            class="px-6 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                        Approve
                    </button>
                    <form action="{{ route('registrar.grade-reopening-requests.reject', $reopeningRequest) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                onclick="return confirm('Are you sure you want to reject this request?')"
                                class="px-6 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition">
                            Reject Request
                        </button>
                    </form>
                </div>
            </div>

            <!-- Approval Modal -->
            <div id="approvalModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Approve Grade Reopening Request</h3>
                    
                    <div class="space-y-3 mb-4">
                        <div>
                            <span class="text-sm text-gray-500">Teacher:</span>
                            <p class="font-medium text-gray-800">{{ $reopeningRequest->teacher->name }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">School Year:</span>
                            <p class="font-medium text-gray-800">{{ $reopeningRequest->school_year }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Grading Period:</span>
                            <p class="font-medium text-gray-800">Term {{ $reopeningRequest->grading_period }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Teacher Reason:</span>
                            <p class="font-medium text-gray-800">{{ $reopeningRequest->reason }}</p>
                        </div>
                    </div>

                    <form action="{{ route('registrar.grade-reopening-requests.approve', $reopeningRequest) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">New Submission Deadline</label>
                            <input type="datetime-local" name="new_deadline" required 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   min="{{ now()->format('Y-m-d\TH:i') }}">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Registrar Remarks (Optional)</label>
                            <textarea name="remarks" rows="3"
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Add any optional remarks..."></textarea>
                        </div>
                        <div class="flex gap-3 justify-end">
                            <button type="button" 
                                    onclick="closeApprovalModal()"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-400 transition">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                                Approve & Reopen
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                function openApprovalModal() {
                    document.getElementById('approvalModal').classList.remove('hidden');
                    document.getElementById('approvalModal').classList.add('flex');
                }

                function closeApprovalModal() {
                    document.getElementById('approvalModal').classList.add('hidden');
                    document.getElementById('approvalModal').classList.remove('flex');
                }
            </script>
        @endif
    </div>
@endsection
