<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/salus-logo.png')); ?>">
    <title>SITech - <?php echo $__env->yieldContent('title', $title ?? 'Dashboard'); ?></title>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<?php ($isSuperAdminPanel = request()->routeIs('superadmin.*')); ?>
<?php ($isRegistrarPanel = request()->routeIs('registrar.*')); ?>
<?php ($isStudentPanel = request()->routeIs('student.*')); ?>
<?php ($isSsgPanel = request()->routeIs('ssg.*')); ?>
<?php ($isTeacherPanel = request()->routeIs('teacher.*')); ?>

<body x-data="{ sidebarOpen: false }" class="min-h-screen <?php echo e($isSuperAdminPanel || $isRegistrarPanel || $isStudentPanel || $isSsgPanel || $isTeacherPanel ? 'bg-[#f5faf6]' : 'bg-gray-50'); ?>">
    <div x-cloak x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-30 bg-green-950/60 backdrop-blur-sm lg:hidden" @click="sidebarOpen = false" aria-hidden="true"></div>

    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" class="fixed inset-y-0 left-0 z-40 flex h-screen w-72 max-w-[88vw] transform flex-col transition-transform duration-300 ease-out lg:w-64 <?php echo e($isSuperAdminPanel || $isRegistrarPanel || $isStudentPanel || $isSsgPanel || $isTeacherPanel ? 'bg-gradient-to-b from-[#14532d] via-[#1a5c1a] to-[#064e3b] shadow-xl shadow-green-950/20' : 'bg-[#1a5c1a]'); ?>">
        <div class="flex items-center justify-between border-b px-5 py-5 sm:px-6 <?php echo e($isSuperAdminPanel || $isRegistrarPanel || $isStudentPanel || $isSsgPanel || $isTeacherPanel ? 'border-green-400/20' : 'border-green-600'); ?>">
            <div class="flex items-center gap-3">
                <img src="<?php echo e(asset('images/salus-logo.png')); ?>" alt="Salus Institute of Technology seal" class="h-20 w-20 rounded-full object-contain p-1">
                <div>
                    <p class="text-sm font-bold tracking-wide text-white">SITech</p>
                    <p class="text-xs text-green-200">Salus Institute of Technology</p>
                </div>
            </div>
            <button type="button" @click="sidebarOpen = false" class="rounded-lg p-2 text-green-100 transition hover:bg-green-950/30 hover:text-white lg:hidden" aria-label="Close navigation menu">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18" stroke-linecap="round" /></svg>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4">
            <?php if (isset($component)) { $__componentOriginaldc1bdb0fc0b85385016108acc5479b8c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldc1bdb0fc0b85385016108acc5479b8c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dynamic-sidebar','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-sidebar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldc1bdb0fc0b85385016108acc5479b8c)): ?>
<?php $attributes = $__attributesOriginaldc1bdb0fc0b85385016108acc5479b8c; ?>
<?php unset($__attributesOriginaldc1bdb0fc0b85385016108acc5479b8c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldc1bdb0fc0b85385016108acc5479b8c)): ?>
<?php $component = $__componentOriginaldc1bdb0fc0b85385016108acc5479b8c; ?>
<?php unset($__componentOriginaldc1bdb0fc0b85385016108acc5479b8c); ?>
<?php endif; ?>
        </nav>

        <div class="border-t px-4 py-4 <?php echo e($isSuperAdminPanel || $isRegistrarPanel || $isStudentPanel || $isSsgPanel || $isTeacherPanel ? 'border-green-300/20' : 'border-green-800'); ?>">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-[#f5d76e] to-[#c8a000] shadow-sm">
                    <span class="text-xs font-bold text-green-950">
                        <?php echo e(strtoupper(substr(auth()->user()->name, 0, 2))); ?>

                    </span>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-xs font-semibold text-white"><?php echo e(auth()->user()->name); ?></p>
                    <p class="text-xs text-green-200"><?php echo e(auth()->user()->getRoleNames()->implode(' | ')); ?></p>
                </div>
            </div>
            <form method="POST" action="<?php echo e(route('logout')); ?>" class="mt-3">
                <?php echo csrf_field(); ?>
                <button type="submit" class="w-full rounded-lg px-2 py-1.5 text-left text-xs text-green-200 transition hover:bg-green-950/30 hover:text-white">
                    Log out
                </button>
            </form>
        </div>
    </aside>

    <main class="min-w-0 min-h-screen lg:ml-64">
        <div class="sticky top-0 z-20 border-b bg-white <?php echo e($isSuperAdminPanel || $isRegistrarPanel || $isStudentPanel || $isSsgPanel || $isTeacherPanel ? 'border-green-100 px-4 py-4 sm:px-8 sm:py-5' : 'border-gray-200 px-4 py-4 sm:px-8'); ?>">
            <div class="flex items-center gap-3 sm:justify-between">
                <button type="button" @click="sidebarOpen = true" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-green-50 text-green-800 transition hover:bg-green-100 lg:hidden" aria-label="Open navigation menu">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round" /></svg>
                </button>
                <div class="min-w-0 flex-1">
                    <?php if($isSuperAdminPanel || $isRegistrarPanel || $isStudentPanel || $isSsgPanel || $isTeacherPanel): ?>
                        <p class="mb-1 truncate text-[10px] font-semibold uppercase tracking-[0.14em] text-green-700 sm:text-[11px] sm:tracking-[0.18em]"><?php echo e($isSuperAdminPanel ? 'System administration' : ($isRegistrarPanel ? 'Registrar workspace' : ($isStudentPanel ? 'Student portal' : ($isSsgPanel ? 'SSG workspace' : 'Teacher workspace')))); ?></p>
                    <?php endif; ?>
                    <h1 class="truncate <?php echo e($isSuperAdminPanel || $isRegistrarPanel || $isStudentPanel || $isSsgPanel || $isTeacherPanel ? 'text-lg font-bold text-green-950 sm:text-xl' : 'text-base font-semibold text-gray-800 sm:text-lg'); ?>"><?php echo $__env->yieldContent('title', $title ?? 'Dashboard'); ?></h1>
                </div>
                <?php if($isSuperAdminPanel || $isRegistrarPanel || $isStudentPanel || $isSsgPanel || $isTeacherPanel): ?>
                    <!-- <div class="hidden items-center gap-2 text-xs text-green-700 sm:flex">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        Control center online
                    </div> -->
                <?php endif; ?>
            </div>
        </div>

        <div class="app-content min-w-0 overflow-x-hidden <?php echo e($isSuperAdminPanel ? 'p-4 sm:p-8 superadmin-panel' : ($isRegistrarPanel ? 'p-4 sm:p-8 registrar-panel' : ($isStudentPanel ? 'p-4 sm:p-8 student-panel' : (($isSsgPanel || $isTeacherPanel) ? 'p-4 sm:p-8 ' . ($isSsgPanel ? 'ssg-panel' : 'teacher-panel') : 'p-4 sm:p-8')))); ?>">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </main>

    <?php echo $__env->yieldContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\Users\Toshiba\sitech\resources\views/layouts/app.blade.php ENDPATH**/ ?>