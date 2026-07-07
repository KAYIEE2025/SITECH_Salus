@extends('layouts.app')
@section('title', 'Student Dashboard')
@section('content')
    @if(!$student)
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center">
            <h2 class="text-xl font-semibold text-yellow-800 mb-2">Student Profile Not Found</h2>
            <p class="text-yellow-700">Your student profile has not been encoded yet. Please contact the Registrar's office to complete your profile setup.</p>
        </div>
    @else
        <!-- Dashboard Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <p class="text-sm text-gray-500 mb-1">Total Subjects Enrolled</p>
                <p class="text-3xl font-bold text-green-800">{{ $totalSubjects }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <p class="text-sm text-gray-500 mb-1">Approved Grades</p>
                <p class="text-3xl font-bold text-green-800">{{ $approvedGrades }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <p class="text-sm text-gray-500 mb-1">Outstanding Fine Balance</p>
                <p class="text-3xl font-bold {{ $outstandingFineBalance > 0 ? 'text-red-600' : 'text-green-800' }}">
                    ₱{{ number_format($outstandingFineBalance, 2) }}
                </p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <p class="text-sm text-gray-500 mb-1">Upcoming SSG Events</p>
                <p class="text-3xl font-bold text-green-800">{{ $upcomingSsgEvents->count() }}</p>
            </div>
        </div>

        <!-- Student Profile Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Student Information -->
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-6">Student Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Student Number</p>
                        <p class="text-base font-medium text-gray-800">{{ $student->student_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Student Name</p>
                        <p class="text-base font-medium text-gray-800">{{ $student->last_name }}, {{ $student->first_name }} {{ $student->middle_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Grade Level</p>
                        <p class="text-base font-medium text-gray-800">{{ $student->yearLevel->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Section</p>
                        <p class="text-base font-medium text-gray-800">{{ $student->section->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">School Year</p>
                        <p class="text-base font-medium text-gray-800">{{ $student->school_year }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Semester</p>
                        <p class="text-base font-medium text-gray-800">{{ $student->semester }}</p>
                    </div>
                </div>
            </div>

            <!-- QR Code -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-6">QR Code</h2>
                @if($student && $student->qr_code_path)
                    <img src="{{ asset('storage/' . $student->qr_code_path) }}"
                        alt="Student QR Code"
                        width="220">
                @else
                    <p>No QR Code Available</p>
                @endif
            </div>
        </div>

        <!-- Upcoming SSG Events -->
        @if($upcomingSsgEvents->isNotEmpty())
            <div class="mt-6 bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Upcoming SSG Events</h2>
                <div class="space-y-3">
                    @foreach($upcomingSsgEvents as $event)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-800">{{ $event->title }}</p>
                                <p class="text-sm text-gray-500">{{ $event->event_date->format('M d, Y - g:i A') }}</p>
                            </div>
                            <span class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded-full">
                                {{ $event->event_date->diffForHumans() }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif
@endsection