<?php $__env->startSection('title', 'SSG Fine Records'); ?>

<?php $__env->startSection('content'); ?>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Fine Records</h1>
        <p class="mt-1 text-sm text-gray-500">Computed from SSG event attendance records.</p>
    </div>

    <div class="sg-card mb-6 p-6">
        <form method="GET" action="<?php echo e(route('ssg.fines.index')); ?>" class="grid gap-4 lg:grid-cols-5 lg:items-end">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">School Year</label>
                <select name="school_year" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    <option value="">All school years</option>
                    <?php $__currentLoopData = $schoolYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schoolYear): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($schoolYear); ?>" <?php if(($filters['school_year'] ?? '') === $schoolYear): echo 'selected'; endif; ?>><?php echo e($schoolYear); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Grade Level</label>
                <select name="year_level_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    <option value="">All grade levels</option>
                    <?php $__currentLoopData = $yearLevels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yearLevel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($yearLevel->id); ?>" <?php if((string) ($filters['year_level_id'] ?? '') === (string) $yearLevel->id): echo 'selected'; endif; ?>><?php echo e($yearLevel->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Section</label>
                <select name="section_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    <option value="">All sections</option>
                    <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($section->id); ?>" <?php if((string) ($filters['section_id'] ?? '') === (string) $section->id): echo 'selected'; endif; ?>>
                            <?php echo e($section->yearLevel->name ?? 'Grade'); ?> - Section <?php echo e($section->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Search Student</label>
                <input type="text" name="search" value="<?php echo e($filters['search'] ?? ''); ?>"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
                    placeholder="Name or student number">
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 rounded-lg bg-green-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-green-900">
                    Apply
                </button>
                <a href="<?php echo e(route('ssg.fines.index')); ?>" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-200">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="sg-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-left">Student Number</th>
                        <th class="px-6 py-3 text-left">Student Name</th>
                        <th class="px-6 py-3 text-left">Grade Level</th>
                        <th class="px-6 py-3 text-left">Section</th>
                        <th class="px-6 py-3 text-left">Events Attended</th>
                        <th class="px-6 py-3 text-left">Events Absent</th>
                        <th class="px-6 py-3 text-left">Total Fine Balance</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-800"><?php echo e($student->student_number); ?></td>
                            <td class="px-6 py-4 text-gray-700"><?php echo e($student->full_name); ?></td>
                            <td class="px-6 py-4 text-gray-600"><?php echo e($student->yearLevel->name ?? 'N/A'); ?></td>
                            <td class="px-6 py-4 text-gray-600"><?php echo e($student->section ? 'Section ' . $student->section->name : 'N/A'); ?></td>
                            <td class="px-6 py-4 text-gray-600"><?php echo e($student->events_attended_count); ?></td>
                            <td class="px-6 py-4 text-gray-600"><?php echo e($student->events_absent_count); ?></td>
                            <td class="px-6 py-4 font-semibold text-gray-800">PHP <?php echo e(number_format($student->total_fine_balance ?? 0, 2)); ?></td>
                            <td class="px-6 py-4 text-right">
                                <a href="<?php echo e(route('ssg.fines.show', $student)); ?>"
                                    class="rounded-lg bg-green-50 px-3 py-1.5 text-xs font-medium text-green-700 transition hover:bg-green-100">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-400">No students found for the selected filters.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($students->hasPages()): ?>
            <div class="border-t border-gray-100 px-6 py-4"><?php echo e($students->links()); ?></div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Toshiba\sitech\resources\views/ssg/fines/index.blade.php ENDPATH**/ ?>