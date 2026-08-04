@extends('layouts.app')

@section('title', 'Add School Year')

@section('content')
    <div class="mb-4 md:mb-6 flex flex-col items-start justify-between gap-3 md:gap-4 sm:flex-row sm:items-center">
        <h1 class="text-xl md:text-2xl font-bold text-gray-800">Add School Year</h1>
        <a href="{{ route('superadmin.school-years.index') }}"
            class="text-xs md:text-sm text-gray-600 hover:text-gray-800 whitespace-nowrap">
            ← Back to School Years
        </a>
    </div>

    <div class="sa-card p-4 md:p-6">
        <form method="POST" action="{{ route('superadmin.school-years.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">
                    School Year Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}"
                    class="w-full border border-gray-300 rounded-lg px-2 py-1.5 md:px-3 md:py-2 text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
                    placeholder="e.g., 2024-2025">
                @error('name') <p class="text-red-500 text-[10px] md:text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex flex-col md:flex-row gap-2 md:gap-3">
                <button type="submit"
                    class="flex-1 bg-green-800 hover:bg-green-900 text-white text-xs md:text-sm font-medium py-2.5 rounded-lg transition min-h-[48px] flex items-center justify-center">
                    Add School Year
                </button>
                <a href="{{ route('superadmin.school-years.index') }}"
                    class="px-4 md:px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs md:text-sm font-medium rounded-lg transition min-h-[48px] flex items-center justify-center whitespace-nowrap">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
