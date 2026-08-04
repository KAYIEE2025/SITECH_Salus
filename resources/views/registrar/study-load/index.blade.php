@extends('layouts.app')
@section('title', 'Study Load Management')
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

    {{-- Tabs --}}
    <div class="ra-card mb-6 p-5 sm:p-6">
        <div class="flex gap-4 border-b border-gray-200 mb-4">
            <button onclick="showTab('section-schedule')" id="tab-section-schedule"
                class="pb-3 px-1 text-sm font-medium border-b-2 border-green-600 text-green-700">
                Section Schedule
            </button>
            <button onclick="showTab('student-study-load')" id="tab-student-study-load"
                class="pb-3 px-1 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                Student Study Load
            </button>
        </div>

        {{-- Section Schedule Tab --}}
        <div id="section-schedule-tab">
            <h2 class="text-base font-semibold text-gray-800 mb-4">Manage Class Schedule by Section</h2>
            <p class="text-sm text-gray-500 mb-4">Create and manage the class schedule assigned to each section. Students will automatically inherit this schedule.</p>
            
            <form method="GET" action="{{ route('registrar.study-load') }}" class="flex gap-3 flex-wrap">
                <select name="section_id"
                class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                <option value="">Select a section...</option>
                @foreach($sections as $section)
                    <option value="{{ $section->id }}"
                        {{ optional($selected)->id == $section->id ? 'selected' : '' }}>
                        {{ $section->yearLevel->name ?? '' }} — Section {{ $section->name }}
                    </option>
                @endforeach
            </select>
                <input type="text" name="school_year" value="{{ request('school_year','2026-2027') }}"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
                    placeholder="School Year">
                <button type="submit"
                    class="bg-green-800 hover:bg-green-900 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                    View Schedule
                </button>
            </form>
        </div>

        {{-- Student Study Load Tab --}}
        <div id="student-study-load-tab" class="hidden">
            <h2 class="text-base font-semibold text-gray-800 mb-4">View Student Study Load</h2>
            <p class="text-sm text-gray-500 mb-4">View the class schedule of students assigned to a section. Students automatically inherit their section's schedule.</p>
            
            <form method="GET" action="{{ route('registrar.study-load') }}" class="flex gap-3 flex-wrap">
                <select name="section_id"
                class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                <option value="">Select a section...</option>
                @foreach($sections as $section)
                    <option value="{{ $section->id }}"
                        {{ optional($selected)->id == $section->id ? 'selected' : '' }}>
                        {{ $section->yearLevel->name ?? '' }} — Section {{ $section->name }}
                    </option>
                @endforeach
            </select>
                <input type="text" name="school_year" value="{{ request('school_year','2026-2027') }}"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
                    placeholder="School Year">
                <input type="hidden" name="view" value="students">
                <button type="submit"
                    class="bg-green-800 hover:bg-green-900 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                    View Students
                </button>
            </form>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tabs
            document.getElementById('section-schedule-tab').classList.add('hidden');
            document.getElementById('student-study-load-tab').classList.add('hidden');
            
            // Reset tab styles
            document.getElementById('tab-section-schedule').classList.remove('border-green-600', 'text-green-700');
            document.getElementById('tab-section-schedule').classList.add('border-transparent', 'text-gray-500');
            document.getElementById('tab-student-study-load').classList.remove('border-green-600', 'text-green-700');
            document.getElementById('tab-student-study-load').classList.add('border-transparent', 'text-gray-500');
            
            // Show selected tab
            document.getElementById(tabName + '-tab').classList.remove('hidden');
            document.getElementById('tab-' + tabName).classList.remove('border-transparent', 'text-gray-500');
            document.getElementById('tab-' + tabName).classList.add('border-green-600', 'text-green-700');
        }

        // Check URL parameter to determine which tab to show
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('view') === 'students') {
            showTab('student-study-load');
        }
    </script>

    @if($selected && request('view') != 'students')
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

        {{-- Add Schedule Entry to Section --}}
        <div class="ra-card p-6">
            <h2 class="text-base font-semibold text-gray-800">
                Add Schedule Entry — {{ $selected->yearLevel->name ?? '' }} Section {{ $selected->name }}
            </h2>

            <form method="POST" action="{{ route('registrar.study-load.store') }}">
                @csrf
                <input type="hidden" name="section_id" value="{{ $selected->id }}">
                <input type="hidden" name="school_year" value="{{ request('school_year', '2026-2027') }}">

               <div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Subject Code <span class="text-red-500">*</span>
    </label>
    <input type="text" name="subject_code"
        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
        placeholder="e.g. MATH 101...">
    <p class="text-xs text-gray-400 mt-1">Short code for the subject.</p>
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Subject Name <span class="text-red-500">*</span>
    </label>
    <input type="text" name="subject_name"
        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
        placeholder="e.g. Mathematics">
</div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teacher</label>
                    <select name="teacher_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                        <option value="">Select teacher...</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Room</label>
                    <input type="text" name="room" placeholder="Room 301..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Days</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['Mon','Tue','Wed','Thu','Fri','Sat'] as $day)
                            <label class="flex items-center gap-1 text-sm text-gray-700">
                                <input type="checkbox" name="days[]" value="{{ $day }}"
                                    class="rounded border-gray-300 text-green-600">
                                {{ $day }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3 mb-6 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Time Start</label>
                        <input type="time" name="time_start"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Time End</label>
                        <input type="time" name="time_end"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3 mb-6 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date Start</label>
                        <input type="date" name="date_start"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
                            placeholder="Optional">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date End</label>
                        <input type="date" name="date_end"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
                            placeholder="Optional">
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-green-800 hover:bg-green-900 text-white text-sm font-semibold py-2.5 rounded-lg transition">
                    Add Schedule Entry
                </button>
            </form>
        </div>

        {{-- Section Schedule Table (sorted by time) --}}
        <div class="ra-card overflow-hidden xl:col-span-2">
            <div class="ra-card-header">
                <h2 class="text-base font-semibold text-gray-800">
                    Class Schedule — {{ $selected->yearLevel->name ?? '' }} Section {{ $selected->name }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">{{ request('school_year','2026-2027') }} · Sorted by time</p>
            </div>

            <div class="overflow-x-auto"><table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs">
                    <tr>
                        <th class="text-left px-6 py-3">Subject</th>
                        <th class="text-left px-6 py-3">Teacher</th>
                        <th class="text-left px-6 py-3">Days</th>
                        <th class="text-left px-6 py-3">Time</th>
                        <th class="text-left px-6 py-3">Date Range</th>
                        <th class="text-left px-6 py-3">Room</th>
                        <th class="text-left px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($schedules as $schedule)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3">
                            <p class="font-medium text-gray-800">{{ $schedule->subject->code ?? '—' }}</p>
                            <p class="text-xs text-gray-400">{{ $schedule->subject->name ?? '' }}</p>
                        </td>
                        <td class="px-6 py-3 text-gray-600">{{ $schedule->teacher->name ?? '—' }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ implode(', ', $schedule->days ?? []) }}</td>
                        <td class="px-6 py-3 text-gray-600 text-xs">
                            {{ \Carbon\Carbon::parse($schedule->time_start)->format('h:i A') }} –
                            {{ \Carbon\Carbon::parse($schedule->time_end)->format('h:i A') }}
                        </td>
                        <td class="px-6 py-3 text-gray-600 text-xs">
                            @if($schedule->date_start && $schedule->date_end)
                                {{ \Carbon\Carbon::parse($schedule->date_start)->format('M d, Y') }} –
                                {{ \Carbon\Carbon::parse($schedule->date_end)->format('M d, Y') }}
                            @elseif($schedule->date_start)
                                {{ \Carbon\Carbon::parse($schedule->date_start)->format('M d, Y') }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-6 py-3 text-gray-600">{{ $schedule->room }}</td>
                        <td class="px-6 py-3">
                            <form method="POST" action="{{ route('registrar.study-load.destroy', $schedule) }}"
                                onsubmit="return confirm('Remove this subject from study load?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="text-xs bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-lg transition">
                                    Remove
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                            No schedule entries for this section yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table></div>
        </div>

    </div>
    @endif

    {{-- Student Study Load View --}}
    @if($selected && request('view') == 'students')
        <div class="ra-card overflow-hidden">
            <div class="ra-card-header">
            <h2 class="text-base font-semibold text-gray-800">
                Students in {{ $selected->yearLevel->name ?? '' }} Section {{ $selected->name }}
            </h2>
            <p class="text-xs text-gray-500 mt-0.5">{{ request('school_year','2026-2027') }}</p>
        </div>

        @php
            $students = \App\Models\Student::where('section_id', $selected->id)
                ->where('school_year', request('school_year', '2026-2027'))
                ->get();
        @endphp

        @forelse($students as $student)
            <div class="border-b border-green-50 px-5 py-4 sm:px-6">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="font-semibold text-gray-800">
                            {{ $student->last_name }}, {{ $student->first_name }} {{ $student->middle_name }}
                        </p>
                        <p class="text-xs text-gray-500">
                            Student Number: {{ $student->student_number }}
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="toggleStudentSchedule({{ $student->id }})"
                            class="text-xs bg-green-50 hover:bg-green-100 text-green-700 px-3 py-1.5 rounded-lg transition">
                            View Schedule
                        </button>
                        <button onclick="openPrintPreview({{ $student->id }})"
                            class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1.5 rounded-lg transition">
                            🖨️ Print Schedule
                        </button>
                    </div>
                </div>

                {{-- Student's Schedule (inherited from section) --}}
                <div id="student-schedule-{{ $student->id }}" class="hidden">
                    @php
                        $studentSchedules = \App\Models\ClassSchedule::with(['subject', 'teacher'])
                            ->where('section_id', $selected->id)
                            ->where('school_year', request('school_year', '2026-2027'))
                            ->orderBy('time_start')
                            ->get();
                    @endphp

                    <table class="w-full text-sm mt-3">
                        <thead class="bg-gray-50 text-gray-500 text-xs">
                            <tr>
                                <th class="text-left px-4 py-2">Subject</th>
                                <th class="text-left px-4 py-2">Teacher</th>
                                <th class="text-left px-4 py-2">Day</th>
                                <th class="text-left px-4 py-2">Time</th>
                                <th class="text-left px-4 py-2">Room</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($studentSchedules as $schedule)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2">
                                    <p class="font-medium text-gray-800">{{ $schedule->subject->code ?? '—' }}</p>
                                    <p class="text-xs text-gray-400">{{ $schedule->subject->name ?? '' }}</p>
                                </td>
                                <td class="px-4 py-2 text-gray-600">{{ $schedule->teacher->name ?? '—' }}</td>
                                <td class="px-4 py-2 text-gray-600">{{ implode(', ', $schedule->days ?? []) }}</td>
                                <td class="px-4 py-2 text-gray-600 text-xs">
                                    {{ \Carbon\Carbon::parse($schedule->time_start)->format('h:i A') }} –
                                    {{ \Carbon\Carbon::parse($schedule->time_end)->format('h:i A') }}
                                </td>
                                <td class="px-4 py-2 text-gray-600">{{ $schedule->room }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-4 text-center text-gray-400">
                                    No schedule assigned to this section yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="px-6 py-8 text-center text-gray-400">
                No students enrolled in this section for the selected school year.
            </div>
        @endforelse
    </div>
    @endif

    {{-- Print Preview Modal --}}
    <div id="print-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center sticky top-0 bg-white">
                <h2 class="text-base font-semibold text-gray-800">Print Class Schedule Preview</h2>
                <button onclick="closePrintModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="print-preview-content" class="p-6" style="background: white;">
                <!-- Preview content will be loaded here -->
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 sticky bottom-0 bg-white">
                <button onclick="closePrintModal()"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
                    Close
                </button>
                <button onclick="printSchedule()"
                    class="px-4 py-2 bg-green-800 hover:bg-green-900 text-white rounded-lg transition">
                    Print PDF
                </button>
            </div>
        </div>
    </div>

    <style>
        /* Modal preview styles to match printable PDF */
        #print-preview-content .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 12mm 15mm;
            background: white;
            position: relative;
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            line-height: 1.5;
            color: #000000;
        }

        #print-preview-content .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        #print-preview-content .logo {
            height: 50px;
            margin-bottom: 8px;
        }

        #print-preview-content .school-name {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 3px;
        }

        #print-preview-content .school-address {
            font-size: 10px;
            margin-bottom: 8px;
        }

        #print-preview-content .document-title {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 8px;
        }

        #print-preview-content .section-title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 15px 0 8px 0;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
        }

        #print-preview-content .info-box {
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 15px;
        }

        #print-preview-content .info-row {
            display: flex;
            margin-bottom: 5px;
        }

        #print-preview-content .info-row:last-child {
            margin-bottom: 0;
        }

        #print-preview-content .info-label {
            font-weight: bold;
            width: 120px;
            flex-shrink: 0;
        }

        #print-preview-content .info-value {
            flex: 1;
            border-bottom: 1px dotted #000;
            padding-left: 5px;
        }

        #print-preview-content .info-col {
            flex: 1;
        }

        #print-preview-content .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            border: 1px solid #000;
        }

        #print-preview-content .schedule-table th {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            background: #f0f0f0;
        }

        #print-preview-content .schedule-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 11px;
            vertical-align: top;
        }

        #print-preview-content .schedule-table .subject-code {
            font-weight: bold;
            font-size: 10px;
        }

        #print-preview-content .schedule-table .subject-name {
            font-size: 9px;
            font-style: italic;
        }

        #print-preview-content .footer {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        #print-preview-content .footer-left {
            font-size: 10px;
        }

        #print-preview-content .footer-right {
            text-align: center;
        }

        #print-preview-content .signature-box {
            width: 180px;
            margin-top: 30px;
        }

        #print-preview-content .signature-line {
            border-top: 1px solid #000;
            padding-top: 3px;
            font-size: 10px;
            font-weight: bold;
        }

        #print-preview-content .date-box {
            margin-top: 15px;
            font-size: 10px;
        }

        #print-preview-content .page-number {
            position: absolute;
            bottom: 12mm;
            right: 15mm;
            font-size: 10px;
        }
    </style>

    <script>
        function toggleStudentSchedule(studentId) {
            const element = document.getElementById('student-schedule-' + studentId);
            element.classList.toggle('hidden');
        }

        function openPrintPreview(studentId) {
            const sectionId = '{{ request('section_id') }}';
            const schoolYear = '{{ request('school_year', '2026-2027') }}';
            
            fetch(`/registrar/students/${studentId}/print-class-schedule?section_id=${sectionId}&school_year=${schoolYear}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('print-preview-content').innerHTML = html;
                    document.getElementById('print-modal').classList.remove('hidden');
                });
        }

        function closePrintModal() {
            document.getElementById('print-modal').classList.add('hidden');
            document.getElementById('print-preview-content').innerHTML = '';
        }

        function printSchedule() {
            const printContent = document.getElementById('print-preview-content').innerHTML;
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                <head>
                    <title>Print Class Schedule</title>
                    <style>
                        * { margin: 0; padding: 0; box-sizing: border-box; }
                        body { font-family: 'Times New Roman', Times, serif; font-size: 12px; line-height: 1.5; background-color: #ffffff; color: #000000; }
                        .page { width: 210mm; min-height: 297mm; margin: 0 auto; padding: 12mm 15mm; background: white; position: relative; }
                        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 10px; }
                        .logo { height: 50px; margin-bottom: 8px; }
                        .school-name { font-size: 14px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 3px; }
                        .school-address { font-size: 10px; margin-bottom: 8px; }
                        .document-title { font-size: 16px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; margin-top: 8px; }
                        .section-title { font-size: 12px; font-weight: bold; text-transform: uppercase; margin: 15px 0 8px 0; border-bottom: 1px solid #000; padding-bottom: 3px; }
                        .info-box { border: 1px solid #000; padding: 10px; margin-bottom: 15px; }
                        .info-row { display: flex; margin-bottom: 5px; }
                        .info-row:last-child { margin-bottom: 0; }
                        .info-label { font-weight: bold; width: 120px; flex-shrink: 0; }
                        .info-value { flex: 1; border-bottom: 1px dotted #000; padding-left: 5px; }
                        .info-col { flex: 1; }
                        .schedule-table { width: 100%; border-collapse: collapse; margin: 15px 0; border: 1px solid #000; }
                        .schedule-table th { border: 1px solid #000; padding: 6px 8px; font-size: 11px; font-weight: bold; text-transform: uppercase; text-align: center; background: #f0f0f0 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                        .schedule-table td { border: 1px solid #000; padding: 6px 8px; font-size: 11px; vertical-align: top; }
                        .schedule-table .subject-code { font-weight: bold; font-size: 10px; }
                        .schedule-table .subject-name { font-size: 9px; font-style: italic; }
                        .footer { margin-top: 20px; display: flex; justify-content: space-between; align-items: flex-end; }
                        .footer-left { font-size: 10px; }
                        .footer-right { text-align: center; }
                        .signature-box { width: 180px; margin-top: 30px; }
                        .signature-line { border-top: 1px solid #000; padding-top: 3px; font-size: 10px; font-weight: bold; }
                        .date-box { margin-top: 15px; font-size: 10px; }
                        .page-number { position: absolute; bottom: 12mm; right: 15mm; font-size: 10px; }
                        @page { size: A4 portrait; margin: 0; }
                    </style>
                </head>
                <body>
                    ${printContent}
                    <script>
                        window.onload = function() {
                            window.print();
                            window.close();
                        };
                    <\/script>
                </body>
                </html>
            `);
            printWindow.document.close();
        }
    </script>

@endsection
