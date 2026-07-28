@props(['href', 'active' => false])
@php($isSuperAdminPanel = request()->routeIs('superadmin.*'))
@php($isRegistrarPanel = request()->routeIs('registrar.*'))
@php($isStudentPanel = request()->routeIs('student.*'))
@php($isSsgPanel = request()->routeIs('ssg.*'))
@php($isTeacherPanel = request()->routeIs('teacher.*'))

<a href="{{ $href }}"
    class="{{ $isSuperAdminPanel || $isRegistrarPanel || $isStudentPanel || $isSsgPanel || $isTeacherPanel ? 'mb-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition' : 'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm mb-1 transition' }}
    {{ $active
        ? (($isSuperAdminPanel || $isRegistrarPanel || $isStudentPanel || $isSsgPanel || $isTeacherPanel) ? 'bg-gradient-to-r from-[#f4d35e] to-[#d6ad22] font-semibold text-green-950 shadow-md shadow-green-950/10' : 'bg-yellow-500 font-semibold text-green-900')
        : 'text-green-300 hover:bg-green-800 hover:text-white' }}">
    {{ $slot }}
</a>
