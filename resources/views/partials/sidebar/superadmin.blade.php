<x-nav-link href="{{ route('superadmin.dashboard') }}" :active="request()->routeIs('superadmin.dashboard')">Dashboard</x-nav-link>
<x-nav-link href="{{ route('superadmin.accounts') }}" :active="request()->routeIs('superadmin.accounts*')">Manage Accounts</x-nav-link>
<x-nav-link href="{{ route('superadmin.student-accounts.index') }}" :active="request()->routeIs('superadmin.student-accounts*')">Student Accounts</x-nav-link>
<x-nav-link href="{{ route('superadmin.roles') }}" :active="request()->routeIs('superadmin.roles')">Roles & Access</x-nav-link>
<x-nav-link href="{{ route('superadmin.role-management') }}" :active="request()->routeIs('superadmin.role-management*')">User Role Management</x-nav-link>
<x-nav-link href="{{ route('superadmin.fine-records.index') }}" :active="request()->routeIs('superadmin.fine-records*')">Fine Records</x-nav-link>
<x-nav-link href="{{ route('superadmin.activity-logs') }}" :active="request()->routeIs('superadmin.activity-logs')">Activity Logs</x-nav-link>
<x-nav-link href="{{ route('superadmin.profile') }}" :active="request()->routeIs('superadmin.profile')">My Profile</x-nav-link>
