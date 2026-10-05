@extends('layouts.app')
@section('title', 'Edit Section')
@section('content')
    @session('success')
        <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg px-4 py-3 mb-6">
            {{ $value }}
        </div>
    @endsession
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3 mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="max-w-2xl mx-auto">
        <div class="ra-card p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-4">Edit Section</h2>

            <form method="POST" action="{{ route('registrar.sections.update', $section) }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Grade Level <span class="text-red-500">*</span>
                    </label>
                    <select name="year_level_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                        <option value="">Select grade level...</option>
                        @foreach($yearLevels as $yr)
                            <option value="{{ $yr->id }}" {{ old('year_level_id', $section->year_level_id) == $yr->id ? 'selected' : '' }}>
                                {{ $yr->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('year_level_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Section Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $section->name) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
                        placeholder="A, B, C, Rizal, Bonifacio..."
                        maxlength="50">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Adviser <span class="text-red-500">*</span>
                    </label>
                    <select name="adviser_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                        <option value="">Select adviser...</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old('adviser_id', $section->adviser_id) == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('adviser_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-3">
                    <button type="submit"
                        class="flex-1 bg-green-800 hover:bg-green-900 text-white text-sm font-semibold py-2.5 rounded-lg transition">
                        Save Changes
                    </button>
                    <a href="{{ route('registrar.sections') }}"
                        class="px-6 py-2.5 border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

@endsection
