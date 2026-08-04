@extends('layouts.app')
@section('title', 'Grade Summary Report Preview')
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
            <h1 class="text-2xl font-bold text-gray-800">Grade Summary Report Preview</h1>
            <p class="text-sm text-gray-600 mt-1">Review the report before printing</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.reports.index') }}" class="text-sm text-gray-600 hover:text-gray-800">
                ← Back to Reports
            </a>
            <button onclick="window.print()" class="bg-green-800 hover:bg-green-900 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition">
                Print
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
            <p class="text-sm text-gray-600 mt-1">Grade Summary Report</p>
        </div>

        <!-- Report Info -->
        <div class="mb-6 bg-gray-50 p-4 rounded-lg">
            <div class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
                <div>
                    <span class="text-gray-500">Subject:</span>
                    <span class="font-medium text-gray-800 ml-2">{{ $subject->code }} - {{ $subject->name }}</span>
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
                    <span class="text-gray-500">Generated Date:</span>
                    <span class="font-medium text-gray-800 ml-2">{{ \Carbon\Carbon::now()->format('F d, Y g:i A') }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Total Students:</span>
                    <span class="font-medium text-gray-800 ml-2">{{ count($studentGrades) }}</span>
                </div>
            </div>
        </div>

        <!-- Grades Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="border border-gray-600 px-4 py-3 text-left">Student Number</th>
                        <th class="border border-gray-600 px-4 py-3 text-left">Student Name</th>
                        <th class="border border-gray-600 px-4 py-3 text-center">Term 1</th>
                        <th class="border border-gray-600 px-4 py-3 text-center">Term 2</th>
                        <th class="border border-gray-600 px-4 py-3 text-center">Term 3</th>
                        <th class="border border-gray-600 px-4 py-3 text-center">Final Grade</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($studentGrades as $index => $studentGrade)
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-300 px-4 py-2">{{ $studentGrade['student']->student_number ?? '—' }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $studentGrade['student']->last_name }}, {{ $studentGrade['student']->first_name }} {{ $studentGrade['student']->middle_name ?? '' }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center">{{ $studentGrade['term1'] }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center">{{ $studentGrade['term2'] }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center">{{ $studentGrade['term3'] }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center font-medium">{{ $studentGrade['final_grade'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="border border-gray-300 px-4 py-8 text-center text-gray-400">
                            No students found for this subject.
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
