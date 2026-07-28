<x-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')">Dashboard</x-nav-link>
<x-nav-link href="{{ route('admin.announcements.index') }}" :active="request()->routeIs('admin.announcements*')">Announcements</x-nav-link>
<x-nav-link href="{{ route('admin.calendar.index') }}" :active="request()->routeIs('admin.calendar*')">School Calendar</x-nav-link>
<x-nav-link href="{{ route('admin.reports.index') }}" :active="request()->routeIs('admin.reports*')">Reports</x-nav-link>
<x-nav-link href="{{ route('admin.activity-logs.index') }}" :active="request()->routeIs('admin.activity-logs*')">Activity Logs</x-nav-link>
