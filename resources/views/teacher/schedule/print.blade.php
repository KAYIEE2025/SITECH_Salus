<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Teaching Schedule - {{ $teacher->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            line-height: 1.5;
            background-color: #ffffff;
            color: #000000;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 12mm 15mm;
            background: white;
            position: relative;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .logo {
            height: 50px;
            margin-bottom: 8px;
        }

        .school-name {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 3px;
        }

        .school-address {
            font-size: 10px;
            margin-bottom: 8px;
        }

        .document-title {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 8px;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 15px 0 8px 0;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
        }

        .info-box {
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 15px;
        }

        .info-row {
            display: flex;
            margin-bottom: 5px;
        }

        .info-row:last-child {
            margin-bottom: 0;
        }

        .info-label {
            font-weight: bold;
            width: 120px;
            flex-shrink: 0;
        }

        .info-value {
            flex: 1;
            border-bottom: 1px dotted #000;
            padding-left: 5px;
        }

        .info-col {
            flex: 1;
        }

        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            border: 1px solid #000;
        }

        .schedule-table th {
            border: 1px solid #000;
            padding: 6px 5px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            background: #f0f0f0;
        }

        .schedule-table td {
            border: 1px solid #000;
            padding: 6px 5px;
            font-size: 10px;
            vertical-align: top;
        }

        .footer {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .footer-left {
            font-size: 10px;
        }

        .footer-right {
            text-align: center;
        }

        .signature-box {
            width: 180px;
            margin-top: 30px;
        }

        .signature-line {
            border-top: 1px solid #000;
            padding-top: 3px;
            font-size: 10px;
            font-weight: bold;
        }

        .date-box {
            margin-top: 15px;
            font-size: 10px;
        }

        .page-number {
            position: absolute;
            bottom: 12mm;
            right: 15mm;
            font-size: 10px;
        }

        .action-buttons {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }

        .btn {
            padding: 10px 20px;
            border: 1px solid #000;
            background: white;
            cursor: pointer;
            font-size: 12px;
            font-family: Arial, sans-serif;
        }

        .btn:hover {
            background: #f0f0f0;
        }

        @media print {
            body {
                background: white;
            }

            .page {
                box-shadow: none;
                margin: 0;
                padding: 12mm 15mm;
                width: 100%;
            }

            .action-buttons {
                display: none;
            }

            .schedule-table th {
                background: #f0f0f0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            @page {
                size: A4 portrait;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="action-buttons">
        <button onclick="window.print()" class="btn">Print PDF</button>
        <button onclick="window.close()" class="btn">Close</button>
    </div>

    <div class="page">
        <div class="header">
            @if(file_exists(public_path('images/salus-logo.png')))
                <img src="{{ $schoolLogo }}" alt="School Logo" class="logo">
            @endif
            <div class="school-name">SALUS INSTITUTE OF TECHNOLOGY</div>
            <div class="school-address">Student Information System</div>
            <div class="document-title">My Teaching Schedule</div>
        </div>

        <div class="section-title">Teacher Information</div>
        <div class="info-box">
            <div class="info-row">
                <div class="info-col">
                    <div class="info-row">
                        <div class="info-label">Teacher Name:</div>
                        <div class="info-value">{{ $teacher->name }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Employee ID:</div>
                        <div class="info-value">{{ $teacher->username ?: 'EMP-' . str_pad($teacher->id, 4, '0', STR_PAD_LEFT) }}</div>
                    </div>
                </div>
                <div class="info-col">
                    <div class="info-row">
                        <div class="info-label">School Year:</div>
                        <div class="info-value">{{ $schoolYear ?? 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Semester:</div>
                        <div class="info-value">{{ $semester ? $semester . ' Semester' : 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-title">Assigned Classes</div>
        <table class="schedule-table">
            <thead>
                <tr>
                    <th style="width: 13%;">Subject Code</th>
                    <th style="width: 22%;">Subject</th>
                    <th style="width: 13%;">Grade</th>
                    <th style="width: 13%;">Section</th>
                    <th style="width: 11%;">Day</th>
                    <th style="width: 16%;">Time</th>
                    <th style="width: 12%;">Room</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schedules as $schedule)
                    <tr>
                        <td>{{ $schedule->subject->code ?? 'N/A' }}</td>
                        <td>{{ $schedule->subject->name ?? 'No subject assigned' }}</td>
                        <td>{{ $schedule->section?->yearLevel?->name ?? 'N/A' }}</td>
                        <td>Section {{ $schedule->section->name ?? 'N/A' }}</td>
                        <td>{{ implode(', ', $schedule->days ?? []) }}</td>
                        <td>
                            {{ \Carbon\Carbon::parse($schedule->time_start)->format('h:i A') }} -
                            {{ \Carbon\Carbon::parse($schedule->time_end)->format('h:i A') }}
                        </td>
                        <td>{{ $schedule->room ?: 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 15px; font-style: italic;">
                            No assigned classes for the selected school year and semester.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            <div class="footer-left">
                <div><strong>Total Classes Handled:</strong> {{ $schedules->count() }}</div>
                <div style="margin-top: 5px;"><strong>Date Printed:</strong> {{ $dateGenerated }}</div>
                <div style="margin-top: 5px; font-style: italic;">System-generated document</div>
            </div>
            <div class="footer-right">
                <div class="signature-box">
                    <div class="signature-line">TEACHER</div>
                </div>
                <div class="date-box">
                    Over Printed Name
                </div>
            </div>
        </div>

        <div class="page-number">Page 1 of 1</div>
    </div>
</body>
</html>
