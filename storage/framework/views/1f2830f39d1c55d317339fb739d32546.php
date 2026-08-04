<?php $__env->startSection('title', 'Create SSG Event'); ?>

<?php $__env->startSection('content'); ?>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Create Event</h1>
        <a href="<?php echo e(route('ssg.events.index')); ?>" class="text-sm text-gray-600 hover:text-gray-800">Back to Events</a>
    </div>

    <div class="sg-card p-6 sm:p-8">
        <form method="POST" action="<?php echo e(route('ssg.events.store')); ?>">
            <?php echo $__env->make('ssg.events._form', ['event' => $event], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Toshiba\sitech\resources\views/ssg/events/create.blade.php ENDPATH**/ ?>