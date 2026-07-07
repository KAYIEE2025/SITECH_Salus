@extends('layouts.app')

@section('title', 'SSG Events')

@section('content')
    @if(session('success'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Events</h1>
            <p class="mt-1 text-sm text-gray-500">Create and manage SSG events.</p>
        </div>
        <a href="{{ route('ssg.events.create') }}"
            class="rounded-lg bg-green-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-green-900">
            + Create Event
        </a>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-left">Event Title</th>
                        <th class="px-6 py-3 text-left">Event Date</th>
                        <th class="px-6 py-3 text-left">Event Time</th>
                        <th class="px-6 py-3 text-left">Venue</th>
                        <th class="px-6 py-3 text-left">Fine Amount</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($events as $event)
                        <tr class="hover:bg-gray-50" data-event-id="{{ $event->id }}" data-event-date="{{ $event->event_date->format('Y-m-d') }}" data-scan-start="{{ $event->scan_start_time }}" data-scan-end="{{ $event->scan_end_time }}">
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $event->title }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $event->event_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $event->event_start_time ? \Carbon\Carbon::parse($event->event_start_time)->format('h:i A') : 'N/A' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $event->venue ?: 'N/A' }}</td>
                            <td class="px-6 py-4 text-gray-600">PHP {{ number_format($event->fine_amount, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="status-badge rounded-full bg-yellow-50 px-2.5 py-1 text-xs font-medium text-yellow-700">{{ $event->status }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col items-end gap-1">
                                    <div class="flex gap-2">
                                        <a href="{{ route('ssg.attendance.index', $event) }}" class="attendance-btn rounded-lg bg-green-800 px-3 py-1.5 text-xs text-white transition hover:bg-green-900">Scan Attendance</a>
                                        <a href="{{ route('ssg.events.show', $event) }}" class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs text-gray-700 transition hover:bg-gray-200">View</a>
                                        <a href="{{ route('ssg.events.edit', $event) }}" class="rounded-lg bg-green-50 px-3 py-1.5 text-xs text-green-700 transition hover:bg-green-100">Edit</a>
                                        <form method="POST" action="{{ route('ssg.events.destroy', $event) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                onclick="return confirm('Delete this event? Attendance records for this event will also be removed.')"
                                                class="rounded-lg bg-red-50 px-3 py-1.5 text-xs text-red-600 transition hover:bg-red-100">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                    <span class="countdown text-xs text-gray-500">--:--:--</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-400">No events yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($events->hasPages())
            <div class="border-t border-gray-100 px-6 py-4">{{ $events->links() }}</div>
        @endif
    </div>

    <script>
        const eventIds = @json($events->pluck('id'));
        const statusBaseUrl = @json(route('ssg.events.status', ['event' => 'PLACEHOLDER'])).replace('PLACEHOLDER', '');

        function updateEventStatus(eventId) {
            fetch(statusBaseUrl + eventId)
                .then(response => response.json())
                .then(data => {
                    const row = document.querySelector(`tr[data-event-id="${eventId}"]`);
                    if (!row) return;

                    const statusBadge = row.querySelector('.status-badge');
                    const attendanceBtn = row.querySelector('.attendance-btn');

                    if (statusBadge) {
                        statusBadge.textContent = data.status;
                        statusBadge.className = 'status-badge rounded-full px-2.5 py-1 text-xs font-medium';
                        if (data.status === 'Upcoming') {
                            statusBadge.classList.add('bg-yellow-50', 'text-yellow-700');
                        } else if (data.status === 'Ongoing') {
                            statusBadge.classList.add('bg-green-50', 'text-green-700');
                        } else {
                            statusBadge.classList.add('bg-gray-100', 'text-gray-600');
                        }
                    }

                    if (attendanceBtn) {
                        if (data.status === 'Ongoing') {
                            attendanceBtn.textContent = 'Scan Attendance';
                            attendanceBtn.className = 'attendance-btn rounded-lg bg-green-800 px-3 py-1.5 text-xs text-white transition hover:bg-green-900';
                        } else if (data.status === 'Upcoming') {
                            attendanceBtn.outerHTML = '<span class="attendance-btn cursor-not-allowed rounded-lg bg-gray-100 px-3 py-1.5 text-xs text-gray-400">Attendance</span>';
                        } else {
                            attendanceBtn.textContent = 'View Attendance';
                            attendanceBtn.className = 'attendance-btn rounded-lg bg-blue-50 px-3 py-1.5 text-xs text-blue-700 transition hover:bg-blue-100';
                        }
                    }
                })
                .catch(error => console.error('Error updating status:', error));
        }

        function updateCountdowns() {
            const rows = document.querySelectorAll('tr[data-event-id]');
            const now = new Date();

            rows.forEach(row => {
                const eventDate = row.dataset.eventDate;
                const scanStart = row.dataset.scanStart;
                const scanEnd = row.dataset.scanEnd;
                const countdownEl = row.querySelector('.countdown');

                if (!scanStart || !scanEnd || !countdownEl) {
                    countdownEl.textContent = '--:--:--';
                    return;
                }

                const startDateTime = new Date(eventDate + 'T' + scanStart);
                const endDateTime = new Date(eventDate + 'T' + scanEnd);

                if (now < startDateTime) {
                    const diff = startDateTime - now;
                    if (diff <= 0) {
                        countdownEl.textContent = 'Scanning starts in 00:00:00';
                    } else {
                        const hours = Math.floor(diff / (1000 * 60 * 60));
                        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                        countdownEl.textContent = `Scanning starts in ${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                    }
                } else if (now >= startDateTime && now <= endDateTime) {
                    const diff = endDateTime - now;
                    if (diff <= 0) {
                        countdownEl.textContent = 'Scanning ends in 00:00:00';
                    } else {
                        const hours = Math.floor(diff / (1000 * 60 * 60));
                        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                        countdownEl.textContent = `Scanning ends in ${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                    }
                } else {
                    countdownEl.textContent = 'Scanning Closed';
                }
            });
        }

        function pollEventStatuses() {
            eventIds.forEach(eventId => {
                updateEventStatus(eventId);
            });
        }

        // Initial poll and countdown update
        pollEventStatuses();
        updateCountdowns();

        // Poll status every 30 seconds
        setInterval(pollEventStatuses, 30000);

        // Update countdown every second
        setInterval(updateCountdowns, 1000);
    </script>
@endsection
