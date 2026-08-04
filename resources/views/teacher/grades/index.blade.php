@extends('layouts.app')
@section('title', 'Grade Management')
@section('content')
    @php
        $selectedSchoolYear = old('school_year', $classSchedule->school_year ?? $activeSchoolYear ?? '');
        $selectedSectionId = old('section_id', $classSchedule->section_id ?? '');
        $selectedClassScheduleId = old('class_schedule_id', $classSchedule->id ?? '');
    @endphp

    <div class="tc-card mb-4 md:mb-6 p-4 md:p-6">
        <h2 class="text-base md:text-lg font-semibold text-gray-800 mb-4 md:mb-6">Grade Management</h2>

        <!-- Selection Form -->
        <div class="bg-gray-50 rounded-lg p-4 md:p-6 mb-4 md:mb-6">
            <h3 class="font-medium text-gray-800 mb-3 md:mb-4 text-sm md:text-base">Select Class to Manage Grades</h3>
            <form action="{{ route('teacher.grades.select') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-4">
                @csrf
                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">School Year</label>
                    @if($activeSchoolYear)
                        <select id="school_year" name="school_year" class="w-full border border-gray-300 rounded px-2 py-1.5 md:px-3 md:py-2 text-xs md:text-sm focus:ring-green-500 focus:border-green-500" required>
                            <option value="">Select...</option>
                            <option value="2024-2025" @selected($selectedSchoolYear === '2024-2025')>2024-2025</option>
                            <option value="2025-2026" @selected($selectedSchoolYear === '2025-2026')>2025-2026</option>
                            <option value="2026-2027" @selected($selectedSchoolYear === '2026-2027')>2026-2027</option>
                            <option value="2027-2028" @selected($selectedSchoolYear === '2027-2028')>2027-2028</option>
                        </select>
                    @else
                        <select id="school_year" name="school_year" class="w-full border border-red-300 rounded px-2 py-1.5 md:px-3 md:py-2 text-xs md:text-sm focus:ring-red-500 focus:border-red-500" required>
                            <option value="">Select...</option>
                            <option value="2024-2025" @selected($selectedSchoolYear === '2024-2025')>2024-2025</option>
                            <option value="2025-2026" @selected($selectedSchoolYear === '2025-2026')>2025-2026</option>
                            <option value="2026-2027" @selected($selectedSchoolYear === '2026-2027')>2026-2027</option>
                            <option value="2027-2028" @selected($selectedSchoolYear === '2027-2028')>2027-2028</option>
                        </select>
                        <p class="text-red-500 text-xs mt-1">No active school year set. Please contact Super Admin to set an active school year.</p>
                    @endif
                </div>
                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">Section</label>
                    <select id="section_id" name="section_id" class="w-full border border-gray-300 rounded px-2 py-1.5 md:px-3 md:py-2 text-xs md:text-sm focus:ring-green-500 focus:border-green-500" required disabled>
                        <option value="">Select School Year First</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">Subject</label>
                    <select id="class_schedule_id" name="class_schedule_id" class="w-full border border-gray-300 rounded px-2 py-1.5 md:px-3 md:py-2 text-xs md:text-sm focus:ring-green-500 focus:border-green-500" required disabled>
                        <option value="">Select Section First</option>
                    </select>
                </div>
                <div class="md:col-span-3">
                    <button id="continue_button" type="submit" class="w-full md:w-auto px-4 py-1.5 md:px-6 md:py-2 bg-green-700 text-white text-xs md:text-sm rounded hover:bg-green-800 transition disabled:bg-gray-300 disabled:cursor-not-allowed whitespace-nowrap" disabled>
                        Continue to Upload
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Upload Section (Hidden until selection) -->
    @if(isset($classSchedule))
    {{-- Grade Submission Status Card --}}
    @if(isset($submissionStatus))
        <div class="tc-card mb-4 md:mb-6 p-4 md:p-6">
            <div class="flex items-start gap-2 md:gap-4">
                <div class="flex-shrink-0">
                    @if($submissionStatus['status'] === 'open')
                        <div class="flex h-10 w-10 md:h-12 md:w-12 items-center justify-center rounded-full bg-green-100">
                            <svg class="h-5 w-5 md:h-6 md:w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    @elseif($submissionStatus['status'] === 'closed')
                        <div class="flex h-10 w-10 md:h-12 md:w-12 items-center justify-center rounded-full bg-red-100">
                            <svg class="h-5 w-5 md:h-6 md:w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                    @else
                        <div class="flex h-10 w-10 md:h-12 md:w-12 items-center justify-center rounded-full bg-yellow-100">
                            <svg class="h-5 w-5 md:h-6 md:w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    @endif
                </div>
                <div class="flex-1">
                    <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-0.5 md:mb-1">Grade Submission Status</h3>
                    <div class="flex items-center gap-2 mb-2 md:mb-3 flex-wrap">
                        @if($submissionStatus['status'] === 'open')
                            <span class="inline-flex items-center px-2 py-0.5 md:px-2.5 md:py-0.5 rounded-full text-[10px] md:text-xs font-medium bg-green-100 text-green-800">
                                🟢 OPEN
                            </span>
                        @elseif($submissionStatus['status'] === 'closed')
                            <span class="inline-flex items-center px-2 py-0.5 md:px-2.5 md:py-0.5 rounded-full text-[10px] md:text-xs font-medium bg-red-100 text-red-800">
                                🔴 CLOSED
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 md:px-2.5 md:py-0.5 rounded-full text-[10px] md:text-xs font-medium bg-yellow-100 text-yellow-800">
                                🟡 SCHEDULED
                            </span>
                        @endif
                        <span class="text-xs md:text-sm text-gray-600">{{ $submissionStatus['message'] }}</span>
                    </div>
                    <div class="grid grid-cols-1 gap-1.5 md:gap-2 text-xs md:text-sm">
                        <div class="flex items-center gap-1.5 md:gap-2">
                            <span class="text-gray-500 w-24 md:w-32">School Year:</span>
                            <span class="font-medium text-gray-800">{{ $classSchedule->school_year }}</span>
                        </div>
                        <div class="flex items-center gap-1.5 md:gap-2">
                            <span class="text-gray-500 w-24 md:w-32">Grading Period:</span>
                            <span class="font-medium text-gray-800">Term {{ $gradingPeriod }}</span>
                        </div>
                        @if($submissionStatus['start_at'])
                        <div class="flex items-center gap-1.5 md:gap-2">
                            <span class="text-gray-500 w-24 md:w-32">Submission Opens:</span>
                            <span class="font-medium text-gray-800">{{ $submissionStatus['start_at']->format('F j, g:i A') }}</span>
                        </div>
                        @endif
                        @if($submissionStatus['deadline_at'])
                        <div class="flex items-center gap-1.5 md:gap-2">
                            <span class="text-gray-500 w-24 md:w-32">Deadline:</span>
                            <span class="font-medium text-gray-800">{{ $submissionStatus['deadline_at']->format('F j, g:i A') }}</span>
                        </div>
                        @endif
                    </div>
                    @if($submissionStatus['status'] === 'closed')
                        <div class="mt-3 md:mt-4" id="reopening-request-section">
                            @if(isset($reopeningRequest) && $reopeningRequest && $reopeningRequest->isPending())
                                <div class="rounded-lg bg-yellow-50 border border-yellow-200 p-2 md:p-3">
                                    <p class="text-xs md:text-sm font-medium text-yellow-800">Status: Pending Approval</p>
                                    <p class="text-[10px] md:text-xs text-yellow-600 mt-1">Your reopening request is being reviewed by the Registrar.</p>
                                </div>
                            @elseif(isset($reopeningRequest) && $reopeningRequest && $reopeningRequest->isRejected())
                                <div class="rounded-lg bg-red-50 border border-red-200 p-2 md:p-3">
                                    <p class="text-xs md:text-sm font-medium text-red-800">Request Rejected</p>
                                    <p class="text-[10px] md:text-xs text-red-600 mt-1">Your reopening request was rejected. You may submit a new request.</p>
                                </div>
                                <button type="button" id="request-reopening-btn"
                                    class="mt-2 md:mt-3 text-xs md:text-sm bg-blue-600 text-white px-3 py-1.5 md:px-4 md:py-2 rounded-lg hover:bg-blue-700 transition whitespace-nowrap">
                                    Request Reopening Again
                                </button>
                            @elseif(isset($reopeningRequest) && $reopeningRequest && $reopeningRequest->isApproved() && $reopeningRequest->hasTemporaryAccess())
                                <div class="rounded-lg bg-green-50 border border-green-200 p-2 md:p-3">
                                    <p class="text-xs md:text-sm font-medium text-green-800">Request Approved</p>
                                    <p class="text-[10px] md:text-xs text-green-600 mt-1">Temporary access granted until {{ $reopeningRequest->temporary_deadline->format('F j, g:i A') }}</p>
                                </div>
                            @else
                                <button type="button" id="request-reopening-btn"
                                    class="text-xs md:text-sm bg-blue-600 text-white px-3 py-1.5 md:px-4 md:py-2 rounded-lg hover:bg-blue-700 transition whitespace-nowrap">
                                    Request Reopening
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <div class="tc-card mb-4 md:mb-6 p-4 md:p-6">
        <div class="flex justify-between items-center mb-4 md:mb-6 flex-col md:flex-row gap-2 md:gap-0">
            <h2 class="text-base md:text-lg font-semibold text-gray-800">
                {{ $classSchedule->subject->name ?? 'Subject' }} - {{ $classSchedule->section->name ?? 'Section' }}
            </h2>
            <span class="text-xs md:text-sm text-gray-500">
                {{ $classSchedule->school_year }}
            </span>
        </div>

        <!-- Upload DepEd Excel -->
        <div class="bg-gray-50 rounded-lg p-4 md:p-6 mb-4 md:mb-6">
            <h3 class="font-medium text-gray-800 mb-3 text-sm md:text-base">Upload DepEd Excel File</h3>
            <form action="{{ route('teacher.grades.process-upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="class_schedule_id" value="{{ $classSchedule->id }}">
                <input type="hidden" name="school_year" value="{{ $classSchedule->school_year }}">
                
                <div class="mb-3 md:mb-4">
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1 md:mb-2">Grading Period</label>
                    <select name="grading_period" class="w-full border border-gray-300 rounded-lg px-2 py-1.5 md:px-3 md:py-2 text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-green-600" required>
                        <option value="">Select grading period...</option>
                        <option value="1" {{ $gradingPeriod == 1 ? 'selected' : '' }}>Term 1</option>
                        <option value="2" {{ $gradingPeriod == 2 ? 'selected' : '' }}>Term 2</option>
                        <option value="3" {{ $gradingPeriod == 3 ? 'selected' : '' }}>Term 3</option>
                        <option value="4" {{ $gradingPeriod == 4 ? 'selected' : '' }}>Term 4</option>
                    </select>
                </div>

                <div class="mb-3 md:mb-4">
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1 md:mb-2">Select Excel File</label>
                    <input type="file" name="excel_file" accept=".xlsx,.xls" class="block w-full text-xs md:text-sm text-gray-500
                        file:mr-1 md:file:mr-4 file:py-1 md:file:py-2 file:px-1.5 md:file:px-4
                        file:rounded-full file:border-0
                        file:text-[10px] md:file:text-sm file:font-semibold
                        file:bg-green-50 file:text-green-700
                        hover:file:bg-green-100" required>
                </div>

                <button type="submit" class="w-full md:w-auto px-4 py-1.5 md:px-6 md:py-2 bg-blue-600 text-white text-xs md:text-sm rounded hover:bg-blue-700 transition whitespace-nowrap">
                    Upload and Import
                </button>
            </form>
            <p class="text-[10px] md:text-xs text-gray-500 mt-2 md:mt-3">
                Upload the official DepEd Excel grading template. The system will import grades including Initial Grade, Transmuted Grade, Quarterly Grade, Final Grade, and Remarks.
            </p>
        </div>

        <!-- Preview Table (Empty initially) -->
        <div class="mb-4 md:mb-6">
            <h3 class="font-medium text-gray-800 mb-3 text-sm md:text-base">Imported Grades Preview</h3>
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="w-full min-w-max text-xs md:text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left py-2 px-2 md:py-3 md:px-4 font-medium text-gray-600 text-[10px] md:text-xs">Student Name</th>
                            <th class="text-left py-2 px-2 md:py-3 md:px-4 font-medium text-gray-600 text-[10px] md:text-xs">LRN</th>
                            <th class="text-center py-2 px-2 md:py-3 md:px-4 font-medium text-gray-600 text-[10px] md:text-xs">Initial Grade</th>
                            <th class="text-center py-2 px-2 md:py-3 md:px-4 font-medium text-gray-600 text-[10px] md:text-xs">Transmuted Grade</th>
                            <th class="text-center py-2 px-2 md:py-3 md:px-4 font-medium text-gray-600 text-[10px] md:text-xs">Quarterly Grade</th>
                            <th class="text-center py-2 px-2 md:py-3 md:px-4 font-medium text-gray-600 text-[10px] md:text-xs">Final Grade</th>
                            <th class="text-center py-2 px-2 md:py-3 md:px-4 font-medium text-gray-600 text-[10px] md:text-xs">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($importedData) && !empty($importedData))
                            @foreach($importedData as $grade)
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-2 px-2 md:py-3 md:px-4 text-gray-800">{{ $grade['student_name'] }}</td>
                                    <td class="py-2 px-2 md:py-3 md:px-4 text-gray-600">{{ $grade['lrn'] }}</td>
                                    <td class="py-2 px-2 md:py-3 md:px-4 text-center text-gray-700">{{ $grade['initial_grade'] ?: '-' }}</td>
                                    <td class="py-2 px-2 md:py-3 md:px-4 text-center text-gray-700">{{ $grade['transmuted_grade'] ?: '-' }}</td>
                                    <td class="py-2 px-2 md:py-3 md:px-4 text-center text-gray-700">{{ $grade['quarterly_grade'] ?: '-' }}</td>
                                    <td class="py-2 px-2 md:py-3 md:px-4 text-center font-semibold text-gray-800">{{ $grade['final_grade'] ?: '-' }}</td>
                                    <td class="py-2 px-2 md:py-3 md:px-4 text-center">
                                        <span class="text-[10px] md:text-xs px-1.5 py-0.5 md:px-2 md:py-1 rounded-full
                                            {{ $grade['remarks'] == 'Passed' ? 'bg-green-50 text-green-700' : ($grade['remarks'] == 'Failed' ? 'bg-red-50 text-red-600' : 'bg-gray-50 text-gray-600') }}">
                                            {{ $grade['remarks'] ?: '-' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center py-4 md:py-8 text-gray-400 text-[10px] md:text-xs">
                                    No grades imported yet. Upload a DepEd Excel file to begin.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Submit Grades Button (Disabled until import and submission window open) -->
        <div class="flex justify-end gap-2 md:gap-3 flex-wrap md:flex-nowrap">
            @if(isset($importedData) && !empty($importedData))
                @if($submissionStatus['status'] === 'open')
                    <form action="{{ route('teacher.grades.submit') }}" method="POST" onsubmit="return confirm('Are you sure you want to submit these grades to the Registrar? This will lock editing.')">
                        @csrf
                        <input type="hidden" name="grading_period" value="{{ $gradingPeriod }}">
                        <button type="submit"
                                class="w-full md:w-auto px-4 py-1.5 md:px-6 md:py-2 bg-green-700 text-white text-xs md:text-sm rounded hover:bg-green-800 transition whitespace-nowrap">
                            Submit Grades to Registrar
                        </button>
                    </form>
                @else
                    <button type="button"
                            disabled
                            class="w-full md:w-auto px-4 py-1.5 md:px-6 md:py-2 bg-green-700 text-white text-xs md:text-sm rounded hover:bg-green-800 transition disabled:bg-gray-300 disabled:cursor-not-allowed whitespace-nowrap">
                        Submit Grades to Registrar ({{ $submissionStatus['message'] }})
                    </button>
                @endif
            @else
                <button type="button"
                        disabled
                        class="w-full md:w-auto px-4 py-1.5 md:px-6 md:py-2 bg-green-700 text-white text-xs md:text-sm rounded hover:bg-green-800 transition disabled:bg-gray-300 disabled:cursor-not-allowed whitespace-nowrap">
                    Submit Grades to Registrar (No grades imported)
                </button>
            @endif
            @if(isset($importedData) && !empty($importedData))
                <button type="button"
                        onclick="window.location.href='{{ route('teacher.grades.upload', $classSchedule) }}'"
                        class="w-full md:w-auto px-4 py-1.5 md:px-6 md:py-2 bg-gray-500 text-white text-xs md:text-sm rounded hover:bg-gray-600 transition whitespace-nowrap">
                    Cancel Import
                </button>
            @endif
        </div>

        <!-- Show submission status -->
        @if(isset($existingGrades) && $existingGrades->isNotEmpty())
            <div class="mt-3 md:mt-4 p-3 md:p-4 rounded-lg {{ $existingGrades->first()->status === 'approved' ? 'bg-green-50 border border-green-200' : ($existingGrades->first()->status === 'rejected' ? 'bg-red-50 border border-red-200' : 'bg-yellow-50 border border-yellow-200') }}">
                <p class="text-xs md:text-sm font-medium {{ $existingGrades->first()->status === 'approved' ? 'text-green-800' : ($existingGrades->first()->status === 'rejected' ? 'text-red-800' : 'text-yellow-800') }}">
                    Status: {{ ucfirst($existingGrades->first()->status) }}
                    @if($existingGrades->first()->status === 'submitted')
                        - Pending Registrar Approval
                    @elseif($existingGrades->first()->status === 'rejected')
                        - {{ $existingGrades->first()->rejection_reason }}
                    @endif
                </p>
                @if($existingGrades->first()->status === 'rejected')
                    <button onclick="window.location.href='{{ route('teacher.grades.upload', $classSchedule) }}"
                            class="mt-2 text-xs md:text-sm text-blue-600 hover:text-blue-800 underline">
                        Upload new file to resubmit
                    </button>
                @endif
            </div>
        @endif
    </div>
    @endif

    @session('success')
        <div class="bg-green-50 border border-green-200 text-green-700 px-3 py-2 md:px-4 md:py-3 rounded mb-4 text-xs md:text-sm">
            {{ $value }}
        </div>
    @endsession

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-3 py-2 md:px-4 md:py-3 rounded mb-4 text-xs md:text-sm">
            {{ session('error') }}
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const schoolYearSelect = document.getElementById('school_year');
            const sectionSelect = document.getElementById('section_id');
            const subjectSelect = document.getElementById('class_schedule_id');
            const continueButton = document.getElementById('continue_button');

            const selectedSectionId = @json((string) $selectedSectionId);
            const selectedClassScheduleId = @json((string) $selectedClassScheduleId);
            const sectionsUrl = @json(route('teacher.grades.sections'));
            const subjectsUrl = @json(route('teacher.grades.subjects'));

            const setOptions = (select, placeholder, items, selectedValue = '') => {
                select.innerHTML = '';
                select.append(new Option(placeholder, ''));

                items.forEach((item) => {
                    const option = new Option(item.name, item.id);
                    option.selected = String(item.id) === String(selectedValue);
                    select.append(option);
                });

                select.disabled = items.length === 0;
            };

            const resetSubjects = (placeholder = 'Select Section First') => {
                setOptions(subjectSelect, placeholder, []);
                updateContinueButton();
            };

            const updateContinueButton = () => {
                continueButton.disabled = !(
                    schoolYearSelect.value &&
                    sectionSelect.value &&
                    subjectSelect.value
                );
            };

            const fetchJson = async (url, params) => {
                const response = await fetch(`${url}?${new URLSearchParams(params)}`, {
                    headers: {
                        Accept: 'application/json',
                    },
                });

                if (!response.ok) {
                    throw new Error('Unable to load options.');
                }

                return response.json();
            };

            const loadSections = async (selectedValue = '') => {
                resetSubjects();

                if (!schoolYearSelect.value) {
                    setOptions(sectionSelect, 'Select School Year First', []);
                    updateContinueButton();
                    return;
                }

                setOptions(sectionSelect, 'Loading sections...', []);

                try {
                    const sections = await fetchJson(sectionsUrl, {
                        school_year: schoolYearSelect.value,
                    });

                    setOptions(sectionSelect, sections.length ? 'Select Section' : 'No sections assigned', sections, selectedValue);
                    updateContinueButton();

                    if (sectionSelect.value) {
                        await loadSubjects(selectedClassScheduleId);
                    }
                } catch (error) {
                    setOptions(sectionSelect, 'Unable to load sections', []);
                    updateContinueButton();
                }
            };

            const loadSubjects = async (selectedValue = '') => {
                if (!schoolYearSelect.value || !sectionSelect.value) {
                    resetSubjects();
                    return;
                }

                setOptions(subjectSelect, 'Loading subjects...', []);

                try {
                    const subjects = await fetchJson(subjectsUrl, {
                        school_year: schoolYearSelect.value,
                        section_id: sectionSelect.value,
                    });

                    setOptions(subjectSelect, subjects.length ? 'Select Subject' : 'No subjects assigned', subjects, selectedValue);
                    updateContinueButton();
                } catch (error) {
                    setOptions(subjectSelect, 'Unable to load subjects', []);
                    updateContinueButton();
                }
            };

            schoolYearSelect.addEventListener('change', () => loadSections());
            sectionSelect.addEventListener('change', () => loadSubjects());
            subjectSelect.addEventListener('change', updateContinueButton);

            if (schoolYearSelect.value) {
                loadSections(selectedSectionId);
            } else {
                updateContinueButton();
            }
        });

        // Reopening Request Modal
        const requestReopeningBtn = document.getElementById('request-reopening-btn');
        const reopeningRequestModal = document.getElementById('reopening-request-modal');
        const cancelReopeningBtn = document.getElementById('cancel-reopening-btn');
        const submitReopeningBtn = document.getElementById('submit-reopening-btn');

        if (requestReopeningBtn) {
            requestReopeningBtn.addEventListener('click', () => {
                if (reopeningRequestModal) {
                    reopeningRequestModal.classList.remove('hidden');
                }
            });
        }

        if (cancelReopeningBtn) {
            cancelReopeningBtn.addEventListener('click', () => {
                if (reopeningRequestModal) {
                    reopeningRequestModal.classList.add('hidden');
                }
            });
        }

        if (submitReopeningBtn) {
            submitReopeningBtn.addEventListener('click', () => {
                const reason = document.getElementById('reopening-reason').value;
                if (!reason || reason.trim() === '') {
                    alert('Please provide a reason for your reopening request.');
                    return;
                }

                submitReopeningBtn.disabled = true;
                submitReopeningBtn.textContent = 'Submitting...';

                const formData = new FormData();
                formData.append('grading_period', '{{ $gradingPeriod ?? 1 }}');
                formData.append('reason', reason);

                fetch('{{ route('teacher.grade-reopening-requests.create', $classSchedule) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message);
                        submitReopeningBtn.disabled = false;
                        submitReopeningBtn.textContent = 'Submit Request';
                    }
                })
                .catch(error => {
                    alert('Error submitting request: ' + error.message);
                    submitReopeningBtn.disabled = false;
                    submitReopeningBtn.textContent = 'Submit Request';
                });
            });
        }

        if (reopeningRequestModal) {
            reopeningRequestModal.addEventListener('click', (e) => {
                if (e.target === reopeningRequestModal) {
                    reopeningRequestModal.classList.add('hidden');
                }
            });
        }
    </script>

    {{-- Reopening Request Modal --}}
    <div id="reopening-request-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Request Grade Submission Reopening</h3>
            <p class="text-sm text-gray-600 mb-4">
                Please provide a reason for requesting to reopen the grade submission period.
            </p>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                <textarea id="reopening-reason" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Explain why you need to submit grades after the deadline..."></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" id="cancel-reopening-btn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="button" id="submit-reopening-btn" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">
                    Submit Request
                </button>
            </div>
        </div>
    </div>
@endsection
