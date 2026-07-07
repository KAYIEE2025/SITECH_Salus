<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grade Report</title>
    <style>
        body {
            color: #111827;
            font-family: Arial, sans-serif;
            margin: 32px;
        }

        .header {
            border-bottom: 2px solid #1a5c1a;
            margin-bottom: 24px;
            padding-bottom: 16px;
            text-align: center;
        }

        .header h1 {
            font-size: 20px;
            margin: 0;
        }

        .header p,
        .meta p {
            color: #4b5563;
            font-size: 13px;
            margin: 4px 0;
        }

        .meta {
            display: grid;
            gap: 6px;
            margin-bottom: 20px;
        }

        .section-header {
            background: #f0fdf4;
            border: 1px solid #16a34a;
            border-radius: 8px;
            margin: 24px 0 16px 0;
            padding: 12px 16px;
        }

        .section-header h3 {
            color: #166534;
            font-size: 16px;
            margin: 0;
        }

        table {
            border-collapse: collapse;
            font-size: 13px;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 10px;
        }

        th {
            background: #f3f4f6;
            text-align: left;
        }

        .center {
            text-align: center;
        }

        .empty {
            border: 1px solid #d1d5db;
            color: #4b5563;
            padding: 24px;
            text-align: center;
        }

        .gwa-box {
            background: #f9fafb;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            margin-top: 16px;
            padding: 12px 16px;
        }

        .gwa-box .label {
            font-size: 14px;
            font-weight: 600;
        }

        .gwa-box .value {
            font-size: 18px;
            font-weight: 700;
        }

        .actions {
            margin-bottom: 20px;
            text-align: right;
        }

        .actions button {
            background: #1a5c1a;
            border: 0;
            border-radius: 4px;
            color: white;
            cursor: pointer;
            font-size: 13px;
            padding: 9px 14px;
        }

        @media print {
            body {
                margin: 0;
            }

            .actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="actions">
        <button type="button" onclick="window.print()">Print Report</button>
        <button type="button" onclick="window.close()">Close</button>
    </div>

    <div class="header">
        <h1>Salus Institute of Technology</h1>
        <p>Student Grade Report</p>
    </div>

    <div class="meta">
        <p><strong>Student:</strong> {{ $student?->full_name ?? auth()->user()->name }}</p>
        <p><strong>Student Number:</strong> {{ $student?->student_number ?? 'N/A' }}</p>
        <p><strong>Date Printed:</strong> {{ now()->format('F d, Y - g:i A') }}</p>
    </div>

    @if($groupedGrades->isEmpty())
        <div class="empty">
            Your grades are not yet available. Please wait for the Registrar's approval.
        </div>
    @else
        @foreach($groupedGrades as $group)
            <div class="section-header">
                <h3>{{ $group['school_year'] }} — {{ $group['semester'] }} Semester</h3>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Subject Code</th>
                        <th>Subject Name</th>
                        <th class="center">Final Grade</th>
                        <th class="center">Remarks</th>
                        <th>Date Approved</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($group['grades'] as $grade)
                        <tr>
                            <td>{{ $grade->classSchedule->subject->code ?? 'N/A' }}</td>
                            <td>{{ $grade->classSchedule->subject->name ?? 'N/A' }}</td>
                            <td class="center">{{ $grade->final_grade }}</td>
                            <td class="center">{{ $grade->remarks }}</td>
                            <td>{{ $grade->reviewed_at ? $grade->reviewed_at->format('M d, Y') : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="gwa-box">
                <div class="flex justify-between items-center">
                    <span class="label">General Weighted Average (GWA):</span>
                    <span class="value">GWA not yet available.</span>
                </div>
            </div>
        @endforeach
    @endif
</body>
</html>
