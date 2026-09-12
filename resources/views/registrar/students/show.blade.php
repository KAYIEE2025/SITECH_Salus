@extends('layouts.app')
@section('title', 'Student Profile')
@section('content')

    @session('success')
        <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg px-4 py-3 mb-6">
            {{ $value }}
        </div>
    @endsession

    <div class="ra-card p-6 sm:p-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-base font-semibold text-gray-800">Student Profile</h2>
            <div class="flex gap-2">
                <a href="{{ route('registrar.students.edit', $student) }}" 
                    class="text-sm text-green-700 hover:text-green-800 font-medium">
                    Edit Profile
                </a>
                <a href="{{ route('registrar.students') }}" 
                    class="text-sm text-gray-600 hover:text-gray-800 font-medium">
                    ← Back to Students
                </a>
            </div>
        </div>

        {{-- Student Information --}}
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 mb-8">
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Personal Information</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Student Number:</span>
                        <span class="font-medium text-gray-800">{{ $student->student_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Full Name:</span>
                        <span class="font-medium text-gray-800">{{ $student->full_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Gender:</span>
                        <span class="text-gray-800">{{ $student->gender ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Date of Birth:</span>
                        <span class="text-gray-800">{{ $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('M d, Y') : '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Contact Number:</span>
                        <span class="text-gray-800">{{ $student->contact_number ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Email:</span>
                        <span class="text-gray-800">{{ $student->email ?? '—' }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Account Information</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Status:</span>
                        <span class="bg-green-50 text-green-700 text-xs px-2 py-0.5 rounded-full">
                            {{ $student->status }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Has Account:</span>
                        <span class="font-medium {{ $student->user_id ? 'text-green-700' : 'text-gray-500' }}">
                            {{ $student->user_id ? 'Yes' : 'No' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Encoded By:</span>
                        <span class="text-gray-800">{{ $student->encoder->name ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Encoded At:</span>
                        <span class="text-gray-800">{{ $student->encoded_at ? \Carbon\Carbon::parse($student->encoded_at)->format('M d, Y g:i A') : '—' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Current Enrollment --}}
        <div class="bg-blue-50 rounded-lg p-4 mb-8">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Current Enrollment</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <span class="text-gray-500 block text-xs">School Year</span>
                    <span class="font-medium text-gray-800">{{ $student->school_year ?? '—' }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block text-xs">Term</span>
                    <span class="font-medium text-gray-800">{{ $student->term ?? '—' }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block text-xs">Grade Level</span>
                    <span class="font-medium text-gray-800">{{ $student->yearLevel->name ?? '—' }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block text-xs">Section</span>
                    <span class="font-medium text-gray-800">{{ $student->section->name ?? '—' }}</span>
                </div>
            </div>
        </div>

        {{-- Enrollment History --}}
        <div class="border-t border-gray-100 pt-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Enrollment History</h3>
            @if($student->enrollments && $student->enrollments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead class="bg-gray-50 text-gray-500">
                            <tr>
                                <th class="text-left px-3 py-2">School Year</th>
                                <th class="text-left px-3 py-2">Term</th>
                                <th class="text-left px-3 py-2">Grade Level</th>
                                <th class="text-left px-3 py-2">Section</th>
                                <th class="text-left px-3 py-2">Status</th>
                                <th class="text-left px-3 py-2">Encoded</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($student->enrollments->sortByDesc('school_year')->sortByDesc('term') as $enrollment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 font-medium text-gray-800">{{ $enrollment->school_year }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ $enrollment->term ?? '—' }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ $enrollment->yearLevel->name ?? '—' }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ $enrollment->section->name ?? '—' }}</td>
                                <td class="px-3 py-2">
                                    <span class="bg-green-50 text-green-700 text-[10px] px-2 py-0.5 rounded-full">
                                        {{ $enrollment->status }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-gray-500">{{ $enrollment->encoded_at ? \Carbon\Carbon::parse($enrollment->encoded_at)->format('M d, Y') : '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-gray-500">No enrollment history available.</p>
            @endif
        </div>

    </div>

@endsection