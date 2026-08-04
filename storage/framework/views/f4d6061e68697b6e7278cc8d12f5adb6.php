<?php $__env->startSection('title', 'Grade Reopening Requests'); ?>

<?php $__env->startSection('content'); ?>
    <div class="mb-4 md:mb-6">
        <h1 class="text-xl md:text-2xl font-semibold text-gray-800">Grade Reopening Requests</h1>
        <p class="text-xs md:text-sm text-gray-600 mt-1">Manage teacher requests to reopen grade submission periods.</p>
    </div>

    <div class="tc-card p-4 md:p-6">
        <div class="overflow-x-auto">
            <table class="w-full min-w-max text-xs md:text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left py-2 px-2 md:py-3 md:px-4 font-medium text-gray-600 text-[10px] md:text-xs">Teacher</th>
                        <th class="text-left py-2 px-2 md:py-3 md:px-4 font-medium text-gray-600 text-[10px] md:text-xs">School Year</th>
                        <th class="text-left py-2 px-2 md:py-3 md:px-4 font-medium text-gray-600 text-[10px] md:text-xs">Grading Period</th>
                        <th class="text-left py-2 px-2 md:py-3 md:px-4 font-medium text-gray-600 text-[10px] md:text-xs">Reason</th>
                        <th class="text-left py-2 px-2 md:py-3 md:px-4 font-medium text-gray-600 text-[10px] md:text-xs">Requested At</th>
                        <th class="text-left py-2 px-2 md:py-3 md:px-4 font-medium text-gray-600 text-[10px] md:text-xs">Status</th>
                        <th class="text-left py-2 px-2 md:py-3 md:px-4 font-medium text-gray-600 text-[10px] md:text-xs">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reopeningRequest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 px-2 md:py-3 md:px-4">
                                <div class="font-medium text-gray-800 text-xs md:text-sm"><?php echo e($reopeningRequest->teacher->name); ?></div>
                            </td>
                            <td class="py-2 px-2 md:py-3 md:px-4 text-gray-600"><?php echo e($reopeningRequest->school_year); ?></td>
                            <td class="py-2 px-2 md:py-3 md:px-4 text-gray-600">Term <?php echo e($reopeningRequest->grading_period); ?></td>
                            <td class="py-2 px-2 md:py-3 md:px-4 text-gray-600 max-w-xs md:max-w-sm truncate"><?php echo e($reopeningRequest->reason); ?></td>
                            <td class="py-2 px-2 md:py-3 md:px-4 text-gray-600"><?php echo e($reopeningRequest->requested_at ? $reopeningRequest->requested_at->format('M d, Y g:i A') : '-'); ?></td>
                            <td class="py-2 px-2 md:py-3 md:px-4">
                                <?php if($reopeningRequest->status === 'Pending'): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 md:px-2.5 md:py-0.5 rounded-full text-[10px] md:text-xs font-medium bg-yellow-100 text-yellow-800 whitespace-nowrap">
                                        <?php echo e($reopeningRequest->status); ?>

                                    </span>
                                <?php elseif($reopeningRequest->status === 'Approved'): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 md:px-2.5 md:py-0.5 rounded-full text-[10px] md:text-xs font-medium bg-green-100 text-green-800 whitespace-nowrap">
                                        <?php echo e($reopeningRequest->status); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2 py-0.5 md:px-2.5 md:py-0.5 rounded-full text-[10px] md:text-xs font-medium bg-red-100 text-red-800 whitespace-nowrap">
                                        <?php echo e($reopeningRequest->status); ?>

                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-2 px-2 md:py-3 md:px-4">
                                <div class="flex gap-1.5 md:gap-2">
                                    <a href="<?php echo e(route('registrar.grade-reopening-requests.show', $reopeningRequest)); ?>" 
                                       class="text-blue-600 hover:text-blue-800 text-xs md:text-sm font-medium whitespace-nowrap">
                                        View
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 md:py-8 text-gray-400 text-[10px] md:text-xs">
                                No reopening requests found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Toshiba\sitech\resources\views/registrar/grade-reopening-requests/index.blade.php ENDPATH**/ ?>