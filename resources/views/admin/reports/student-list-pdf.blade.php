@extends('layouts.pdf', ['orientation' => 'portrait'])

@section('title', 'Student List Report')

@section('content')
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/salus-logo.png') }}" alt="School Logo" class="logo">
            <div class="header-content">
                <h1>SALUS INSTITUTE OF TECHNOLOGY</h1>
                <h2>Student Information System</h2>
                <h3>STUDENT LIST REPORT</h3>
            </div>
        </div>

        <div class="report-info">
            <div class="report-info-row">
                <span class="report-info-label">Grade Level:</span> {{ $yearLevel ? $yearLevel->name : 'All' }}
            </div>
            <div class="report-info-row">
                <span class="report-info-label">Section:</span> {{ $section ? $section->yearLevel->name . ' — Section ' . $section->name : 'All' }}
            </div>
            <div class="report-info-row">
                <span class="report-info-label">Generated On:</span> {{ now()->format('F d, Y h:i A') }}
            </div>
            <div class="report-info-row">
                <span class="report-info-label">Total Students:</span> {{ $students->count() }}
            </div>
        </div>

        <table>
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
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $student->student_number }}</td>
                    <td>{{ $student->last_name }}, {{ $student->first_name }} {{ $student->middle_name }} {{ $student->suffix }}</td>
                    <td>{{ $student->yearLevel->name ?? 'N/A' }}</td>
                    <td>{{ $student->section->yearLevel->name ?? '' }} — Sec {{ $student->section->name ?? 'N/A' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="no-records">
                        No students found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
