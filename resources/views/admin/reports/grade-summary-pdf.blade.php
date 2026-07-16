@extends('layouts.pdf', ['orientation' => 'landscape'])

@section('title', 'Grade Summary Report')

@section('content')
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/salus-logo.png') }}" alt="School Logo" class="logo">
            <div class="header-content">
                <h1>SALUS INSTITUTE OF TECHNOLOGY</h1>
                <h2>Student Information System</h2>
                <h3>GRADE SUMMARY REPORT</h3>
            </div>
        </div>

        <div class="report-info">
            <div class="report-info-row">
                <span class="report-info-label">Subject:</span> {{ $subject->code }} - {{ $subject->name }}
            </div>
            <div class="report-info-row">
                <span class="report-info-label">Generated On:</span> {{ now()->format('F d, Y h:i A') }}
            </div>
            <div class="report-info-row">
                <span class="report-info-label">Total Students:</span> {{ $grades->count() }}
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">No.</th>
                    <th style="width: 35%;">Student Name</th>
                    <th style="width: 15%;">Student Number</th>
                    <th style="width: 15%;">Grade</th>
                    <th style="width: 25%;">Remarks</th>
                </tr>
            </thead>
            <tbody>
                @forelse($grades as $index => $grade)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $grade->student->last_name }}, {{ $grade->student->first_name }} {{ $grade->student->middle_name }} {{ $grade->student->suffix }}</td>
                    <td>{{ $grade->student->student_number }}</td>
                    <td class="text-center">{{ $grade->grade }}</td>
                    <td>{{ $grade->remarks ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="no-records">
                        No approved grades found for this subject.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
