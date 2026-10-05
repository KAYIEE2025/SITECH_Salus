<div class="page">
    <!-- Header -->
    <div class="header">
        @if(file_exists(public_path('images/salus-logo.png')))
            <img src="{{ $schoolLogo }}" alt="School Logo" class="logo">
        @endif
        <div class="school-name">SALUS INSTITUTE OF TECHNOLOGY</div>
        <div class="school-address">Student Information System</div>
        <div class="document-title">Student Study Load</div>
    </div>

    <!-- Student Information -->
    <div class="section-title">Student Information</div>
    <div class="info-box">
        <div class="info-row">
            <div class="info-col">
                <div class="info-row">
                    <div class="info-label">Student No.:</div>
                    <div class="info-value">{{ $student->student_number }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Name:</div>
                    <div class="info-value">{{ $student->last_name }}, {{ $student->first_name }} {{ $student->middle_name }} {{ $student->suffix }}</div>
                </div>
            </div>
            <div class="info-col">
                <div class="info-row">
                    <div class="info-label">Grade Level:</div>
                    <div class="info-value">{{ $student->yearLevel->name ?? 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Section:</div>
                    <div class="info-value">{{ $student->section->yearLevel->name ?? '' }} — Sec {{ $student->section->name ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
        <div class="info-row">
            <div class="info-col">
                <div class="info-row">
                    <div class="info-label">School Year:</div>
                    <div class="info-value">{{ request('school_year', $student->school_year) }}</div>
                </div>
            </div>
        </div>
        @if($student->section->adviser ?? null)
        <div class="info-row">
            <div class="info-col">
                <div class="info-row">
                    <div class="info-label">Adviser:</div>
                    <div class="info-value">{{ $student->section->adviser->name }}</div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Class Schedule -->
    <div class="section-title">Class Schedule</div>
    <table class="schedule-table">
        <thead>
            <tr>
                <th style="width: 8%;">No.</th>
                <th style="width: 25%;">Subject</th>
                <th style="width: 22%;">Teacher</th>
                <th style="width: 12%;">Days</th>
                <th style="width: 13%;">Time</th>
                <th style="width: 20%;">Room</th>
            </tr>
        </thead>
        <tbody>
            @forelse($schedules as $index => $schedule)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>
                    <div class="subject-code">{{ $schedule->subject->code ?? 'N/A' }}</div>
                    <div class="subject-name">{{ $schedule->subject->name ?? '' }}</div>
                </td>
                <td>{{ $schedule->teacher->name ?? 'N/A' }}</td>
                <td>{{ implode(', ', $schedule->days ?? []) }}</td>
                <td>
                    {{ \Carbon\Carbon::parse($schedule->time_start)->format('h:i A') }} – 
                    {{ \Carbon\Carbon::parse($schedule->time_end)->format('h:i A') }}
                </td>
                <td>{{ $schedule->room }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 15px; font-style: italic;">
                    No class schedule assigned for this student.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-left">
            <div><strong>Total Subjects:</strong> {{ $schedules->count() }}</div>
            <div style="margin-top: 5px;"><strong>Date Printed:</strong> {{ $dateGenerated }}</div>
            <div style="margin-top: 5px; font-style: italic;">System-generated document</div>
        </div>
    </div>

    <!-- Page Number -->
    <div class="page-number">Page 1 of 1</div>
</div>
