<x-nav-link href="{{ route('teacher.dashboard') }}" :active="request()->routeIs('teacher.dashboard')">Dashboard</x-nav-link>
<x-nav-link href="{{ route('teacher.schedule.index') }}" :active="request()->routeIs('teacher.schedule*')">My Schedule</x-nav-link>
<x-nav-link href="{{ route('teacher.classes.index') }}" :active="request()->routeIs('teacher.classes*')">Class List</x-nav-link>
<x-nav-link href="{{ route('teacher.announcements.index') }}" :active="request()->routeIs('teacher.announcements*')">Announcements</x-nav-link>
