@extends('layouts.app')
@section('title', 'Reports')
@section('sidebar-links')
    <x-nav-link href="{{ route('admin.dashboard') }}" :active="false">Dashboard</x-nav-link>
    <x-nav-link href="{{ route('admin.announcements.index') }}" :active="false">Announcements</x-nav-link>
    <x-nav-link href="{{ route('admin.calendar.index') }}" :active="false">School Calendar</x-nav-link>
    <x-nav-link href="{{ route('admin.reports.index') }}" :active="true">Reports</x-nav-link>
    <x-nav-link href="{{ route('admin.activity-logs.index') }}" :active="false">Activity Logs</x-nav-link>
    <x-nav-link href="{{ route('admin.profile.index') }}" :active="false">My Profile</x-nav-link>
@endsection
@section('content')
    @session('success')
        <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg px-4 py-3 mb-6">
            {{ $value }}
        </div>
    @endsession
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3 mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Reports</h1>
    </div>

    <div class="grid grid-cols-2 gap-6">
        <!-- Student List Report -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Student List</h2>
            <p class="text-sm text-gray-600 mb-4">Generate a PDF report of students filtered by grade level and/or section.</p>
            
            <form method="POST" action="{{ route('admin.reports.student-list') }}" id="studentListForm">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Grade Level</label>
                    <select name="year_level_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                        <option value="">All Grade Levels</option>
                        @foreach($yearLevels as $yearLevel)
                            <option value="{{ $yearLevel->id }}">{{ $yearLevel->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                    <select name="section_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                        <option value="">All Sections</option>
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}">{{ $section->yearLevel->name ?? '' }} — Section {{ $section->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-3">
                    <button type="submit"
                        class="flex-1 bg-green-800 hover:bg-green-900 text-white text-sm font-medium py-2.5 rounded-lg transition">
                        Preview & Print
                    </button>
                    <button type="button" onclick="downloadStudentListPdf()"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2.5 rounded-lg transition">
                        Download PDF
                    </button>
                </div>
            </form>
        </div>

        <!-- Grade Summary Report -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Grade Summary</h2>
            <p class="text-sm text-gray-600 mb-4">Generate a PDF report of student grades for a specific subject.</p>
            
            <form method="POST" action="{{ route('admin.reports.grade-summary') }}" id="gradeSummaryForm">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subject <span class="text-red-500">*</span></label>
                    <select name="subject_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                        <option value="">Select subject...</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->code }} - {{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Grade Level</label>
                    <select name="year_level_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                        <option value="">All Grade Levels</option>
                        @foreach($yearLevels as $yearLevel)
                            <option value="{{ $yearLevel->id }}">{{ $yearLevel->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                    <select name="section_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                        <option value="">All Sections</option>
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}">{{ $section->yearLevel->name ?? '' }} — Section {{ $section->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="generateGradeSummaryPdf()"
                        class="flex-1 bg-green-800 hover:bg-green-900 text-white text-sm font-medium py-2.5 rounded-lg transition">
                        Generate PDF
                    </button>
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2.5 rounded-lg transition">
                        Print
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function downloadStudentListPdf() {
            const form = document.getElementById('studentListForm');
            const formData = new FormData(form);
            
            const pdfForm = document.createElement('form');
            pdfForm.method = 'POST';
            pdfForm.action = '{{ route("admin.reports.student-list-pdf") }}';
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken.getAttribute('content');
                pdfForm.appendChild(csrfInput);
            }
            
            for (let [key, value] of formData.entries()) {
                if (value) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = value;
                    pdfForm.appendChild(input);
                }
            }
            
            document.body.appendChild(pdfForm);
            pdfForm.submit();
            document.body.removeChild(pdfForm);
        }

        function generateGradeSummaryPdf() {
            const form = document.getElementById('gradeSummaryForm');
            const formData = new FormData(form);
            
            const pdfForm = document.createElement('form');
            pdfForm.method = 'POST';
            pdfForm.action = '{{ route("admin.reports.grade-summary-pdf") }}';
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken.getAttribute('content');
                pdfForm.appendChild(csrfInput);
            }
            
            for (let [key, value] of formData.entries()) {
                if (value) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = value;
                    pdfForm.appendChild(input);
                }
            }
            
            document.body.appendChild(pdfForm);
            pdfForm.submit();
            document.body.removeChild(pdfForm);
        }
    </script>
@endsection
