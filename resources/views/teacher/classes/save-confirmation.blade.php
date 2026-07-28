@extends('layouts.app')

@section('title', 'Grades Saved')

@section('content')
    <div class="mb-6">
        <a href="{{ route('teacher.classes.grades', $classSchedule) }}" class="text-sm font-medium text-[#1a5c1a] hover:text-green-900">
            Back to Manage Grades
        </a>
    </div>

    <div class="tc-card mb-6 p-6">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-800">Import Completed</h2>
            <p class="mt-2 text-sm text-gray-600">
                Grades have been successfully saved to the database with Draft status.
            </p>
        </div>

        <!-- Success Message -->
        <div class="rounded-lg bg-green-50 border border-green-200 p-4 mb-6">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <p class="text-sm font-medium text-green-800">
                    Grades saved successfully. Status: Draft
                </p>
            </div>
        </div>

        <!-- Import Summary -->
        @if($saveSummary)
            <div class="rounded-lg bg-gray-50 border border-gray-200 p-4 mb-6">
                <h3 class="text-sm font-medium text-gray-800 mb-3">Import Summary</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Imported Students:</p>
                        <p class="font-medium text-gray-800">{{ $saveSummary['total_students'] }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Successfully Saved:</p>
                        <p class="font-medium text-green-700">{{ $saveSummary['saved_count'] }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Updated:</p>
                        <p class="font-medium text-blue-700">{{ $saveSummary['updated_count'] }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Skipped:</p>
                        <p class="font-medium text-orange-600">{{ $saveSummary['skipped_count'] }}</p>
                    </div>
                </div>
                @if($saveSummary['error_count'] > 0)
                    <div class="mt-3">
                        <p class="text-gray-500">Errors:</p>
                        <p class="font-medium text-red-600">{{ $saveSummary['error_count'] }}</p>
                    </div>
                @endif
            </div>
        @endif

        <!-- Warnings -->
        @if($saveSummary && !empty($saveSummary['warnings']))
            <div class="rounded-lg bg-yellow-50 border border-yellow-200 p-4 mb-6">
                <h3 class="text-sm font-medium text-yellow-800 mb-2">Warnings</h3>
                <ul class="text-sm text-yellow-700 space-y-1">
                    @foreach($saveSummary['warnings'] as $warning)
                        <li>{{ $warning }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Information -->
        <div class="rounded-lg bg-blue-50 border border-blue-200 p-4 mb-6">
            <h3 class="text-sm font-medium text-blue-800 mb-2">Important Information</h3>
            <ul class="text-sm text-blue-700 space-y-1">
                <li>• Grades are saved as <strong>Draft</strong> status</li>
                <li>• Students cannot view Draft grades</li>
                <li>• You can still modify Draft grades before submission</li>
                <li>• Submit grades to the Registrar when ready for approval</li>
            </ul>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between">
            <a href="{{ route('teacher.classes.grades', $classSchedule) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                Back to Manage Grades
            </a>
            <div class="ml-auto">
                <button type="button" class="px-6 py-2 bg-gray-400 text-white text-sm font-medium rounded-lg cursor-not-allowed" disabled>
                    Continue to Submit Grades (Phase 5)
                </button>
            </div>
        </div>
    </div>

    <!-- Phase 4 Status -->
    <div class="rounded-lg bg-blue-50 border border-blue-200 p-4">
        <h3 class="text-sm font-medium text-blue-800 mb-2">Phase 4 Status</h3>
        <ul class="text-sm text-blue-700 space-y-1">
            <li>✓ Save imported grades to database</li>
            <li>✓ Set status to Draft for all saved grades</li>
            <li>✓ Update existing Draft records</li>
            <li>✓ Prevent duplicate records</li>
            <li>✓ Prevent editing Submitted/Approved grades</li>
            <li>✓ Import summary (saved, skipped, updated, errors)</li>
            <li>✓ Save confirmation page</li>
            <li>✓ Back to Manage Grades button</li>
            <li>○ Grade submission (Phase 5)</li>
            <li>○ Registrar approval (Phase 5)</li>
        </ul>
    </div>
@endsection
