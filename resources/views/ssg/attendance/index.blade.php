@extends('layouts.app')

@section('title', 'Event Attendance')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Event Attendance</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $event->title }}</p>
        </div>
        <a href="{{ route('ssg.events.index') }}" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-200">
            Back to Events
        </a>
    </div>

    <div class="grid gap-6 xl:grid-cols-[360px_1fr]">
        <div class="space-y-6">
            <section class="rounded-xl border border-gray-200 bg-white p-6">
                <h2 class="mb-4 text-base font-semibold text-gray-800">Event Information</h2>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Date</dt>
                        <dd class="mt-1 text-gray-800">{{ $event->event_date->format('F d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Time</dt>
                        <dd class="mt-1 text-gray-800">{{ $event->event_time ? \Carbon\Carbon::parse($event->event_time)->format('h:i A') : 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Venue</dt>
                        <dd class="mt-1 text-gray-800">{{ $event->venue ?: 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700">{{ $event->status }}</span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Attendance</dt>
                        <dd class="mt-1 text-gray-800">{{ $event->present_count }} / {{ $event->attendances_count }} present</dd>
                    </div>
                </dl>
            </section>

            <section class="rounded-xl border border-gray-200 bg-white p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-800">QR Scanner</h2>
                    <div id="attendance-status-badge">
                        @if($event->status === 'Ongoing')
                            <span class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700">Active</span>
                        @else
                            <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">View only</span>
                        @endif
                    </div>
                </div>

                <div class="mb-4 rounded-lg bg-gray-50 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Attendance Status</p>
                            <p id="attendance-status" class="mt-1 text-sm font-medium text-gray-800">Checking...</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Time Remaining</p>
                            <p id="countdown" class="mt-1 text-sm font-medium text-gray-800">--:--:--</p>
                        </div>
                    </div>
                    @if($event->scan_start_time && $event->scan_end_time)
                        <div class="mt-3 text-xs text-gray-500">
                            <span>Start: {{ \Carbon\Carbon::parse($event->scan_start_time)->format('h:i A') }}</span>
                            <span class="mx-2">|</span>
                            <span>End: <span id="display-scan-end-time">{{ \Carbon\Carbon::parse($event->scan_end_time)->format('h:i A') }}</span></span>
                        </div>
                    @endif
                    @if($event->scan_end_time)
                        <div class="mt-3">
                            <button type="button" id="extend-time-btn" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                                Extend Attendance Time
                            </button>
                        </div>
                    @endif
                </div>

                @if($event->status === 'Ongoing')
                    <div id="reader" class="overflow-hidden rounded-lg border border-gray-200 bg-gray-50"></div>
                    <p class="mt-3 text-xs text-gray-500">Camera starts automatically. Keep the student QR code inside the scanner frame.</p>
                @else
                    <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-10 text-center text-sm text-gray-500">
                        Scanning is available only while the event status is Ongoing.
                    </div>
                @endif

                <div id="scan-alert" class="mt-4 hidden rounded-lg px-4 py-3 text-sm"></div>
                <div id="scan-student" class="mt-3 hidden rounded-lg border border-green-200 bg-green-50 px-4 py-4">
                    <div class="flex items-start gap-4">
                        <img id="student-photo" src="" alt="Student Photo" class="h-16 w-16 rounded-full border-2 border-green-300 object-cover">
                        <div class="flex-1">
                            <p class="text-xs font-semibold uppercase tracking-wide text-green-700">Student</p>
                            <p id="student-name" class="mt-1 font-semibold text-green-900"></p>
                            <p id="student-number" class="mt-1 text-sm text-green-700"></p>
                            <p class="mt-2 text-xs text-green-600">Scanned at: <span id="scan-time"></span></p>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <section class="overflow-hidden rounded-xl border border-gray-200 bg-white">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="text-base font-semibold text-gray-800">Attendance List</h2>
                <p class="mt-0.5 text-xs text-gray-500">Refreshes automatically every 5 seconds.</p>
            </div>
            <div id="attendance-table">
                @include('ssg.attendance._table', ['attendances' => $attendances])
            </div>
        </section>
    </div>

    <!-- Extend Time Modal -->
    <div id="extend-time-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
        <div class="w-full max-w-md rounded-xl bg-white p-6">
            <h3 class="mb-4 text-lg font-semibold text-gray-800">Extend Attendance Time</h3>
            <form id="extend-time-form">
                @csrf
                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium text-gray-700">New End Time</label>
                    <input type="time" name="scan_end_time" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                </div>
                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Reason (Optional)</label>
                    <textarea name="reason" rows="3"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600"></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" id="cancel-extend-time" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-200">
                        Cancel
                    </button>
                    <button type="submit" class="rounded-lg bg-green-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-green-900">
                        Extend Time
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($event->status === 'Ongoing')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
    @endif

    <script>
        const tableUrl = @json(route('ssg.attendance.list', $event));
        const scanUrl = @json(route('ssg.attendance.scan'));
        const extendTimeUrl = @json(route('ssg.attendance.extend-time', $event));
        const csrfToken = @json(csrf_token());
        const eventId = @json($event->id);
        const isOngoing = @json($event->status === 'Ongoing');
        const scanStartTime = @json($event->scan_start_time);
        let scanEndTime = @json($event->scan_end_time);
        const eventDate = @json($event->event_date ? $event->event_date->format('Y-m-d') : null);
        let scanLocked = false;
        let scanner = null;
        let countdownInterval = null;

        function showScanAlert(success, message) {
            const alert = document.getElementById('scan-alert');
            alert.textContent = message;
            alert.className = success
                ? 'mt-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800'
                : 'mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700';
            alert.classList.remove('hidden');
        }

        function showScannedStudent(data) {
            const panel = document.getElementById('scan-student');
            document.getElementById('student-name').textContent = data.student_name;
            document.getElementById('student-number').textContent = data.student_number;
            document.getElementById('student-photo').src = data.student_photo || '/images/default-avatar.png';
            document.getElementById('scan-time').textContent = data.scan_time;
            panel.classList.remove('hidden');
        }

        function playSuccessSound() {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) {
                return;
            }

            const context = new AudioContext();
            const oscillator = context.createOscillator();
            const gain = context.createGain();

            oscillator.type = 'sine';
            oscillator.frequency.setValueAtTime(880, context.currentTime);
            gain.gain.setValueAtTime(0.12, context.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, context.currentTime + 0.2);

            oscillator.connect(gain);
            gain.connect(context.destination);
            oscillator.start();
            oscillator.stop(context.currentTime + 0.2);
        }

        function playErrorSound() {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) {
                return;
            }

            const context = new AudioContext();
            const oscillator = context.createOscillator();
            const gain = context.createGain();

            oscillator.type = 'sawtooth';
            oscillator.frequency.setValueAtTime(200, context.currentTime);
            gain.gain.setValueAtTime(0.1, context.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, context.currentTime + 0.3);

            oscillator.connect(gain);
            gain.connect(context.destination);
            oscillator.start();
            oscillator.stop(context.currentTime + 0.3);
        }

        function refreshAttendanceTable() {
            fetch(tableUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
                .then(response => response.text())
                .then(html => {
                    document.getElementById('attendance-table').innerHTML = html;
                });
        }

        function submitScan(qrValue) {
            if (scanLocked) {
                return;
            }

            scanLocked = true;

            fetch(scanUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    event_id: eventId,
                    qr_value: qrValue,
                }),
            })
                .then(response => response.json())
                .then(data => {
                    showScanAlert(data.success, data.message || (data.success ? 'Attendance Recorded' : 'Scan failed.'));

                    if (data.success) {
                        playSuccessSound();
                        showScannedStudent(data);
                        refreshAttendanceTable();
                    } else {
                        // Play error sound for failed scans
                        playErrorSound();
                    }
                })
                .catch(() => {
                    showScanAlert(false, 'Unable to process QR scan.');
                    playErrorSound();
                })
                .finally(() => {
                    // Auto-hide student display and return to scanner after 3 seconds
                    setTimeout(() => {
                        document.getElementById('scan-student').classList.add('hidden');
                        document.getElementById('scan-alert').classList.add('hidden');
                        scanLocked = false;
                    }, 3000);
                });
        }

        function getAttendanceStatus() {
            if (!scanStartTime && !scanEndTime) {
                return { status: 'Open', canScan: isOngoing };
            }

            const now = new Date();
            const startDateTime = new Date(eventDate + 'T' + scanStartTime);
            const endDateTime = new Date(eventDate + 'T' + scanEndTime);

            if (now < startDateTime) {
                return { status: 'Upcoming', canScan: false };
            } else if (now >= startDateTime && now <= endDateTime) {
                return { status: 'Open', canScan: isOngoing };
            } else {
                return { status: 'Closed', canScan: false };
            }
        }

        function updateAttendanceStatus() {
            const statusInfo = getAttendanceStatus();
            const statusElement = document.getElementById('attendance-status');
            const badgeElement = document.getElementById('attendance-status-badge');
            const readerElement = document.getElementById('reader');

            statusElement.textContent = statusInfo.status;

            if (statusInfo.status === 'Upcoming') {
                badgeElement.innerHTML = '<span class="rounded-full bg-yellow-50 px-2.5 py-1 text-xs font-medium text-yellow-700">Upcoming</span>';
            } else if (statusInfo.status === 'Open') {
                badgeElement.innerHTML = '<span class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700">Open</span>';
            } else if (statusInfo.status === 'Closed') {
                badgeElement.innerHTML = '<span class="rounded-full bg-red-50 px-2.5 py-1 text-xs font-medium text-red-700">Closed</span>';
            }

            return statusInfo.canScan;
        }

        function updateCountdown() {
            if (!scanEndTime) {
                document.getElementById('countdown').textContent = '--:--:--';
                return;
            }

            const now = new Date();
            const endDateTime = new Date(eventDate + 'T' + scanEndTime);
            const diff = endDateTime - now;

            if (diff <= 0) {
                document.getElementById('countdown').textContent = '00:00:00';
                updateAttendanceStatus();
                return;
            }

            const hours = Math.floor(diff / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);

            document.getElementById('countdown').textContent =
                String(hours).padStart(2, '0') + ':' +
                String(minutes).padStart(2, '0') + ':' +
                String(seconds).padStart(2, '0');
        }

        function startScanner() {
            if (!isOngoing) return;

            const canScan = updateAttendanceStatus();
            if (!canScan) return;

            scanner = new Html5Qrcode('reader');

            Html5Qrcode.getCameras()
                .then(cameras => {
                    if (!cameras.length) {
                        showScanAlert(false, 'No camera found.');
                        return;
                    }

                    const cameraId = cameras[0].id;
                    scanner.start(
                        cameraId,
                        {
                            fps: 10,
                            qrbox: { width: 250, height: 250 },
                        },
                        decodedText => submitScan(decodedText)
                    ).catch(() => {
                        showScanAlert(false, 'Unable to start the camera.');
                    });
                })
                .catch(() => {
                    showScanAlert(false, 'Camera permission is required to scan QR codes.');
                });
        }

        function stopScanner() {
            if (scanner) {
                scanner.stop().then(() => {
                    scanner.clear();
                }).catch(() => {});
                scanner = null;
            }
            // Clear the reader element to allow re-initialization
            const readerElement = document.getElementById('reader');
            if (readerElement) {
                readerElement.innerHTML = '';
            }
        }

        function checkAndControlScanner() {
            const canScan = updateAttendanceStatus();

            if (canScan && !scanner) {
                startScanner();
            } else if (!canScan && scanner) {
                stopScanner();
            }
        }

        // Extend Time Modal
        const extendTimeBtn = document.getElementById('extend-time-btn');
        const extendTimeModal = document.getElementById('extend-time-modal');
        const cancelExtendTimeBtn = document.getElementById('cancel-extend-time');
        const extendTimeForm = document.getElementById('extend-time-form');

        if (extendTimeBtn) {
            extendTimeBtn.addEventListener('click', () => {
                extendTimeModal.classList.remove('hidden');
                extendTimeModal.classList.add('flex');
            });
        }

        if (cancelExtendTimeBtn) {
            cancelExtendTimeBtn.addEventListener('click', () => {
                extendTimeModal.classList.add('hidden');
                extendTimeModal.classList.remove('flex');
            });
        }

        if (extendTimeForm) {
            extendTimeForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(extendTimeForm);

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
                            extendTimeModal.classList.add('hidden');
                            extendTimeModal.classList.remove('flex');
                            showScanAlert(true, data.message);

                            // Update the scan_end_time variable
                            if (data.new_scan_end_time) {
                                scanEndTime = data.new_scan_end_time;

                                // Update the displayed end time
                                const endTimeDisplay = document.getElementById('display-scan-end-time');
                                if (endTimeDisplay) {
                                    const [hours, minutes] = data.new_scan_end_time.split(':');
                                    const hour = parseInt(hours);
                                    const ampm = hour >= 12 ? 'PM' : 'AM';
                                    const displayHour = hour % 12 || 12;
                                    endTimeDisplay.textContent = `${displayHour}:${minutes} ${ampm}`;
                                }

                                // Update countdown immediately
                                updateCountdown();

                                // Update attendance status
                                updateAttendanceStatus();

                                // Re-enable scanner if within new time window
                                checkAndControlScanner();
                            }

                            // Hide alert after 3 seconds
                            setTimeout(() => {
                                document.getElementById('scan-alert').classList.add('hidden');
                            }, 3000);
                        } else {
                            showScanAlert(false, data.message || 'Failed to extend time.');
                        }
                    })
                    .catch(() => {
                        showScanAlert(false, 'Unable to extend attendance time.');
                    });
            });
        }

        // Initialize
        window.addEventListener('load', () => {
            updateAttendanceStatus();
            updateCountdown();
            checkAndControlScanner();

            // Update countdown every second
            countdownInterval = setInterval(() => {
                updateCountdown();
                checkAndControlScanner();
            }, 1000);
        });

        window.setInterval(refreshAttendanceTable, 5000);
    </script>
@endsection
