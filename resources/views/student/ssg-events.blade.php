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

        <!-- Filters -->
        <div class="st-card mb-6 p-6">
            <form action="{{ route('student.ssg-events.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">School Year</label>
                    <input type="text" 
                           name="school_year" 
                           value="{{ $filters['school_year'] }}" 
                           placeholder="e.g., 2024-2025"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Attendance Status</label>
                    <select name="attendance_status" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                        <option value="">All</option>
                        <option value="present" {{ $filters['attendance_status'] === 'present' ? 'selected' : '' }}>Present</option>
                        <option value="absent" {{ $filters['attendance_status'] === 'absent' ? 'selected' : '' }}>Absent</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                    <select name="payment_status" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                        <option value="">All</option>
                        <option value="paid" {{ $filters['payment_status'] === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="unpaid" {{ $filters['payment_status'] === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search Event</label>
                    <input type="text" 
                           name="search" 
                           value="{{ $filters['search'] }}" 
                           placeholder="Search by event name..."
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                </div>
                <div class="md:col-span-4 flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-green-700 text-white text-sm rounded hover:bg-green-800 transition">
                        Apply Filters
                    </button>
                    <a href="{{ route('student.ssg-events.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded hover:bg-gray-300 transition">
                        Clear Filters
                    </a>
                </div>
            </form>
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
                                <th class="text-center py-3 px-4 font-medium text-gray-600">Payment Status</th>
                                <th class="text-center py-3 px-4 font-medium text-gray-600">Actions</th>
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
                                    <td class="py-3 px-4 text-center">
                                        @if($attendance->payment_status === 'paid')
                                            <span class="px-2 py-1 rounded-full text-xs bg-green-50 text-green-700 font-medium">Paid</span>
                                        @else
                                            <span class="px-2 py-1 rounded-full text-xs bg-orange-50 text-orange-600 font-medium">Unpaid</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <button onclick="viewEvent({{ $attendance->id }})" 
                                                class="px-3 py-1 bg-green-700 text-white text-xs rounded hover:bg-green-800 transition">
                                            View
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Event Details Modal -->
        <div id="eventModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white rounded-xl max-w-md w-full mx-4 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 id="modalTitle" class="text-lg font-semibold text-gray-800"></h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-500">Description</p>
                        <p id="modalDescription" class="text-sm text-gray-800"></p>
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <p class="text-sm text-gray-500">Event Date</p>
                            <p id="modalEventDate" class="text-sm text-gray-800"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Venue</p>
                            <p id="modalVenue" class="text-sm text-gray-800"></p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <p class="text-sm text-gray-500">Attendance Status</p>
                            <p id="modalAttendance" class="text-sm font-medium"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Date Scanned</p>
                            <p id="modalScannedAt" class="text-sm text-gray-800"></p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <p class="text-sm text-gray-500">Fine Amount</p>
                            <p id="modalFineAmount" class="text-sm text-gray-800"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Payment Status</p>
                            <p id="modalPaymentStatus" class="text-sm font-medium"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            const attendancesData = @json($attendances);

            function viewEvent(attendanceId) {
                const attendance = attendancesData.find(a => a.id === attendanceId);
                if (!attendance) return;

                document.getElementById('modalTitle').textContent = attendance.ssg_event.title || 'N/A';
                document.getElementById('modalDescription').textContent = attendance.ssg_event.description || 'No description available.';
                document.getElementById('modalEventDate').textContent = attendance.ssg_event.event_date ? new Date(attendance.ssg_event.event_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : 'N/A';
                document.getElementById('modalVenue').textContent = attendance.ssg_event.venue || 'N/A';
                
                const attendanceStatus = attendance.is_present ? 'Present' : 'Absent';
                document.getElementById('modalAttendance').textContent = attendanceStatus;
                document.getElementById('modalAttendance').className = attendance.is_present ? 'text-sm font-medium text-green-700' : 'text-sm font-medium text-red-600';
                
                document.getElementById('modalScannedAt').textContent = attendance.scanned_at ? new Date(attendance.scanned_at).toLocaleString() : 'N/A';
                document.getElementById('modalFineAmount').textContent = '₱' + (attendance.actual_fine || 0).toFixed(2);
                
                const paymentStatus = attendance.payment_status === 'paid' ? 'Paid' : 'Unpaid';
                document.getElementById('modalPaymentStatus').textContent = paymentStatus;
                document.getElementById('modalPaymentStatus').className = attendance.payment_status === 'paid' ? 'text-sm font-medium text-green-700' : 'text-sm font-medium text-orange-600';

                document.getElementById('eventModal').classList.remove('hidden');
                document.getElementById('eventModal').classList.add('flex');
            }

            function closeModal() {
                document.getElementById('eventModal').classList.add('hidden');
                document.getElementById('eventModal').classList.remove('flex');
            }

            // Close modal on outside click
            document.getElementById('eventModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });
        </script>
    @endif
@endsection
