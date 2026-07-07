@extends('layouts.app')
@section('title', 'Class List')
@section('content')
    <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">My Classes</h2>
            <p class="mt-1 text-sm text-gray-500">Sections are grouped below. Open a section to manage grades by subject.</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm text-gray-600">
            {{ $sectionGroups->sum(fn ($group) => $group['classes']->count()) }} assigned subject(s)
        </div>
    </div>

    @if($sectionGroups->isEmpty())
        <div class="rounded-xl border border-gray-200 bg-white px-6 py-12 text-center">
            <p class="text-sm text-gray-500">No classes assigned yet.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($sectionGroups as $group)
                @php
                    $section = $group['section'];
                    $classes = $group['classes'];
                @endphp

                <details class="group overflow-hidden rounded-xl border border-gray-200 bg-white" @if($loop->first) open @endif>
                    <summary class="flex cursor-pointer list-none flex-col gap-3 px-5 py-4 transition hover:bg-gray-50 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-base font-semibold text-gray-800">
                                    {{ optional($section?->yearLevel)->name ?? 'No grade level' }} - {{ $section->name ?? 'Unassigned Section' }}
                                </h3>
                                <span class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700">
                                    {{ $classes->count() }} subject(s)
                                </span>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ $group['student_count'] }} enrolled student(s)
                            </p>
                        </div>
                        <div class="flex items-center gap-3 text-sm font-medium text-[#1a5c1a]">
                            <span class="group-open:hidden">Show subjects</span>
                            <span class="hidden group-open:inline">Hide subjects</span>
                            <span class="text-lg leading-none transition group-open:rotate-180">v</span>
                        </div>
                    </summary>

                    <div class="border-t border-gray-100">
                        <div class="hidden grid-cols-12 gap-4 bg-gray-50 px-5 py-3 text-xs font-medium uppercase text-gray-500 md:grid">
                            <div class="col-span-4">Subject</div>
                            <div class="col-span-2">Schedule</div>
                            <div class="col-span-2">Term</div>
                            <div class="col-span-2">Room</div>
                            <div class="col-span-2 text-right">Action</div>
                        </div>

                        <div class="divide-y divide-gray-100">
                            @foreach($classes as $class)
                                <div class="grid gap-4 px-5 py-4 md:grid-cols-12 md:items-center">
                                    <div class="md:col-span-4">
                                        <p class="font-medium text-gray-800">
                                            {{ $class->subject->name ?? 'No subject assigned' }}
                                        </p>
                                        <p class="mt-1 text-xs text-gray-500">
                                            {{ $class->subject->code ?? 'No subject code' }}
                                        </p>
                                    </div>

                                    <div class="text-sm text-gray-600 md:col-span-2">
                                        <span class="font-medium text-gray-500 md:hidden">Schedule: </span>
                                        {{ is_array($class->days) ? implode(', ', $class->days) : ($class->days ?? 'No days') }}
                                        <div class="text-xs text-gray-500">
                                            {{ $class->time_start ?? 'No start' }} - {{ $class->time_end ?? 'No end' }}
                                        </div>
                                    </div>

                                    <div class="text-sm text-gray-600 md:col-span-2">
                                        <span class="font-medium text-gray-500 md:hidden">Term: </span>
                                        {{ $class->school_year }}
                                        <div class="text-xs text-gray-500">{{ $class->semester }} Semester</div>
                                    </div>

                                    <div class="text-sm text-gray-600 md:col-span-2">
                                        <span class="font-medium text-gray-500 md:hidden">Room: </span>
                                        {{ $class->room ?? 'No room assigned' }}
                                    </div>

                                    <div class="md:col-span-2 md:text-right">
                                        <a href="{{ route('teacher.classes.grades', $class) }}"
                                           class="inline-flex w-full items-center justify-center rounded-lg bg-[#1a5c1a] px-3 py-2 text-sm font-semibold text-white transition hover:bg-green-900 sm:w-auto">
                                            Manage Grades
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </details>
            @endforeach
        </div>
    @endif
@endsection
