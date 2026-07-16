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
