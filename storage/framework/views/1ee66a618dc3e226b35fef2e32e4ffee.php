<?php $__env->startSection('title', 'Super Admin Dashboard'); ?>

<?php $__env->startSection('content'); ?>
    <div class="mb-6 md:mb-8 overflow-hidden rounded-2xl bg-gradient-to-br from-[#14532d] via-[#1a5c1a] to-[#0f766e] p-4 md:p-6 text-white shadow-lg shadow-green-900/10 sm:p-8">
        <div class="flex flex-col justify-between gap-4 md:gap-6 sm:flex-row sm:items-end">
            <div class="min-w-0">
                <p class="text-xs md:text-sm font-medium text-green-100">Welcome back, <?php echo e(auth()->user()->name); ?></p>
                <h2 class="mt-1 md:mt-2 text-lg md:text-2xl font-bold tracking-tight sm:text-3xl">Your system at a glance.</h2>
                <p class="mt-1 md:mt-2 max-w-lg text-xs md:text-sm leading-5 md:leading-6 text-green-100">Monitor accounts, access roles, student onboarding, and important activity from one place.</p>
            </div>
            <div class="rounded-xl border border-white/20 bg-white/10 px-3 py-2 md:px-4 md:py-3 text-xs md:text-sm backdrop-blur-sm shrink-0">
                <p class="text-[10px] md:text-xs text-green-100">Access level</p>
                <p class="mt-1 font-semibold text-xs md:text-sm">Super Administrator</p>
            </div>
        </div>
    </div>

    <div class="mb-6 md:mb-8 grid grid-cols-1 gap-3 md:gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <div class="sa-card p-4 md:p-5">
            <div class="flex items-center justify-between"><p class="text-xs md:text-sm text-gray-500">Total Accounts</p><span class="rounded-lg bg-green-50 p-1.5 md:p-2 text-green-700 text-xs md:text-sm">◎</span></div>
            <p class="mt-3 md:mt-4 text-2xl md:text-3xl font-bold text-green-900"><?php echo e($totalUsers); ?></p>
            <p class="mt-1 text-[10px] md:text-xs text-gray-400">All registered users</p>
        </div>
        <div class="sa-card p-4 md:p-5">
            <div class="flex items-center justify-between"><p class="text-xs md:text-sm text-gray-500">Active Accounts</p><span class="rounded-lg bg-emerald-50 p-1.5 md:p-2 text-emerald-700 text-xs md:text-sm">✓</span></div>
            <p class="mt-3 md:mt-4 text-2xl md:text-3xl font-bold text-green-900"><?php echo e($activeUsers); ?></p>
            <p class="mt-1 text-[10px] md:text-xs text-gray-400">Currently enabled</p>
        </div>
        <div class="sa-card p-4 md:p-5">
            <div class="flex items-center justify-between"><p class="text-xs md:text-sm text-gray-500">Inactive Accounts</p><span class="rounded-lg bg-red-50 p-1.5 md:p-2 text-red-600 text-xs md:text-sm">!</span></div>
            <p class="mt-3 md:mt-4 text-2xl md:text-3xl font-bold text-red-600"><?php echo e($inactiveUsers); ?></p>
            <p class="mt-1 text-[10px] md:text-xs text-gray-400">Review when necessary</p>
        </div>
        <div class="sa-card p-4 md:p-5">
            <div class="flex items-center justify-between"><p class="text-xs md:text-sm text-gray-500">Students</p><span class="rounded-lg bg-amber-50 p-1.5 md:p-2 text-amber-700 text-xs md:text-sm">◉</span></div>
            <p class="mt-3 md:mt-4 text-2xl md:text-3xl font-bold text-amber-700"><?php echo e($totalStudents); ?></p>
            <p class="mt-1 text-[10px] md:text-xs text-gray-400">Student records</p>
        </div>
        <a href="<?php echo e(route('superadmin.student-accounts.index')); ?>" class="rounded-2xl border border-yellow-200 bg-gradient-to-br from-yellow-50 to-amber-100 p-4 md:p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex items-center justify-between"><p class="text-xs md:text-sm text-yellow-700">Pending Accounts</p><span class="rounded-lg bg-white/70 p-1.5 md:p-2 text-yellow-700 text-xs md:text-sm">→</span></div>
            <p class="mt-3 md:mt-4 text-2xl md:text-3xl font-bold text-yellow-800"><?php echo e($pendingStudentAccounts); ?></p>
            <p class="mt-1 text-[10px] md:text-xs text-yellow-700">Needs your attention</p>
        </a>
    </div>

    <div class="mb-6 md:mb-8 grid grid-cols-1 gap-3 md:gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="sa-card p-4 md:p-5 flex flex-col items-center justify-center text-center"><p class="text-xs md:text-sm text-gray-500">Admins</p><p class="mt-2 text-xl md:text-2xl font-bold text-gray-800"><?php echo e($totalAdmins); ?></p></div>
        <div class="sa-card p-4 md:p-5 flex flex-col items-center justify-center text-center"><p class="text-xs md:text-sm text-gray-500">Registrars</p><p class="mt-2 text-xl md:text-2xl font-bold text-gray-800"><?php echo e($totalRegistrars); ?></p></div>
        <div class="sa-card p-4 md:p-5 flex flex-col items-center justify-center text-center"><p class="text-xs md:text-sm text-gray-500">Teachers</p><p class="mt-2 text-xl md:text-2xl font-bold text-gray-800"><?php echo e($totalTeachers); ?></p></div>
        <div class="sa-card p-4 md:p-5 flex flex-col items-center justify-center text-center"><p class="text-xs md:text-sm text-gray-500">SSG Officers</p><p class="mt-2 text-xl md:text-2xl font-bold text-gray-800"><?php echo e($totalSSG); ?></p><p class="mt-1 text-[10px] md:text-xs text-gray-400">Manage via <a href="<?php echo e(route('superadmin.role-management')); ?>" class="font-semibold text-green-700 hover:underline">Role Management</a></p></div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:gap-6 xl:grid-cols-3">
        <div class="sa-card p-4 md:p-6">
            <div class="flex items-center justify-between"><h2 class="text-sm md:text-base font-semibold text-gray-800">Quick Actions</h2><span class="text-[10px] md:text-xs font-semibold uppercase tracking-wider text-green-700">Shortcuts</span></div>
            <div class="mt-3 md:mt-4 space-y-2 md:space-y-3">
                <a href="<?php echo e(route('superadmin.accounts')); ?>" class="block rounded-xl bg-gradient-to-r from-[#1a5c1a] to-[#0f766e] px-4 py-3 md:py-3 text-xs md:text-sm font-semibold text-white transition hover:shadow-md min-h-[48px] flex items-center justify-center">Create or Manage Accounts</a>
                <a href="<?php echo e(route('superadmin.student-accounts.index')); ?>" class="block rounded-xl bg-yellow-50 px-4 py-3 md:py-3 text-xs md:text-sm font-semibold text-yellow-800 transition hover:bg-yellow-100 min-h-[48px] flex items-center justify-center">Generate Student Accounts</a>
                <a href="<?php echo e(route('superadmin.activity-logs')); ?>" class="block rounded-xl bg-green-50 px-4 py-3 md:py-3 text-xs md:text-sm font-semibold text-green-900 transition hover:bg-green-100 min-h-[48px] flex items-center justify-center">View System Activity</a>
                <a href="<?php echo e(route('superadmin.roles')); ?>" class="block rounded-xl bg-green-50 px-4 py-3 md:py-3 text-xs md:text-sm font-semibold text-green-900 transition hover:bg-green-100 min-h-[48px] flex items-center justify-center">Review Roles &amp; Access</a>
            </div>
        </div>

        <div class="sa-card xl:col-span-2">
            <div class="sa-card-header">
                <h2 class="text-sm md:text-base font-semibold text-gray-800">Recent System Activity</h2>
                <p class="mt-1 text-[10px] md:text-xs text-gray-500">The latest changes recorded in your system.</p>
            </div>

            <div class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $recentLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex items-start justify-between gap-3 md:gap-4 px-4 py-3 md:px-6 md:py-4">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs md:text-sm font-medium text-gray-800 break-words"><?php echo e($log->description); ?></p>
                            <p class="mt-1 text-[10px] md:text-xs text-gray-500 break-words"><?php echo e(optional($log->causer)->name ?? 'System'); ?> <span class="text-green-600">•</span> <?php echo e($log->event ?? 'activity'); ?></p>
                        </div>
                        <p class="shrink-0 text-[10px] md:text-xs text-gray-400"><?php echo e($log->created_at->diffForHumans()); ?></p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="px-4 py-6 md:px-6 md:py-8 text-center text-xs md:text-sm text-gray-400">No activity logs yet.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Toshiba\sitech\resources\views/superadmin/dashboard.blade.php ENDPATH**/ ?>