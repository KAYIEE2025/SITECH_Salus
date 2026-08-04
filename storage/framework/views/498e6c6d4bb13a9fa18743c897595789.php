<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['href', 'active' => false]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['href', 'active' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php ($isSuperAdminPanel = request()->routeIs('superadmin.*')); ?>
<?php ($isRegistrarPanel = request()->routeIs('registrar.*')); ?>
<?php ($isStudentPanel = request()->routeIs('student.*')); ?>
<?php ($isSsgPanel = request()->routeIs('ssg.*')); ?>
<?php ($isTeacherPanel = request()->routeIs('teacher.*')); ?>

<a href="<?php echo e($href); ?>"
    class="<?php echo e($isSuperAdminPanel || $isRegistrarPanel || $isStudentPanel || $isSsgPanel || $isTeacherPanel ? 'mb-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition' : 'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm mb-1 transition'); ?>

    <?php echo e($active
        ? (($isSuperAdminPanel || $isRegistrarPanel || $isStudentPanel || $isSsgPanel || $isTeacherPanel) ? 'bg-gradient-to-r from-[#f4d35e] to-[#d6ad22] font-semibold text-green-950 shadow-md shadow-green-950/10' : 'bg-yellow-500 font-semibold text-green-900')
        : 'text-green-300 hover:bg-green-800 hover:text-white'); ?>">
    <?php echo e($slot); ?>

</a>
<?php /**PATH C:\Users\Toshiba\sitech\resources\views/components/nav-link.blade.php ENDPATH**/ ?>