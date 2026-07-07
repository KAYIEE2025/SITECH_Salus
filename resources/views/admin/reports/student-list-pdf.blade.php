<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student List Report</title>
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
            color: #000000;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 15mm;
            background: white;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }

        .school-name {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .report-title {
            font-size: 14px;
            font-weight: bold;
            margin-top: 10px;
        }

        .report-info {
            margin-bottom: 20px;
            font-size: 11px;
        }

        .report-info p {
            margin-bottom: 3px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #000;
        }

        .table th {
            border: 1px solid #000;
            padding: 8px 10px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: left;
            background: #f0f0f0;
        }

        .table td {
            border: 1px solid #000;
            padding: 8px 10px;
            font-size: 11px;
            vertical-align: top;
        }

        .table tr:nth-child(even) {
            background: #f9f9f9;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <div class="school-name">SALUS INSTITUTE OF TECHNOLOGY</div>
            <div class="report-title">Student List Report</div>
        </div>

        <div class="report-info">
            <p><strong>Grade Level:</strong> {{ $yearLevel ? $yearLevel->name : 'All' }}</p>
            <p><strong>Section:</strong> {{ $section ? $section->yearLevel->name . ' — Section ' . $section->name : 'All' }}</p>
            <p><strong>Date Generated:</strong> {{ now()->format('F d, Y g:i A') }}</p>
            <p><strong>Total Students:</strong> {{ $students->count() }}</p>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th style="width: 10%;">No.</th>
                    <th style="width: 25%;">Student Number</th>
                    <th style="width: 35%;">Name</th>
                    <th style="width: 15%;">Grade Level</th>
                    <th style="width: 15%;">Section</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $index => $student)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $student->student_number }}</td>
                    <td>{{ $student->last_name }}, {{ $student->first_name }} {{ $student->middle_name }} {{ $student->suffix }}</td>
                    <td>{{ $student->yearLevel->name ?? 'N/A' }}</td>
                    <td>{{ $student->section->yearLevel->name ?? '' }} — Sec {{ $student->section->name ?? 'N/A' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px;">
                        No students found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            <p>This document is system-generated.</p>
        </div>
    </div>
</body>
</html>
