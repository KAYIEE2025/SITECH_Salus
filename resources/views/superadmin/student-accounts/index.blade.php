@extends('layouts.app')

@section('title', 'Pending Student Accounts')

@section('content')
    @session('success')
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ $value }}
        </div>
    @endsession

    @if(session('error'))
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    @if(session('generatedCredentials'))
        <div class="mb-6 overflow-hidden rounded-2xl border border-yellow-200 bg-gradient-to-br from-yellow-50 to-amber-50">
            <div class="border-b border-yellow-100 px-6 py-4">
                <h2 class="text-base font-semibold text-yellow-900">Generated Credentials</h2>
                <p class="mt-1 text-xs text-yellow-700">Temporary passwords are shown once. Record them before leaving this page.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-yellow-100/60 text-xs text-yellow-800">
                        <tr>
                            <th class="px-6 py-3 text-left">Student</th>
                            <th class="px-6 py-3 text-left">Student No.</th>
                            <th class="px-6 py-3 text-left">Username</th>
                            <th class="px-6 py-3 text-left">Email</th>
                            <th class="px-6 py-3 text-left">Temporary Password</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-yellow-100">
                        @foreach(session('generatedCredentials') as $credential)
                            <tr>
                                <td class="px-6 py-3 font-medium text-gray-800">{{ $credential['student_name'] }}</td>
                                <td class="px-6 py-3 text-gray-600">{{ $credential['student_number'] }}</td>
                                <td class="px-6 py-3 font-mono text-gray-800">{{ $credential['username'] }}</td>
                                <td class="px-6 py-3 text-gray-600">{{ $credential['email'] }}</td>
                                <td class="px-6 py-3 font-mono text-gray-900">{{ $credential['temporary_password'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="sa-card overflow-hidden">
        <div class="flex flex-col items-start justify-between gap-4 border-b border-green-50 px-5 py-4 sm:flex-row sm:items-center sm:px-6">
            <div>
                <h2 class="text-base font-semibold text-gray-800">Students Awaiting Account Creation</h2>
                <p class="mt-1 text-xs text-gray-500">Accounts use the student number as the default username.</p>
            </div>
            <form id="bulk-student-account-form" method="POST" action="{{ route('superadmin.student-accounts.bulk-store') }}">
                @csrf
                <button type="submit" class="rounded-lg bg-[#1a5c1a] px-4 py-2 text-sm font-semibold text-white transition hover:bg-green-900">
                    Generate Selected
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500">
                    <tr>
                        <th class="w-12 px-6 py-3 text-left">
                            <input type="checkbox" class="rounded border-gray-300 text-[#1a5c1a] focus:ring-[#1a5c1a]" onclick="document.querySelectorAll('[data-student-account-checkbox]').forEach((checkbox) => checkbox.checked = this.checked)">
                        </th>
                        <th class="px-6 py-3 text-left">Student</th>
                        <th class="px-6 py-3 text-left">Student No.</th>
                        <th class="px-6 py-3 text-left">Section</th>
                        <th class="px-6 py-3 text-left">Contact</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pendingStudents as $student)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3">
                                <input form="bulk-student-account-form" data-student-account-checkbox type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="rounded border-gray-300 text-[#1a5c1a] focus:ring-[#1a5c1a]">
                            </td>
                            <td class="px-6 py-3">
                                <p class="font-medium text-gray-800">{{ $student->last_name }}, {{ $student->first_name }} {{ $student->middle_name }}</p>
                                <p class="text-xs text-gray-500">{{ $student->email ?? 'No email provided' }}</p>
                            </td>
                            <td class="px-6 py-3 font-mono text-gray-700">{{ $student->student_number }}</td>
                            <td class="px-6 py-3 text-gray-500">
                                {{ optional($student->yearLevel)->name ?? 'No year level' }} / {{ optional($student->section)->name ?? 'No section' }}
                            </td>
                            <td class="px-6 py-3 text-gray-500">{{ $student->contact_number ?? 'Not provided' }}</td>
                            <td class="px-6 py-3">
                                <span class="rounded-full bg-yellow-50 px-2.5 py-1 text-xs font-medium text-yellow-700">
                                    {{ $student->status }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <form method="POST" action="{{ route('superadmin.student-accounts.store', $student) }}">
                                    @csrf
                                    <button type="submit" class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-700 transition hover:bg-gray-200">
                                        Generate
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-400">
                                No students are pending account creation.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pendingStudents->hasPages())
            <div class="border-t border-gray-100 px-6 py-4">
                {{ $pendingStudents->links() }}
            </div>
        @endif
    </div>
@endsection
