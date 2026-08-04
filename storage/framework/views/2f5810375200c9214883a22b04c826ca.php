<?php $__env->startSection('title', 'Import Summary Preview'); ?>

<?php $__env->startSection('content'); ?>
    <div class="mb-6">
        <a href="<?php echo e(route('teacher.classes.grades', $classSchedule)); ?>" class="text-sm font-medium text-[#1a5c1a] hover:text-green-900">
            Back to Manage Grades
        </a>
    </div>

    <!-- Class Information Card -->
    <div class="tc-card mb-6 p-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3 flex-1">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">School Year</p>
                    <p class="mt-1 text-sm font-medium text-gray-800"><?php echo e($classSchedule->school_year ?? 'N/A'); ?></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Section</p>
                    <p class="mt-1 text-sm font-medium text-gray-800"><?php echo e($classSchedule->section->name ?? 'N/A'); ?></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Subject</p>
                    <p class="mt-1 text-sm font-medium text-gray-800"><?php echo e($classSchedule->subject->name ?? 'N/A'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Summary Card -->
    <div class="tc-card mb-6 p-6">
        <div class="mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Import Summary Preview</h2>
            <p class="mt-1 text-sm text-gray-500">Review the parsed data before importing grades for Quarter <?php echo e($gradingPeriod); ?>.</p>
        </div>

        <!-- Summary Statistics -->
        <div class="mb-6 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="rounded-lg bg-blue-50 border border-blue-200 px-4 py-3 text-center min-w-0">
                <p class="text-xs md:text-sm text-blue-600 uppercase tracking-wide break-words">Total Parsed</p>
                <p class="mt-2 text-2xl font-bold text-blue-700"><?php echo e($totalParsed); ?></p>
            </div>
            <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-center min-w-0">
                <p class="text-xs md:text-sm text-green-600 uppercase tracking-wide break-words">Matched</p>
                <p class="mt-2 text-2xl font-bold text-green-700"><?php echo e($matchedCount); ?></p>
            </div>
            <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-center min-w-0">
                <p class="text-xs md:text-sm text-red-600 uppercase tracking-wide break-words">Unmatched</p>
                <p class="mt-2 text-2xl font-bold text-red-700"><?php echo e($unmatchedCount); ?></p>
            </div>
            <div class="rounded-lg bg-yellow-50 border border-yellow-200 px-4 py-3 text-center min-w-0">
                <p class="text-xs md:text-sm text-yellow-600 uppercase tracking-wide break-words">Ambiguous</p>
                <p class="mt-2 text-2xl font-bold text-yellow-700"><?php echo e($ambiguousCount ?? 0); ?></p>
            </div>
        </div>

        <!-- Data Table -->
        <div class="overflow-x-auto border border-gray-200 rounded-lg">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Student Name (Excel)</th>
                        <th class="px-4 py-3 text-left">Matched Student</th>
                        <th class="px-4 py-3 text-left">Quarter <?php echo e($gradingPeriod); ?> Grade</th>
                        <th class="px-4 py-3 text-left">Remarks</th>
                        <th class="px-4 py-3 text-left">Match Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__currentLoopData = $displayData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="<?php echo e($row['match_status'] === 'unmatched' ? 'bg-red-50' : ($row['match_status'] === 'ambiguous' ? 'bg-yellow-50' : 'hover:bg-gray-50')); ?>">
                            <td class="px-4 py-3 font-medium text-gray-800">
                                <?php echo e($row['student_name']); ?>

                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                <?php if($row['match_status'] === 'ambiguous'): ?>
                                    <div class="text-xs">
                                        <p class="font-medium text-yellow-700">Multiple matches:</p>
                                        <?php $__currentLoopData = $row['ambiguous_matches']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $match): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <p class="text-gray-600"><?php echo e($match['student_name']); ?></p>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php else: ?>
                                    <?php echo e($row['matched_student'] ?? 'Not matched'); ?>

                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-gray-700">
                                <?php echo e($row['quarter_grade'] ?? 'N/A'); ?>

                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                <?php echo e($row['remarks'] ?? 'N/A'); ?>

                            </td>
                            <td class="px-4 py-3">
                                <?php if($row['match_status'] === 'matched'): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                        Matched
                                    </span>
                                <?php elseif($row['match_status'] === 'ambiguous'): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                        Ambiguous
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                        Unmatched
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <!-- Action Buttons -->
        <div class="mt-6 flex justify-end gap-3">
            <form action="<?php echo e(route('teacher.classes.cancel-import-summary', $classSchedule)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </button>
            </form>
            <form action="<?php echo e(route('teacher.classes.confirm-import-summary', $classSchedule)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-green-700 rounded-lg hover:bg-green-800 transition">
                    Import Grades
                </button>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Toshiba\sitech\resources\views/teacher/classes/import-summary-preview.blade.php ENDPATH**/ ?>