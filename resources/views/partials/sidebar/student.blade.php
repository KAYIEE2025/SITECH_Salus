<x-nav-link href="{{ route('student.dashboard') }}" :active="request()->routeIs('student.dashboard')">
    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
    <span>Dashboard</span>
</x-nav-link>
<x-nav-link href="{{ route('student.study-load.index') }}" :active="request()->routeIs('student.study-load*')">
    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
    <span>Study Load</span>
</x-nav-link>
<x-nav-link href="{{ route('student.grades.index') }}" :active="request()->routeIs('student.grades*')">
    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><path d="M9 13h6"></path><path d="M9 17h6"></path></svg>
    <span>Grades</span>
</x-nav-link>
<x-nav-link href="{{ route('student.announcements.index') }}" :active="request()->routeIs('student.announcements*')">
    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 5h14"></path><path d="M5 9h14"></path><path d="M5 13h9"></path><path d="M5 17h7"></path><path d="M15 17l2 2 4-4"></path></svg>
    <span>Announcements</span>
</x-nav-link>
<x-nav-link href="{{ route('student.school-calendar.index') }}" :active="request()->routeIs('student.school-calendar*')">
    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"></rect><path d="M16 2v4"></path><path d="M8 2v4"></path><path d="M3 10h18"></path></svg>
    <span>School Calendar</span>
</x-nav-link>
<x-nav-link href="{{ route('student.ssg-events.index') }}" :active="request()->routeIs('student.ssg-events*')">
    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l7 4v6c0 5-3.5 8-7 10-3.5-2-7-5-7-10V6z"></path><path d="M9 12l2 2 4-4"></path></svg>
    <span>SSG Events &amp; Fines</span>
</x-nav-link>
