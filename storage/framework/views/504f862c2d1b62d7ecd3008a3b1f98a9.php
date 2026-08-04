<div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
            <tr>
                <th class="px-4 py-3 text-left md:px-6">Student Number</th>
                <th class="px-4 py-3 text-left md:px-6">Student Name</th>
                <th class="px-4 py-3 text-left md:px-6">Grade & Section</th>
                <th class="px-4 py-3 text-left md:px-6">Attendance Status</th>
                <th class="px-4 py-3 text-left md:px-6">Scan Time</th>
                <th class="px-4 py-3 text-left md:px-6">Fine Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php $__empty_1 = true; $__currentLoopData = $attendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-800 md:px-6 md:py-4"><?php echo e($attendance->student->student_number ?? 'N/A'); ?></td>
                    <td class="px-4 py-3 text-gray-700 md:px-6 md:py-4"><?php echo e($attendance->student->full_name ?? 'N/A'); ?></td>
                    <td class="px-4 py-3 text-gray-600 md:px-6 md:py-4">
                        <?php echo e($attendance->student?->yearLevel?->name ?? 'N/A'); ?>

                        <?php if($attendance->student?->section): ?>
                            - Section <?php echo e($attendance->student->section->name); ?>

                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 md:px-6 md:py-4">
                        <?php if($attendance->is_present): ?>
                            <span class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700">Present</span>
                        <?php else: ?>
                            <span class="rounded-full bg-red-50 px-2.5 py-1 text-xs font-medium text-red-700">Absent</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-gray-600 md:px-6 md:py-4">
                        <?php echo e($attendance->scanned_at ? $attendance->scanned_at->format('M d, Y h:i A') : 'Not scanned'); ?>

                    </td>
                    <td class="px-4 py-3 md:px-6 md:py-4">
                        <?php if($attendance->is_present): ?>
                            <span class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700">Waived</span>
                        <?php else: ?>
                            <span class="rounded-full bg-yellow-50 px-2.5 py-1 text-xs font-medium text-yellow-700"><?php echo e($attendance->payment_status ?? 'Unpaid'); ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-gray-400 md:px-6 md:py-8">No attendance records prepared for this event.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php /**PATH C:\Users\Toshiba\sitech\resources\views/ssg/attendance/_table.blade.php ENDPATH**/ ?>