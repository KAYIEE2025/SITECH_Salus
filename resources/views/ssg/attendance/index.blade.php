@extends('layouts.app')

@section('title', 'Event Attendance')

@section('content')
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-gray-800">Event Attendance</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $event->title }}</p>
        </div>
        <a href="{{ route('ssg.events.index') }}" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-200 min-h-[48px] flex items-center justify-center">
            Back to Events
        </a>
    </div>

    <div class="grid gap-6 lg:gap-8 grid-cols-1 lg:grid-cols-[360px_1fr] xl:grid-cols-[360px_1fr]">
        <div class="space-y-6 lg:space-y-6 w-full xl:w-auto">
            <section class="sg-card p-4 md:p-6">
                <h2 class="mb-4 text-base font-semibold text-gray-800 md:text-lg">Event Information</h2>
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

            <section class="sg-card p-4 md:p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-800 md:text-lg">QR Scanner</h2>
                    <div id="attendance-status-badge">
                        @if($event->status === 'Ongoing')
                            <span class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700">Active</span>
                        @else
                            <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">View only</span>
                        @endif
                    </div>
                </div>

                <div class="mb-4 rounded-lg bg-gray-50 p-3 md:p-4">
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
                            <button type="button" id="extend-time-btn" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 min-h-[48px]">
                                Extend Attendance Time
                            </button>
                        </div>
                    @endif
                </div>

                @if($event->status === 'Ongoing')
                    <div id="reader" class="overflow-hidden rounded-lg border border-gray-200 bg-gray-50 w-full aspect-square md:aspect-auto"></div>
                    <p class="mt-3 text-xs text-gray-500">Camera starts automatically. Keep the student QR code inside the scanner frame. For old QR codes, ensure good lighting and hold steady.</p>

                    <div class="mt-4 space-y-3">
                        <p class="mb-2 text-xs font-semibold text-gray-600">Manual Entry (Last 6 Digits):</p>
                        <div class="flex flex-col md:flex-row gap-2">
                            <input type="text" id="manual-qr-input" placeholder="Enter last 6 digits of School ID" maxlength="6" pattern="\d{6}"
                                class="flex-1 rounded-lg border border-gray-300 px-3 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-green-600 min-h-[48px]">
                            <button type="button" id="manual-scan-btn" class="rounded-lg bg-green-800 px-4 py-3 text-sm font-medium text-white transition hover:bg-green-900 min-h-[48px]">
                                Submit
                            </button>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="button" id="restart-scanner-btn" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 min-h-[48px]">
                            Restart Camera Scanner
                        </button>
                    </div>
                @else
                    <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-10 text-center text-sm text-gray-500">
                        Scanning is available only while the event status is Ongoing.
                    </div>
                @endif

                <div id="scan-alert" class="mt-4 hidden rounded-lg px-4 py-3 text-sm"></div>
                <div id="scan-student" class="mt-3 hidden rounded-lg border border-green-200 bg-green-50 px-4 py-4">
                    <div class="flex items-start gap-4">
                        <img id="student-photo" src="" alt="Student Photo" class="h-14 w-14 md:h-16 md:w-16 rounded-full border-2 border-green-300 object-cover">
                        <div class="flex-1">
                            <p class="text-xs font-semibold uppercase tracking-wide text-green-700">Student</p>
                            <p id="student-name" class="mt-1 font-semibold text-green-900 text-sm md:text-base"></p>
                            <p id="student-number" class="mt-1 text-sm text-green-700"></p>
                            <p class="mt-2 text-xs text-green-600">Scanned at: <span id="scan-time"></span></p>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <section class="sg-card overflow-hidden">
            <div class="border-b border-gray-100 px-4 py-4 md:px-6">
                <h2 class="text-base font-semibold text-gray-800 md:text-lg">Attendance List</h2>
                <p class="mt-0.5 text-xs text-gray-500">Refreshes automatically every 5 seconds.</p>
            </div>
            <div id="attendance-table">
                @include('ssg.attendance._table', ['attendances' => $attendances])
            </div>
        </section>
    </div>

    <!-- Extend Time Modal -->
    <div id="extend-time-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-md rounded-xl bg-white p-4 md:p-6">
            <h3 class="mb-4 text-lg font-semibold text-gray-800 md:text-xl">Extend Attendance Time</h3>
            <form id="extend-time-form">
                @csrf
                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium text-gray-700">New End Time</label>
                    <input type="time" name="scan_end_time" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-green-600 min-h-[48px]">
                </div>
                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Reason (Optional)</label>
                    <textarea name="reason" rows="3"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600"></textarea>
                </div>
                <div class="flex flex-col md:flex-row justify-end gap-3">
                    <button type="button" id="cancel-extend-time" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-200 min-h-[48px]">
                        Cancel
                    </button>
                    <button type="submit" class="rounded-lg bg-green-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-green-900 min-h-[48px]">
                        Extend Time
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Student Selection Modal -->
    <div id="student-selection-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-md rounded-xl bg-white p-4 md:p-6">
            <h3 class="mb-4 text-lg font-semibold text-gray-800 md:text-xl">Select Student</h3>
            <p class="mb-4 text-sm text-gray-600">Multiple students found with the same last 6 digits. Please select the correct student:</p>
            <div id="student-list" class="mb-4 max-h-64 space-y-2 overflow-y-auto">
                <!-- Student options will be populated here -->
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" id="cancel-student-selection" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-200 min-h-[48px]">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    @if($event->status === 'Ongoing')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
    @endif

    <script>
        const tableUrl = @json(route('ssg.attendance.list', $event));
        const scanUrl = @json(route('ssg.attendance.scan'));
        const manualEntryUrl = @json(route('ssg.attendance.manual-entry'));
        const manualEntryFullUrl = @json(route('ssg.attendance.manual-entry-full'));
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

            console.log('SSG Scanner - Submitting:', {
                qrValue: qrValue,
                eventId: eventId,
                timestamp: new Date().toISOString()
            });

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
                            fps: 20, // Higher frame rate for better detection
                            qrbox: { width: 250, height: 250 }, // Smaller scan area for mobile
                            aspectRatio: 1.0,
                            videoConstraints: {
                                facingMode: 'environment', // Use back camera on mobile
                                width: { ideal: 1280 },
                                height: { ideal: 720 }
                            }
                        },
                        decodedText => {
                            console.log('QR Code detected:', decodedText);
                            submitScan(decodedText);
                        },
                        (errorMessage) => {
                            // Suppress frequent error messages during scanning
                        }
                    ).catch((error) => {
                        console.error('Camera start error:', error);
                        showScanAlert(false, 'Unable to start the camera.');
                    });
                })
                .catch((error) => {
                    console.error('Camera permission error:', error);
                    showScanAlert(false, 'Camera permission is required to scan QR codes.');
                });
        }

        function stopScanner() {
            if (scanner) {
                scanner.stop().then(() => {
                    scanner.clear();
                }).catch(err => {
                    console.error("Error stopping scanner", err);
                });
                scanner = null;
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

        // Manual QR entry
        const manualQrInput = document.getElementById('manual-qr-input');
        const manualScanBtn = document.getElementById('manual-scan-btn');
        const restartScannerBtn = document.getElementById('restart-scanner-btn');
        const studentSelectionModal = document.getElementById('student-selection-modal');
        const studentList = document.getElementById('student-list');
        const cancelStudentSelection = document.getElementById('cancel-student-selection');

        if (manualScanBtn && manualQrInput) {
            // Only allow numbers in the input
            manualQrInput.addEventListener('input', (e) => {
                e.target.value = e.target.value.replace(/\D/g, '').slice(0, 6);
            });

            manualScanBtn.addEventListener('click', () => {
                const last6Digits = manualQrInput.value.trim();
                
                // Validate that exactly 6 digits are entered
                if (!last6Digits) {
                    showScanAlert(false, 'Please enter the last 6 digits of the School ID.');
                    return;
                }
                
                if (!/^\d{6}$/.test(last6Digits)) {
                    showScanAlert(false, 'Please enter exactly 6 digits.');
                    return;
                }
                
                console.log('Manual entry with last 6 digits:', last6Digits);
                submitManualEntry(last6Digits);
                manualQrInput.value = '';
            });

            // Allow Enter key to submit
            manualQrInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    manualScanBtn.click();
                }
            });
        }

        // Student selection modal handlers
        if (cancelStudentSelection) {
            cancelStudentSelection.addEventListener('click', () => {
                studentSelectionModal.classList.add('hidden');
                studentSelectionModal.classList.remove('flex');
                studentList.innerHTML = '';
                scanLocked = false;
            });
        }

        function submitManualEntry(last6Digits) {
            if (scanLocked) {
                return;
            }

            scanLocked = true;

            fetch(manualEntryUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    event_id: eventId,
                    last_6_digits: last6Digits,
                }),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.requires_selection) {
                        // Show student selection modal
                        showStudentSelectionModal(data.students);
                    } else {
                        showScanAlert(data.success, data.message || (data.success ? 'Attendance Recorded' : 'Entry failed.'));

                        if (data.success) {
                            playSuccessSound();
                            showScannedStudent(data);
                            refreshAttendanceTable();
                        } else {
                            playErrorSound();
                        }
                    }
                })
                .catch(() => {
                    showScanAlert(false, 'Unable to process manual entry.');
                    playErrorSound();
                })
                .finally(() => {
                    if (!studentSelectionModal.classList.contains('hidden')) {
                        // Don't unlock if modal is shown
                        return;
                    }
                    
                    setTimeout(() => {
                        document.getElementById('scan-student').classList.add('hidden');
                        document.getElementById('scan-alert').classList.add('hidden');
                        scanLocked = false;
                    }, 3000);
                });
        }

        function showStudentSelectionModal(students) {
            studentList.innerHTML = '';
            
            students.forEach(student => {
                const studentOption = document.createElement('div');
                studentOption.className = 'rounded-lg border border-gray-200 p-3 hover:bg-gray-50 cursor-pointer transition';
                studentOption.innerHTML = `
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-800">${student.full_name}</p>
                            <p class="text-sm text-gray-600">ID: ${student.student_number}</p>
                            <p class="text-xs text-gray-500">${student.year_level} - Section ${student.section}</p>
                        </div>
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                `;
                
                studentOption.addEventListener('click', () => {
                    submitManualEntryWithFullId(student.id);
                });
                
                studentList.appendChild(studentOption);
            });
            
            studentSelectionModal.classList.remove('hidden');
            studentSelectionModal.classList.add('flex');
        }

        function submitManualEntryWithFullId(studentId) {
            // Hide modal
            studentSelectionModal.classList.add('hidden');
            studentSelectionModal.classList.remove('flex');
            studentList.innerHTML = '';
            
            fetch(manualEntryFullUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    event_id: eventId,
                    student_id: studentId,
                }),
            })
                .then(response => response.json())
                .then(data => {
                    showScanAlert(data.success, data.message || (data.success ? 'Attendance Recorded' : 'Entry failed.'));

                    if (data.success) {
                        playSuccessSound();
                        showScannedStudent(data);
                        refreshAttendanceTable();
                    } else {
                        playErrorSound();
                    }
                })
                .catch(() => {
                    showScanAlert(false, 'Unable to process manual entry.');
                    playErrorSound();
                })
                .finally(() => {
                    setTimeout(() => {
                        document.getElementById('scan-student').classList.add('hidden');
                        document.getElementById('scan-alert').classList.add('hidden');
                        scanLocked = false;
                    }, 3000);
                });
        }

        // Restart scanner button
        if (restartScannerBtn) {
            restartScannerBtn.addEventListener('click', () => {
                stopScanner();
                setTimeout(() => {
                    startScanner();
                }, 500);
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
