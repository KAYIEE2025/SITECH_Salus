<x-nav-link href="{{ route('ssg.dashboard') }}" :active="request()->routeIs('ssg.dashboard')">Dashboard</x-nav-link>
<x-nav-link href="{{ route('ssg.events.index') }}" :active="request()->routeIs('ssg.events*') || request()->routeIs('ssg.attendance*')">Events</x-nav-link>
<x-nav-link href="{{ route('ssg.fines.index') }}" :active="request()->routeIs('ssg.fines*')">Fine Records</x-nav-link>
<x-nav-link href="{{ route('ssg.profile.index') }}" :active="request()->routeIs('ssg.profile*')">My Profile</x-nav-link>
