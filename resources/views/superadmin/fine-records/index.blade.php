@extends('layouts.app')

@section('title', 'Fine Records')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Fine Records</h1>
            <p class="mt-1 text-sm text-gray-500">View and generate reports for student fine records.</p>
        </div>
        <button onclick="openReportModal()" class="rounded-lg bg-green-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-green-900">
            Generate Report
        </button>
    </div>

    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-6">
        <form method="GET" action="{{ route('superadmin.fine-records.index') }}" class="grid gap-4 lg:grid-cols-5 lg:items-end">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">School Year</label>
                <select name="school_year" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    <option value="">All school years</option>
                    @foreach($schoolYears as $schoolYear)
                        <option value="{{ $schoolYear }}" @selected(($filters['school_year'] ?? '') === $schoolYear)>{{ $schoolYear }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Grade Level</label>
                <select name="year_level_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    <option value="">All grade levels</option>
                    @foreach($yearLevels as $yearLevel)
                        <option value="{{ $yearLevel->id }}" @selected((string) ($filters['year_level_id'] ?? '') === (string) $yearLevel->id)>{{ $yearLevel->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Section</label>
                <select name="section_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    <option value="">All sections</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}" @selected((string) ($filters['section_id'] ?? '') === (string) $section->id)>
                            {{ $section->yearLevel->name ?? 'Grade' }} - Section {{ $section->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Search Student</label>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
                    placeholder="Name or student number">
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 rounded-lg bg-green-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-green-900">
                    Apply
                </button>
                <a href="{{ route('superadmin.fine-records.index') }}" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-200">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-left">Student Number</th>
                        <th class="px-6 py-3 text-left">Student Name</th>
                        <th class="px-6 py-3 text-left">Grade Level</th>
                        <th class="px-6 py-3 text-left">Section</th>
                        <th class="px-6 py-3 text-left">Events Attended</th>
                        <th class="px-6 py-3 text-left">Events Absent</th>
                        <th class="px-6 py-3 text-left">Total Fine Balance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($students as $student)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $student->student_number }}</td>
                            <td class="px-6 py-4 text-gray-700">{{ $student->full_name }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $student->yearLevel->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $student->section ? 'Section ' . $student->section->name : 'N/A' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $student->events_attended_count }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $student->events_absent_count }}</td>
                            <td class="px-6 py-4 font-semibold text-gray-800">PHP {{ number_format($student->total_fine_balance ?? 0, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-400">No students found for the selected filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
            <div class="border-t border-gray-100 px-6 py-4">{{ $students->links() }}</div>
        @endif
    </div>

    <!-- Report Modal -->
    <div id="reportModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
        <div class="w-full max-w-2xl rounded-xl bg-white p-6">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-800">Generate Fine Records Report</h2>
                <button onclick="closeReportModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form method="GET" action="{{ route('superadmin.fine-records.report') }}" class="space-y-4">
                <div class="grid gap-4 lg:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Date From</label>
                        <input type="date" name="date_from" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Date To</label>
                        <input type="date" name="date_to" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    </div>
                </div>

                <div class="grid gap-4 lg:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">School Year</label>
                        <select name="school_year" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                            <option value="">All school years</option>
                            @foreach($schoolYears as $schoolYear)
                                <option value="{{ $schoolYear }}">{{ $schoolYear }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Grade Level</label>
                        <select name="year_level_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                            <option value="">All grade levels</option>
                            @foreach($yearLevels as $yearLevel)
                                <option value="{{ $yearLevel->id }}">{{ $yearLevel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid gap-4 lg:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Section</label>
                        <select name="section_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                            <option value="">All sections</option>
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}">
                                    {{ $section->yearLevel->name ?? 'Grade' }} - Section {{ $section->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Search Student</label>
                        <input type="text" name="search" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600" placeholder="Name or student number">
                    </div>
                </div>

                <div class="flex gap-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="include_paid" value="1" checked class="rounded border-gray-300 text-green-600 focus:ring-green-600">
                        <span class="text-sm text-gray-700">Include Paid</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="include_unpaid" value="1" checked class="rounded border-gray-300 text-green-600 focus:ring-green-600">
                        <span class="text-sm text-gray-700">Include Unpaid</span>
                    </label>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="closeReportModal()" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-200">
                        Cancel
                    </button>
                    <button type="submit" name="action" value="preview" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">
                        Preview
                    </button>
                    <button type="submit" name="action" value="print" class="rounded-lg bg-green-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-green-900">
                        Print
                    </button>
                    <button type="submit" name="action" value="download" class="rounded-lg bg-purple-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-purple-700">
                        Export PDF
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openReportModal() {
            document.getElementById('reportModal').classList.remove('hidden');
            document.getElementById('reportModal').classList.add('flex');
        }

        function closeReportModal() {
            document.getElementById('reportModal').classList.add('hidden');
            document.getElementById('reportModal').classList.remove('flex');
        }

        // Close modal when clicking outside
        document.getElementById('reportModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeReportModal();
            }
        });
    </script>
@endsection
