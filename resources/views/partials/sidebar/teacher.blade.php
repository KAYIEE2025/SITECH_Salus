<x-nav-link href="{{ route('teacher.dashboard') }}" :active="request()->routeIs('teacher.dashboard')">
    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
    <span>Dashboard</span>
</x-nav-link>
<x-nav-link href="{{ route('teacher.schedule.index') }}" :active="request()->routeIs('teacher.schedule*')">
    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"></rect><path d="M16 2v4"></path><path d="M8 2v4"></path><path d="M3 10h18"></path></svg>
    <span>My Schedule</span>
</x-nav-link>
<x-nav-link href="{{ route('teacher.classes.index') }}" :active="request()->routeIs('teacher.classes*')">
    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path><path d="M10 10h6"></path><path d="M10 14h6"></path></svg>
    <span>Class List</span>
</x-nav-link>
<x-nav-link href="{{ route('teacher.advisory-students.index') }}" :active="request()->routeIs('teacher.advisory-students*')">
    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
    <span>My Advisory Students</span>
</x-nav-link>
<x-nav-link href="{{ route('teacher.announcements.index') }}" :active="request()->routeIs('teacher.announcements*')">
    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 5h14"></path><path d="M5 9h14"></path><path d="M5 13h9"></path><path d="M5 17h7"></path><path d="M15 17l2 2 4-4"></path></svg>
    <span>Announcements</span>
</x-nav-link>
