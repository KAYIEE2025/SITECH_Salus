@extends('layouts.app')
@section('title', 'Student Records')
@section('content')

    @session('success')
        <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg px-4 py-3 mb-6">
            {{ $value }}
        </div>
    @endsession

    <div class="ra-card overflow-hidden">
        <div class="ra-card-header flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <h2 class="text-base font-semibold text-gray-800">All Students</h2>
            <a href="{{ route('registrar.students.create') }}"
                class="bg-green-800 hover:bg-green-900 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                + Encode Student
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-max text-xs md:text-sm">
                <thead class="bg-gray-50 text-gray-500 text-[10px] md:text-xs">
                    <tr>
                        <th class="text-left px-2 py-2 md:px-6 md:py-3">Student Number</th>
                        <th class="text-left px-2 py-2 md:px-6 md:py-3">Full Name</th>
                        <th class="text-left px-2 py-2 md:px-6 md:py-3">Grade Level & Section</th>
                        <th class="text-left px-2 py-2 md:px-6 md:py-3">Status</th>
                        <th class="text-left px-2 py-2 md:px-6 md:py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($students as $student)
                    <tr class="hover:bg-gray-50">
                        <td class="px-2 py-2 md:px-6 md:py-3 font-medium text-green-800">{{ $student->student_number }}</td>
                        <td class="px-2 py-2 md:px-6 md:py-3 text-gray-800">{{ $student->last_name }}, {{ $student->first_name }} {{ $student->middle_name }}</td>
                        <td class="px-2 py-2 md:px-6 md:py-3 text-gray-500">{{ $student->yearLevel->name ?? '—' }} — Sec {{ $student->section->name ?? '—' }}</td>
                        <td class="px-2 py-2 md:px-6 md:py-3">
                            <span class="bg-green-50 text-green-700 text-[10px] md:text-xs px-2 py-0.5 md:px-2.5 md:py-1 rounded-full">
                                {{ $student->status }}
                            </span>
                        </td>
                        <td class="px-2 py-2 md:px-6 md:py-3">
                            <div class="flex gap-2">
                                <a href="{{ route('registrar.students.edit', $student) }}"
                                    class="text-[10px] md:text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-1 md:px-3 md:py-1.5 rounded-lg transition whitespace-nowrap">
                                    Edit
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-2 py-4 md:px-6 md:py-8 text-center text-gray-400">No students encoded yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $students->links() }}
            </div>
        @endif
    </div>

@endsection
