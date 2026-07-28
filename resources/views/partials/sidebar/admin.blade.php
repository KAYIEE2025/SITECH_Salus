<x-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')">
    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
    <span>Dashboard</span>
</x-nav-link>
<x-nav-link href="{{ route('admin.announcements.index') }}" :active="request()->routeIs('admin.announcements*')">
    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 5h14"></path><path d="M5 9h14"></path><path d="M5 13h9"></path><path d="M5 17h7"></path><path d="M15 17l2 2 4-4"></path></svg>
    <span>Announcements</span>
</x-nav-link>
<x-nav-link href="{{ route('admin.calendar.index') }}" :active="request()->routeIs('admin.calendar*')">
    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"></rect><path d="M16 2v4"></path><path d="M8 2v4"></path><path d="M3 10h18"></path></svg>
    <span>School Calendar</span>
</x-nav-link>
<x-nav-link href="{{ route('admin.reports.index') }}" :active="request()->routeIs('admin.reports*')">
    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><path d="M9 13h6"></path><path d="M9 17h6"></path></svg>
    <span>Reports</span>
</x-nav-link>
<x-nav-link href="{{ route('admin.activity-logs.index') }}" :active="request()->routeIs('admin.activity-logs*')">
    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
    <span>Activity Logs</span>
</x-nav-link>
