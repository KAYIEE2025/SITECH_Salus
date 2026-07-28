@extends('layouts.app')

@section('title', 'User Role Management')

@section('content')
    @if(session('success'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="sa-card overflow-hidden">
        <div class="sa-card-header">
            <h2 class="text-base font-semibold text-gray-800">User Role Management</h2>
            <p class="mt-1 text-xs text-gray-500">Manage user roles and permissions.</p>
        </div>

        <div class="border-b border-green-50 px-5 py-4 sm:px-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input
                        type="text"
                        id="searchInput"
                        placeholder="Search by name, username, email, or role..."
                        class="w-full rounded-lg border border-gray-300 pl-10 pr-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5c1a]"
                    >
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <span>Show</span>
                    <form method="GET" action="{{ route('superadmin.role-management') }}" class="inline">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <select
                            name="per_page"
                            onchange="this.form.submit()"
                            class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5c1a]"
                        >
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page') == 25 || !request('per_page') ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </form>
                    <span>entries</span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-left">Name</th>
                        <th class="px-6 py-3 text-left">Email</th>
                        <th class="px-6 py-3 text-left">Current Roles</th>
                        <th class="px-6 py-3 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="usersTableBody">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 user-row"
                            data-name="{{ $user->name }}"
                            data-username="{{ $user->username ?? '' }}"
                            data-email="{{ $user->email }}"
                            data-roles="{{ $user->roles->pluck('name')->implode(', ') }}"
                        >
                            <td class="px-6 py-3">
                                <p class="font-medium text-gray-800">{{ $user->name }}</p>
                            </td>
                            <td class="px-6 py-3 text-gray-500">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-3">
                                @if($user->roles->isNotEmpty())
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($user->roles as $role)
                                            <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-800">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">No roles assigned</span>
                                @endif
                            </td>
                            <td class="px-6 py-3">
                                <button
                                    onclick="openRoleModal({{ $user->id }}, '{{ $user->name }}', {{ $user->id === 1 ? 'true' : 'false' }})"
                                    class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs text-gray-700 transition hover:bg-gray-200"
                                >
                                    Manage Roles
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="border-t border-gray-100 px-6 py-4">
                {{ $users->appends(['per_page' => request('per_page'), 'search' => request('search')])->links() }}
            </div>
        @endif
    </div>

    <!-- Role Management Modal -->
    <div id="roleModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
        <div class="w-full max-w-md rounded-xl bg-white p-6">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-800">Manage Roles</h3>
                <button onclick="closeRoleModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <p class="mb-4 text-sm text-gray-600">
                Select roles for <span id="modalUserName" class="font-semibold"></span>
            </p>

            <form id="roleForm" method="POST" action="">
                @csrf
                @method('PUT')
                <input type="hidden" id="userId" name="user_id" value="">

                <div class="space-y-2">
                    @foreach($roles as $role)
                        <label class="flex items-center space-x-3 rounded-lg border border-gray-200 p-3 hover:bg-gray-50 cursor-pointer">
                            <input
                                type="checkbox"
                                name="roles[]"
                                value="{{ $role->name }}"
                                data-role="{{ $role->name }}"
                                class="h-4 w-4 rounded border-gray-300 text-[#1a5c1a] focus:ring-[#1a5c1a]"
                                onchange="updateRoleCheckboxes(this)"
                            >
                            <span class="text-sm text-gray-700">{{ $role->name }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button 
                        type="button" 
                        onclick="closeRoleModal()"
                        class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-200"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit"
                        class="rounded-lg bg-[#1a5c1a] px-4 py-2 text-sm font-semibold text-white transition hover:bg-green-900"
                    >
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        const usersTableBody = document.getElementById('usersTableBody');
        const userRows = document.querySelectorAll('.user-row');

        // Preserve search value from URL parameter on page load
        const urlParams = new URLSearchParams(window.location.search);
        const searchParam = urlParams.get('search');
        if (searchParam) {
            searchInput.value = searchParam;
            // Trigger search to filter results
            searchInput.dispatchEvent(new Event('input'));
        }

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            let visibleCount = 0;

            userRows.forEach(row => {
                const name = row.dataset.name.toLowerCase();
                const username = row.dataset.username.toLowerCase();
                const email = row.dataset.email.toLowerCase();
                const roles = row.dataset.roles.toLowerCase();

                const matches = name.includes(searchTerm) ||
                               username.includes(searchTerm) ||
                               email.includes(searchTerm) ||
                               roles.includes(searchTerm);

                if (matches) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Show/hide "No users found" message
            let noResultsRow = usersTableBody.querySelector('.no-results-row');
            if (visibleCount === 0 && userRows.length > 0) {
                if (!noResultsRow) {
                    noResultsRow = document.createElement('tr');
                    noResultsRow.className = 'no-results-row';
                    noResultsRow.innerHTML = '<td colspan="4" class="px-6 py-8 text-center text-gray-400">No users found.</td>';
                    usersTableBody.appendChild(noResultsRow);
                }
                noResultsRow.style.display = '';
            } else if (noResultsRow) {
                noResultsRow.style.display = 'none';
            }
        });

        function openRoleModal(userId, userName, isProtectedUser) {
            const modal = document.getElementById('roleModal');
            const form = document.getElementById('roleForm');
            const userIdInput = document.getElementById('userId');
            const modalUserName = document.getElementById('modalUserName');

            // Set user info
            userIdInput.value = userId;
            modalUserName.textContent = userName;

            // Set form action
            form.action = '{{ route('superadmin.role-management.update', ':user') }}'.replace(':user', userId);

            // Fetch current roles for this user
            fetch(`/superadmin/api/user-roles/${userId}`)
                .then(response => response.json())
                .then(data => {
                    // Reset all checkboxes and enable them
                    document.querySelectorAll('input[name="roles[]"]').forEach(checkbox => {
                        checkbox.checked = false;
                        checkbox.disabled = false;
                    });

                    // Check user's current roles
                    data.roles.forEach(roleName => {
                        const checkbox = document.querySelector(`input[name="roles[]"][value="${roleName}"]`);
                        if (checkbox) {
                            checkbox.checked = true;
                        }
                    });

                    // If user is the protected system Super Admin (user ID 1), disable the Super Admin checkbox
                    if (isProtectedUser) {
                        const superAdminCheckbox = document.querySelector(`input[name="roles[]"][value="Super Admin"]`);
                        if (superAdminCheckbox) {
                            superAdminCheckbox.disabled = true;
                        }
                    }

                    // Show modal
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                })
                .catch(error => console.error('Error fetching roles:', error));
        }

        function closeRoleModal() {
            const modal = document.getElementById('roleModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function updateRoleCheckboxes(checkbox) {
            // Optional: Add logic for mutually exclusive roles if needed
            // Currently allows multiple role assignments
        }

        // Close modal when clicking outside
        document.getElementById('roleModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRoleModal();
            }
        });
    </script>
@endsection
