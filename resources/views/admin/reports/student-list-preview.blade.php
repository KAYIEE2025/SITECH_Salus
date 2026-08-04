@extends('layouts.app')
@section('title', 'Student List Report Preview')
@section('sidebar-links')
    <x-nav-link href="{{ route('admin.dashboard') }}" :active="false">Dashboard</x-nav-link>
    <x-nav-link href="{{ route('admin.announcements.index') }}" :active="false">Announcements</x-nav-link>
    <x-nav-link href="{{ route('admin.calendar.index') }}" :active="false">School Calendar</x-nav-link>
    <x-nav-link href="{{ route('admin.reports.index') }}" :active="true">Reports</x-nav-link>
    <x-nav-link href="{{ route('admin.activity-logs.index') }}" :active="false">Activity Logs</x-nav-link>
    <x-nav-link href="{{ route('admin.profile.index') }}" :active="false">My Profile</x-nav-link>
@endsection
@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Student List Report Preview</h1>
            <p class="text-sm text-gray-600 mt-1">Review the report before printing</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.reports.index') }}" class="text-sm text-gray-600 hover:text-gray-800">
                ← Back to Reports
            </a>
            <button onclick="window.print()" class="bg-green-800 hover:bg-green-900 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition">
                Print PDF
            </button>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-8" id="print-area">
        <!-- Header -->
        <div class="text-center mb-8 border-b-2 border-gray-800 pb-6">
            <div class="flex justify-center mb-4">
                <img src="{{ asset('images/salus-logo.png') }}" alt="Salus Institute of Technology" class="h-20 w-20 object-contain">
            </div>
            <h1 class="text-2xl font-bold text-gray-800">SALUS INSTITUTE OF TECHNOLOGY</h1>
            <p class="text-sm text-gray-600 mt-1">Student List Report</p>
        </div>

        <!-- Report Info -->
        <div class="mb-6 bg-gray-50 p-4 rounded-lg">
            <div class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
                <div>
                    <span class="text-gray-500">Generated Date:</span>
                    <span class="font-medium text-gray-800 ml-2">{{ \Carbon\Carbon::now()->format('F d, Y g:i A') }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Grade Level:</span>
                    <span class="font-medium text-gray-800 ml-2">{{ $yearLevel ? $yearLevel->name : 'All' }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Section:</span>
                    <span class="font-medium text-gray-800 ml-2">{{ $section ? $section->name : 'All' }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Total Students:</span>
                    <span class="font-medium text-gray-800 ml-2">{{ $students->count() }}</span>
                </div>
            </div>
        </div>

        <!-- Student Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="border border-gray-600 px-4 py-3 text-left">Student Number</th>
                        <th class="border border-gray-600 px-4 py-3 text-left">Student Name</th>
                        <th class="border border-gray-600 px-4 py-3 text-left">Grade Level</th>
                        <th class="border border-gray-600 px-4 py-3 text-left">Section</th>
                        <th class="border border-gray-600 px-4 py-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-300 px-4 py-2">{{ $student->student_number ?? '—' }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $student->last_name }}, {{ $student->first_name }} {{ $student->middle_name ?? '' }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $student->yearLevel->name ?? '—' }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $student->section->name ?? '—' }}</td>
                        <td class="border border-gray-300 px-4 py-2">
                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Active</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="border border-gray-300 px-4 py-8 text-center text-gray-400">
                            No students found for the selected criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="mt-8 pt-4 border-t border-gray-200 text-center text-xs text-gray-500">
            <p>Generated by SITech Administration System</p>
            <p class="mt-1">Page 1 of 1</p>
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #print-area, #print-area * {
                visibility: visible;
            }
            #print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 20px;
            }
            aside, nav, .mb-6 {
                display: none !important;
            }
            main {
                margin-left: 0 !important;
            }
            .bg-white {
                border: none !important;
                border-radius: 0 !important;
            }
        }
    </style>
@endsection
