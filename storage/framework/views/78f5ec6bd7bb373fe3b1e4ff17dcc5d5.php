<?php $__env->startSection('title', 'SSG Events'); ?>

<?php $__env->startSection('content'); ?>
    <?php $__sessionArgs = ['success'];
if (session()->has($__sessionArgs[0])) :
if (isset($value)) { $__sessionPrevious[] = $value; }
$value = session()->get($__sessionArgs[0]); ?>
        <div class="mb-4 md:mb-6 rounded-lg border border-green-200 bg-green-50 px-3 py-2 md:px-4 md:py-3 text-xs md:text-sm text-green-800">
            <?php echo e($value); ?>

        </div>
    <?php unset($value);
if (isset($__sessionPrevious) && !empty($__sessionPrevious)) { $value = array_pop($__sessionPrevious); }
if (isset($__sessionPrevious) && empty($__sessionPrevious)) { unset($__sessionPrevious); }
endif;
unset($__sessionArgs); ?>

    <div class="mb-4 md:mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-3 md:gap-0">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-gray-800">Events</h1>
            <p class="mt-1 text-xs md:text-sm text-gray-500">Create and manage SSG events.</p>
        </div>
        <a href="<?php echo e(route('ssg.events.create')); ?>"
            class="rounded-lg bg-green-800 px-3 py-1.5 md:px-4 md:py-2 text-xs md:text-sm font-medium text-white transition hover:bg-green-900 whitespace-nowrap">
            + Create Event
        </a>
    </div>

    <div class="sg-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-max text-xs md:text-sm">
                <thead class="bg-gray-50 text-[10px] md:text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-2 py-2 md:px-6 md:py-3 text-left">Event Title</th>
                        <th class="px-2 py-2 md:px-6 md:py-3 text-left">Event Date</th>
                        <th class="px-2 py-2 md:px-6 md:py-3 text-left">Event Time</th>
                        <th class="px-2 py-2 md:px-6 md:py-3 text-left">Venue</th>
                        <th class="px-2 py-2 md:px-6 md:py-3 text-left">Fine Amount</th>
                        <th class="px-2 py-2 md:px-6 md:py-3 text-left">Status</th>
                        <th class="px-2 py-2 md:px-6 md:py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50" data-event-id="<?php echo e($event->id); ?>" data-event-date="<?php echo e($event->event_date->format('Y-m-d')); ?>" data-scan-start="<?php echo e($event->scan_start_time); ?>" data-scan-end="<?php echo e($event->scan_end_time); ?>">
                            <td class="px-2 py-2 md:px-6 md:py-4 font-medium text-gray-800"><?php echo e($event->title); ?></td>
                            <td class="px-2 py-2 md:px-6 md:py-4 text-gray-600"><?php echo e($event->event_date->format('M d, Y')); ?></td>
                            <td class="px-2 py-2 md:px-6 md:py-4 text-gray-600"><?php echo e($event->event_start_time ? \Carbon\Carbon::parse($event->event_start_time)->format('h:i A') : 'N/A'); ?></td>
                            <td class="px-2 py-2 md:px-6 md:py-4 text-gray-600"><?php echo e($event->venue ?: 'N/A'); ?></td>
                            <td class="px-2 py-2 md:px-6 md:py-4 text-gray-600">PHP <?php echo e(number_format($event->fine_amount, 2)); ?></td>
                            <td class="px-2 py-2 md:px-6 md:py-4">
                                <span class="status-badge rounded-full bg-yellow-50 px-2 py-0.5 md:px-2.5 md:py-1 text-[10px] md:text-xs font-medium text-yellow-700 whitespace-nowrap"><?php echo e($event->status); ?></span>
                            </td>
                            <td class="px-2 py-2 md:px-6 md:py-4">
                                <div class="flex flex-col items-end gap-1">
                                    <div class="flex gap-1.5 md:gap-2 flex-wrap">
                                        <a href="<?php echo e(route('ssg.attendance.index', $event)); ?>" class="attendance-btn rounded-lg bg-green-800 px-2 py-1 md:px-3 md:py-1.5 text-[10px] md:text-xs text-white transition hover:bg-green-900 whitespace-nowrap">Scan Attendance</a>
                                        <a href="<?php echo e(route('ssg.events.show', $event)); ?>" class="rounded-lg bg-gray-100 px-2 py-1 md:px-3 md:py-1.5 text-[10px] md:text-xs text-gray-700 transition hover:bg-gray-200 whitespace-nowrap">View</a>
                                        <a href="<?php echo e(route('ssg.events.edit', $event)); ?>" class="rounded-lg bg-green-50 px-2 py-1 md:px-3 md:py-1.5 text-[10px] md:text-xs text-green-700 transition hover:bg-green-100 whitespace-nowrap">Edit</a>
                                        <form method="POST" action="<?php echo e(route('ssg.events.destroy', $event)); ?>">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit"
                                                onclick="return confirm('Delete this event? Attendance records for this event will also be removed.')"
                                                class="rounded-lg bg-red-50 px-2 py-1 md:px-3 md:py-1.5 text-[10px] md:text-xs text-red-600 transition hover:bg-red-100 whitespace-nowrap">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                    <span class="countdown text-[10px] md:text-xs text-gray-500">--:--:--</span>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-2 py-4 md:px-6 md:py-8 text-center text-gray-400 text-[10px] md:text-xs">No events yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($events->hasPages()): ?>
            <div class="border-t border-gray-100 px-4 py-3 md:px-6 md:py-4"><?php echo e($events->links()); ?></div>
        <?php endif; ?>
    </div>

    <script>
        const eventIds = <?php echo json_encode($events->pluck('id'), 15, 512) ?>;
        const statusBaseUrl = <?php echo json_encode(route('ssg.events.status', ['event' => '__EVENT_ID__']), 512) ?>;

        function updateEventStatus(eventId) {
            fetch(statusBaseUrl.replace('__EVENT_ID__', eventId))
                .then(response => response.json())
                .then(data => {
                    const row = document.querySelector(`tr[data-event-id="${eventId}"]`);
                    if (!row) return;

                    const statusBadge = row.querySelector('.status-badge');
                    const attendanceBtn = row.querySelector('.attendance-btn');

                    if (statusBadge) {
                        statusBadge.textContent = data.status;
                        statusBadge.className = 'status-badge rounded-full px-2 py-0.5 md:px-2.5 md:py-1 text-[10px] md:text-xs font-medium whitespace-nowrap';
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
                            attendanceBtn.className = 'attendance-btn rounded-lg bg-green-800 px-2 py-1 md:px-3 md:py-1.5 text-[10px] md:text-xs text-white transition hover:bg-green-900 whitespace-nowrap';
                        } else if (data.status === 'Upcoming') {
                            attendanceBtn.outerHTML = '<span class="attendance-btn cursor-not-allowed rounded-lg bg-gray-100 px-2 py-1 md:px-3 md:py-1.5 text-[10px] md:text-xs text-gray-400 whitespace-nowrap">Attendance</span>';
                        } else {
                            attendanceBtn.textContent = 'View Attendance';
                            attendanceBtn.className = 'attendance-btn rounded-lg bg-blue-50 px-2 py-1 md:px-3 md:py-1.5 text-[10px] md:text-xs text-blue-700 transition hover:bg-blue-100 whitespace-nowrap';
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Toshiba\sitech\resources\views/ssg/events/index.blade.php ENDPATH**/ ?>