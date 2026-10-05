<x-nav-link href="{{ route('superadmin.dashboard') }}" :active="request()->routeIs('superadmin.dashboard')">
    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
    <span>Dashboard</span>
</x-nav-link>
<x-nav-link href="{{ route('superadmin.accounts') }}" :active="request()->routeIs('superadmin.accounts*')">
    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
    <span>Manage Accounts</span>
</x-nav-link>
<x-nav-link href="{{ route('superadmin.student-accounts.index') }}" :active="request()->routeIs('superadmin.student-accounts*')">
    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
    <span>Student Accounts</span>
</x-nav-link>

<x-nav-link href="{{ route('superadmin.role-management') }}" :active="request()->routeIs('superadmin.role-management*')">
    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l7 4v6c0 5-3.5 8-7 10-3.5-2-7-5-7-10V6z"></path><path d="M9 12l2 2 4-4"></path></svg>
    <span>User Role Management</span>
</x-nav-link>
<x-nav-link href="{{ route('superadmin.fine-records.index') }}" :active="request()->routeIs('superadmin.fine-records*')">
    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l7 4v6c0 5-3.5 8-7 10-3.5-2-7-5-7-10V6z"></path><path d="M9 12l2 2 4-4"></path></svg>
    <span>Fine Records</span>
</x-nav-link>
<x-nav-link href="{{ route('superadmin.activity-logs') }}" :active="request()->routeIs('superadmin.activity-logs')">
    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
    <span>Activity Logs</span>
</x-nav-link>
