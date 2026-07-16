@extends('layouts.app')

@section('title', 'Activity Logs')

@section('content')
    <div class="rounded-xl border border-gray-200 bg-white">
        <div class="border-b border-gray-100 px-6 py-4">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-base font-semibold text-gray-800">System Activity Logs</h2>
                    <p class="mt-1 text-xs text-gray-500">Review login, account, grade, QR, and other system activity.</p>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="openPdfModal()" class="rounded-lg bg-[#1a5c1a] px-4 py-2 text-sm font-semibold text-white transition hover:bg-green-900">
                        Generate Report
                    </button>
                    <a href="{{ route('superadmin.activity-logs') }}" class="text-xs font-semibold text-[#1a5c1a] hover:text-green-900">
                        Reset filters
                    </a>
                </div>
            </div>

            <form method="GET" action="{{ route('superadmin.activity-logs') }}" class="mt-4 grid grid-cols-6 gap-3">
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Search description"
                    class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5c1a]">

                <select name="causer_id" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5c1a]">
                    <option value="">All users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected((string) request('causer_id') === (string) $user->id)>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>

                <select name="role" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5c1a]">
                    <option value="">All Roles</option>
                    <option value="Super Admin" @selected(request('role') === 'Super Admin')">Super Admin</option>
                    <option value="Admin" @selected(request('role') === 'Admin')">Admin</option>
                    <option value="Registrar" @selected(request('role') === 'Registrar')">Registrar</option>
                    <option value="Teacher" @selected(request('role') === 'Teacher')">Teacher</option>
                    <option value="SSG" @selected(request('role') === 'SSG')">SSG</option>
                    <option value="Student" @selected(request('role') === 'Student')">Student</option>
                </select>

                <select name="event" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5c1a]">
                    <option value="">All events</option>
                    @php
                        $eventCategories = [
                            'Authentication' => ['login', 'logout'],
                            'Accounts' => ['account_created', 'account_updated', 'account_deleted'],
                            'Roles' => ['roles_updated'],
                            'Grades' => ['grade_approved'],
                            'Other' => []
                        ];

                        // Categorize events
                        $categorizedEvents = [];
                        $uncategorizedEvents = [];

                        foreach($events as $event) {
                            $categorized = false;
                            foreach($eventCategories as $category => $eventList) {
                                if($category !== 'Other' && in_array($event, $eventList)) {
                                    $categorizedEvents[$category][] = $event;
                                    $categorized = true;
                                    break;
                                }
                            }
                            if(!$categorized) {
                                $uncategorizedEvents[] = $event;
                            }
                        }
                    @endphp

                    @foreach($categorizedEvents as $category => $categoryEvents)
                        @if(!empty($categoryEvents))
                            <optgroup label="{{ $category }}">
                                @foreach($categoryEvents as $event)
                                    <option value="{{ $event }}" @selected(request('event') === $event)>
                                        {{ ucfirst($event) }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif
                    @endforeach

                    @if(!empty($uncategorizedEvents))
                        <optgroup label="Other">
                            @foreach($uncategorizedEvents as $event)
                                <option value="{{ $event }}" @selected(request('event') === $event)>
                                    {{ ucfirst($event) }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endif
                </select>

                <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="From Date"
                    class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5c1a]">

                <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="To Date"
                    class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5c1a]">

                <button type="submit" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-200">
                    Apply Filters
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-left">Event</th>
                        <th class="px-6 py-3 text-left">Performed By</th>
                        <th class="px-6 py-3 text-left">Description</th>
                        <th class="px-6 py-3 text-left">Date & Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3">
                                <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-800">
                                    {{ ucfirst($log->event ?? 'activity') }}
                                </span>
                            </td>
                            <td class="px-6 py-3 font-medium text-gray-800">
                                {{ optional($log->causer)->name ?? 'System' }}
                            </td>
                            <td class="px-6 py-3 text-gray-500">
                                {{ $log->description }}
                            </td>
                            <td class="px-6 py-3 text-gray-400">
                                {{ $log->created_at->format('M d, Y h:i A') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                                No activity logs match the current filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="border-t border-gray-100 px-6 py-4">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

    <!-- PDF Generation Modal -->
    <div id="pdfModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
        <div class="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl">
            <h3 class="mb-4 text-lg font-semibold text-gray-800">Generate Activity Logs Report</h3>
            
            <form id="pdfForm" method="GET" action="{{ route('superadmin.activity-logs.pdf') }}">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                        <input type="date" name="date_from" 
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5c1a]">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                        <input type="date" name="date_to"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5c1a]">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <select name="role" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5c1a]">
                            <option value="">All Roles</option>
                            <option value="Super Admin">Super Admin</option>
                            <option value="Admin">Admin</option>
                            <option value="Registrar">Registrar</option>
                            <option value="Teacher">Teacher</option>
                            <option value="SSG">SSG</option>
                            <option value="Student">Student</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Event/Activity</label>
                        <select name="event" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5c1a]">
                            <option value="">All Events</option>
                            @foreach($events as $event)
                                <option value="{{ $event }}">{{ ucfirst($event) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="closePdfModal()" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="button" onclick="generatePdf('download')" class="rounded-lg bg-[#1a5c1a] px-4 py-2 text-sm font-semibold text-white transition hover:bg-green-900">
                        Save PDF
                    </button>
                    <button type="button" onclick="generatePdf('print')" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">
                        Print PDF
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openPdfModal() {
            document.getElementById('pdfModal').classList.remove('hidden');
            document.getElementById('pdfModal').classList.add('flex');
        }

        function closePdfModal() {
            document.getElementById('pdfModal').classList.add('hidden');
            document.getElementById('pdfModal').classList.remove('flex');
            document.getElementById('pdfForm').reset();
        }

        function generatePdf(action) {
            const form = document.getElementById('pdfForm');
            const formData = new FormData(form);
            const url = new URL(form.action);
            
            // Add all form fields to URL
            for (const [key, value] of formData.entries()) {
                if (value) {
                    url.searchParams.append(key, value);
                }
            }
            
            // Add action parameter
            url.searchParams.append('action', action);
            
            if (action === 'print') {
                // Open in new tab for print
                window.open(url.toString(), '_blank');
            } else {
                // Download directly
                window.location.href = url.toString();
            }
            
            closePdfModal();
        }

        // Close modal when clicking outside
        document.getElementById('pdfModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePdfModal();
            }
        });
    </script>
@endsection
