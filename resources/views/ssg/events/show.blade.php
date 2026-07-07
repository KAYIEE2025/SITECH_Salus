@extends('layouts.app')

@section('title', 'View SSG Event')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">{{ $event->title }}</h1>
        <div class="flex gap-2">
            @if($event->status === 'Ongoing' && $event->scan_end_time)
                <button type="button" id="extend-time-btn" class="rounded-lg bg-blue-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-900">Extend Time</button>
            @endif
            <a href="{{ route('ssg.events.edit', $event) }}" class="rounded-lg bg-green-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-green-900">Edit</a>
            <a href="{{ route('ssg.events.index') }}" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-200">Back</a>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-6 md:col-span-2">
            <h2 class="mb-4 text-base font-semibold text-gray-800">Event Details</h2>
            <dl class="grid gap-4 text-sm md:grid-cols-2">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Event Date</dt>
                    <dd class="mt-1 text-gray-800">{{ $event->event_date->format('F d, Y') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Event Start Time</dt>
                    <dd class="mt-1 text-gray-800">{{ $event->event_start_time ? \Carbon\Carbon::parse($event->event_start_time)->format('h:i A') : 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Event End Time</dt>
                    <dd class="mt-1 text-gray-800">{{ $event->event_end_time ? \Carbon\Carbon::parse($event->event_end_time)->format('h:i A') : 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Venue</dt>
                    <dd class="mt-1 text-gray-800">{{ $event->venue ?: 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Fine Amount</dt>
                    <dd class="mt-1 text-gray-800">PHP {{ number_format($event->fine_amount, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Status</dt>
                    <dd class="mt-1">
                        <span id="status-badge" class="rounded-full px-2.5 py-1 text-xs font-medium">
                            @if($event->status === 'Upcoming')
                                <span class="bg-yellow-50 text-yellow-700">{{ $event->status }}</span>
                            @elseif($event->status === 'Ongoing')
                                <span class="bg-green-50 text-green-700">{{ $event->status }}</span>
                            @else
                                <span class="bg-gray-100 text-gray-600">{{ $event->status }}</span>
                            @endif
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Countdown</dt>
                    <dd class="mt-1">
                        <span id="countdown" class="text-sm font-medium text-gray-700">--:--:--</span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Created By</dt>
                    <dd class="mt-1 text-gray-800">{{ $event->creator->name ?? 'System' }}</dd>
                </div>
            </dl>
            <div class="mt-6">
                <h3 class="mb-2 text-sm font-semibold text-gray-800">Description</h3>
                <p class="whitespace-pre-line text-sm text-gray-600">{{ $event->description ?: 'No description provided.' }}</p>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <p class="text-sm text-gray-500">Prepared Attendance Records</p>
            <p class="mt-2 text-3xl font-bold text-green-800">{{ $event->attendances_count }}</p>
        </div>
    </div>

    <!-- Extend Time Modal -->
    <div id="extend-time-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
        <div class="w-full max-w-md rounded-xl bg-white p-6">
            <h3 class="mb-4 text-lg font-semibold text-gray-800">Extend Scan Time</h3>
            <form id="extend-time-form">
                @csrf
                <div class="mb-4">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Quick Add</label>
                    <div class="grid grid-cols-5 gap-2">
                        <button type="button" class="extend-preset rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100" data-minutes="5">+5m</button>
                        <button type="button" class="extend-preset rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100" data-minutes="10">+10m</button>
                        <button type="button" class="extend-preset rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100" data-minutes="15">+15m</button>
                        <button type="button" class="extend-preset rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100" data-minutes="30">+30m</button>
                        <button type="button" class="extend-preset rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100" data-minutes="60">+1h</button>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Or Set Custom End Time</label>
                    <input type="time" name="scan_end_time" id="custom-end-time"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                </div>
                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Reason (Optional)</label>
                    <textarea name="reason" rows="3"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600"></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" id="cancel-extend-time" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-200">Cancel</button>
                    <button type="submit" class="rounded-lg bg-blue-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-900">Extend Time</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const eventId = @json($event->id);
        const statusUrl = @json(route('ssg.events.status', $event));
        const extendTimeUrl = @json(route('ssg.attendance.extend-time', $event));
        const csrfToken = @json(csrf_token());
        const currentScanEndTime = @json($event->scan_end_time);
        const eventDate = @json($event->event_date ? $event->event_date->format('Y-m-d') : null);
        const scanStartTime = @json($event->scan_start_time);
        const scanEndTime = @json($event->scan_end_time);

        function updateEventStatus() {
            fetch(statusUrl)
                .then(response => response.json())
                .then(data => {
                    const statusBadge = document.getElementById('status-badge');
                    const extendTimeBtn = document.getElementById('extend-time-btn');

                    if (statusBadge) {
                        statusBadge.innerHTML = `<span class="${getStatusClass(data.status)}">${data.status}</span>`;
                    }

                    if (extendTimeBtn) {
                        if (data.status === 'Ongoing' && currentScanEndTime) {
                            extendTimeBtn.classList.remove('hidden');
                        } else {
                            extendTimeBtn.classList.add('hidden');
                        }
                    }
                })
                .catch(error => console.error('Error updating status:', error));
        }

        function updateCountdown() {
            const countdownEl = document.getElementById('countdown');
            if (!countdownEl || !eventDate || !scanStartTime || !scanEndTime) {
                if (countdownEl) countdownEl.textContent = '--:--:--';
                return;
            }

            const now = new Date();
            const startDateTime = new Date(eventDate + 'T' + scanStartTime);
            const endDateTime = new Date(eventDate + 'T' + scanEndTime);

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
        }

        function getStatusClass(status) {
            if (status === 'Upcoming') return 'bg-yellow-50 text-yellow-700';
            if (status === 'Ongoing') return 'bg-green-50 text-green-700';
            return 'bg-gray-100 text-gray-600';
        }

        document.getElementById('extend-time-btn').addEventListener('click', () => {
            document.getElementById('extend-time-modal').classList.remove('hidden');
            document.getElementById('extend-time-modal').classList.add('flex');
        });

        document.getElementById('cancel-extend-time').addEventListener('click', () => {
            document.getElementById('extend-time-modal').classList.add('hidden');
            document.getElementById('extend-time-modal').classList.remove('flex');
        });

        document.querySelectorAll('.extend-preset').forEach(btn => {
            btn.addEventListener('click', () => {
                const minutes = parseInt(btn.dataset.minutes);
                const currentEndTime = new Date('2000-01-01 ' + currentScanEndTime);
                currentEndTime.setMinutes(currentEndTime.getMinutes() + minutes);
                const newTime = currentEndTime.toTimeString().slice(0, 5);
                document.getElementById('custom-end-time').value = newTime;
            });
        });

        document.getElementById('extend-time-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);

            fetch(extendTimeUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData,
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('extend-time-modal').classList.add('hidden');
                        document.getElementById('extend-time-modal').classList.remove('flex');
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message || 'Failed to extend time.');
                    }
                })
                .catch(() => {
                    alert('Unable to extend scan time.');
                });
        });

        // Initial poll and countdown update
        updateEventStatus();
        updateCountdown();

        // Poll status every 30 seconds
        setInterval(updateEventStatus, 30000);

        // Update countdown every second
        setInterval(updateCountdown, 1000);
    </script>
@endsection
