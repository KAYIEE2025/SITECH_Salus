@extends('layouts.app')

@section('title', 'School Year Management')

@section('content')
    @session('success')
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ $value }}
        </div>
    @endsession
    @session('error')
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $value }}
        </div>
    @endsession

    <div class="mb-4 md:mb-6 flex flex-col items-start justify-between gap-3 md:gap-4 sm:flex-row sm:items-center">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-gray-800">School Year Management</h1>
            <p class="mt-1 text-xs md:text-sm text-gray-500">Manage school years and set the active school year.</p>
        </div>
        <a href="{{ route('superadmin.school-years.create') }}"
            class="rounded-lg bg-green-800 px-3 py-1.5 md:px-4 md:py-2 text-xs md:text-sm font-semibold text-white transition hover:bg-green-900 whitespace-nowrap">
            + Add School Year
        </a>
    </div>

    <div class="sa-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs md:text-sm">
                <thead class="bg-gray-50 text-[10px] md:text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-2 py-2 md:px-6 md:py-3 text-left">School Year</th>
                        <th class="px-2 py-2 md:px-6 md:py-3 text-left">Status</th>
                        <th class="px-2 py-2 md:px-6 md:py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($schoolYears as $schoolYear)
                        <tr class="hover:bg-gray-50">
                            <td class="px-2 py-2 md:px-6 md:py-4 font-medium text-gray-800">{{ $schoolYear->name }}</td>
                            <td class="px-2 py-2 md:px-6 md:py-4">
                                @if($schoolYear->is_active)
                                    <span class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700">Active</span>
                                @else
                                    <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">Inactive</span>
                                @endif
                            </td>
                            <td class="px-2 py-2 md:px-6 md:py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    @if(!$schoolYear->is_active)
                                        <form method="POST" action="{{ route('superadmin.school-years.set-active', $schoolYear) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                onclick="return confirm('Set {{ $schoolYear->name }} as the active school year?')"
                                                class="text-xs bg-green-50 hover:bg-green-100 text-green-700 px-3 py-1.5 rounded-lg transition">
                                                Set Active
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('superadmin.school-years.edit', $schoolYear) }}"
                                        class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-600 px-3 py-1.5 rounded-lg transition">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('superadmin.school-years.destroy', $schoolYear) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Are you sure you want to delete this school year?')"
                                            class="text-xs bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-lg transition">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-2 py-4 md:px-6 md:py-8 text-center text-gray-400 text-[10px] md:text-xs">No school years found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
