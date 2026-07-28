@extends('layouts.app')
@section('title', 'Sections')
@section('content')

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg px-4 py-3 mb-6">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3 mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

        <div class="ra-card p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-4">Add Section</h2>

            <form method="POST" action="{{ route('registrar.sections.store') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Grade Level <span class="text-red-500">*</span>
                    </label>
                    <select name="year_level_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                        <option value="">Select grade level...</option>
                        @foreach($yearLevels as $yr)
                            <option value="{{ $yr->id }}" {{ old('year_level_id') == $yr->id ? 'selected' : '' }}>
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
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
                        placeholder="A, B, C, Rizal, Bonifacio...">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Adviser <span class="text-gray-400">(optional)</span>
                    </label>
                    <input type="text" name="adviser" value="{{ old('adviser') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
                        placeholder="Teacher name...">
                </div>

                <button type="submit"
                    class="w-full bg-green-800 hover:bg-green-900 text-white text-sm font-semibold py-2.5 rounded-lg transition">
                    Add Section
                </button>
            </form>
        </div>

        <div class="ra-card overflow-hidden xl:col-span-2">
            <div class="ra-card-header">
                <h2 class="text-base font-semibold text-gray-800">All Sections</h2>
                <p class="text-xs text-gray-500 mt-0.5">Total: {{ $sections->count() }} sections</p>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs">
                    <tr>
                        <th class="text-left px-6 py-3">Grade Level</th>
                        <th class="text-left px-6 py-3">Section</th>
                        <th class="text-left px-6 py-3">Students</th>
                        <th class="text-left px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($sections as $section)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 text-gray-600">{{ $section->yearLevel->name ?? '—' }}</td>
                        <td class="px-6 py-3">
                            <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-1 rounded-full">
                                Section {{ $section->name }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-gray-500">
                            {{ $section->students()->count() }} student(s)
                        </td>
                        <td class="px-6 py-3">
                            @if($section->students()->count() == 0)
                            <form method="POST" action="{{ route('registrar.sections.destroy', $section) }}"
                                onsubmit="return confirm('Delete this section?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="text-xs bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-lg transition">
                                    Delete
                                </button>
                            </form>
                            @else
                                <span class="text-xs text-gray-400">Has students</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                            No sections yet. Add one using the form.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

@endsection
