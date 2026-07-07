@extends('layouts.app')

@section('title', 'Fine Record Details')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Fine Record Details</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $student->full_name }}</p>
        </div>
        <a href="{{ route('ssg.fines.index') }}" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-200">
            Back to Fine Records
        </a>
    </div>

    <div class="mb-6 grid gap-6 md:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Student Number</p>
            <p class="mt-1 font-semibold text-gray-800">{{ $student->student_number }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Grade Level</p>
            <p class="mt-1 font-semibold text-gray-800">{{ $student->yearLevel->name ?? 'N/A' }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Section</p>
            <p class="mt-1 font-semibold text-gray-800">{{ $student->section ? 'Section ' . $student->section->name : 'N/A' }}</p>
        </div>
        <div class="rounded-xl border border-green-200 bg-green-50 p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-green-700">Total Outstanding</p>
            <p class="mt-1 text-2xl font-bold text-green-900">PHP {{ number_format($totalOutstanding, 2) }}</p>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <div class="border-b border-gray-100 px-6 py-4">
            <h2 class="text-base font-semibold text-gray-800">Event Fine Details</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-left">Event Name</th>
                        <th class="px-6 py-3 text-left">Event Date</th>
                        <th class="px-6 py-3 text-left">Attendance Status</th>
                        <th class="px-6 py-3 text-left">Fine Amount</th>
                        <th class="px-6 py-3 text-left">Payment Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($fineRecords as $record)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $record->ssgEvent->title ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $record->ssgEvent?->event_date?->format('M d, Y') ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                @if($record->is_present)
                                    <span class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700">Present</span>
                                @else
                                    <span class="rounded-full bg-red-50 px-2.5 py-1 text-xs font-medium text-red-700">Absent</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-700">PHP {{ number_format($record->actual_fine, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700">
                                    {{ $record->payment_status ?? 'Unpaid' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">No fine records found for this student.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-right text-sm font-semibold text-gray-800">Total Outstanding</td>
                        <td colspan="2" class="px-6 py-4 text-sm font-bold text-green-800">PHP {{ number_format($totalOutstanding, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection
