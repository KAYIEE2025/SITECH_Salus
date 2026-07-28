@php
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
@endphp

@if($currentSidebar)
    @include($currentSidebar)
@endif

@foreach($roles as $role)
    @if(isset($roleSidebarMap[$role]) && $role !== $currentRole)
        <div class="mt-6 border-t border-green-800 pt-4">
            <p class="mb-3 px-3 text-xs font-semibold text-green-200 uppercase tracking-wider">{{ $role }} Management</p>
            @include($roleSidebarMap[$role])
        </div>
    @endif
@endforeach

<div class="mt-6 border-t border-green-800 pt-4">
    <x-nav-link href="{{ route($profileRoute) }}" :active="request()->routeIs('*profile*')">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
        <span>My Profile</span>
    </x-nav-link>
</div>
