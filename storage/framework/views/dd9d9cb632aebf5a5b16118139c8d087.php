<?php
    $user = auth()->user();
    $roles = $user->getRoleNames()->toArray();
    
    $routeSidebarMap = [
        'superadmin.*' => 'partials.sidebar.superadmin',
        'admin.*' => 'partials.sidebar.admin',
        'registrar.*' => 'partials.sidebar.registrar',
        'teacher.*' => 'partials.sidebar.teacher',
        'student.*' => 'partials.sidebar.student',
        'ssg.*' => 'partials.sidebar.ssg',
    ];
    
    $roleSidebarMap = [
        'Super Admin' => 'partials.sidebar.superadmin',
        'Admin' => 'partials.sidebar.admin',
        'Registrar' => 'partials.sidebar.registrar',
        'Teacher' => 'partials.sidebar.teacher',
        'Student' => 'partials.sidebar.student',
        'SSG' => 'partials.sidebar.ssg',
    ];
    
    $routeProfileMap = [
        'superadmin.*' => 'superadmin.profile',
        'admin.*' => 'admin.profile.index',
        'registrar.*' => 'registrar.profile',
        'teacher.*' => 'teacher.profile.index',
        'student.*' => 'student.profile.index',
        'ssg.*' => 'ssg.profile.index',
    ];
    
    $currentSidebar = null;
    foreach($routeSidebarMap as $routePattern => $sidebar) {
        if(request()->routeIs($routePattern)) {
            $currentSidebar = $sidebar;
            break;
        }
    }
    
    $currentRole = null;
    if($currentSidebar) {
        $currentRole = array_search($currentSidebar, $roleSidebarMap);
    }
    
    $profileRoute = 'student.profile.index';
    foreach($routeProfileMap as $routePattern => $route) {
        if(request()->routeIs($routePattern)) {
            $profileRoute = $route;
            break;
        }
    }
?>

<?php if($currentSidebar): ?>
    <?php echo $__env->make($currentSidebar, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?>

<?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if(isset($roleSidebarMap[$role]) && $role !== $currentRole): ?>
        <div class="mt-6 border-t border-green-800 pt-4">
            <p class="mb-3 px-3 text-xs font-semibold text-green-200 uppercase tracking-wider"><?php echo e($role); ?> Management</p>
            <?php echo $__env->make($roleSidebarMap[$role], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    <?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<div class="mt-6 border-t border-green-800 pt-4">
    <?php if (isset($component)) { $__componentOriginalc295f12dca9d42f28a259237a5724830 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc295f12dca9d42f28a259237a5724830 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.nav-link','data' => ['href' => ''.e(route($profileRoute)).'','active' => request()->routeIs('*profile*')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route($profileRoute)).'','active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->routeIs('*profile*'))]); ?>
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
        <span>My Profile</span>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc295f12dca9d42f28a259237a5724830)): ?>
<?php $attributes = $__attributesOriginalc295f12dca9d42f28a259237a5724830; ?>
<?php unset($__attributesOriginalc295f12dca9d42f28a259237a5724830); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc295f12dca9d42f28a259237a5724830)): ?>
<?php $component = $__componentOriginalc295f12dca9d42f28a259237a5724830; ?>
<?php unset($__componentOriginalc295f12dca9d42f28a259237a5724830); ?>
<?php endif; ?>
</div>
<?php /**PATH C:\Users\Toshiba\sitech\resources\views/components/dynamic-sidebar.blade.php ENDPATH**/ ?>