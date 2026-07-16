<x-nav-link href="{{ route('registrar.dashboard') }}" :active="request()->routeIs('registrar.dashboard')">Dashboard</x-nav-link>
<x-nav-link href="{{ route('registrar.sections') }}" :active="request()->routeIs('registrar.sections*')">Sections</x-nav-link>
<x-nav-link href="{{ route('registrar.students') }}" :active="request()->routeIs('registrar.students*')">Student Records</x-nav-link>
<x-nav-link href="{{ route('registrar.study-load') }}" :active="request()->routeIs('registrar.study-load*')">Study Load</x-nav-link>
<x-nav-link href="{{ route('registrar.grade-approval') }}" :active="request()->routeIs('registrar.grade-approval*')">Grade Approval</x-nav-link>
<x-nav-link href="{{ route('registrar.profile') }}" :active="request()->routeIs('registrar.profile')">My Profile</x-nav-link>
