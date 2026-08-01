@extends('layouts.app')
@section('title', 'Grade Management')
@section('content')
    @php
        $selectedSchoolYear = old('school_year', $classSchedule->school_year ?? '');
        $selectedSectionId = old('section_id', $classSchedule->section_id ?? '');
        $selectedClassScheduleId = old('class_schedule_id', $classSchedule->id ?? '');
    @endphp

    <div class="tc-card mb-6 p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-6">Grade Management</h2>

        <!-- Selection Form -->
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <h3 class="font-medium text-gray-800 mb-4">Select Class to Manage Grades</h3>
            <form action="{{ route('teacher.grades.select') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">School Year</label>
                    <select id="school_year" name="school_year" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500" required>
                        <option value="">Select...</option>
                        <option value="2024-2025" @selected($selectedSchoolYear === '2024-2025')>2024-2025</option>
                        <option value="2025-2026" @selected($selectedSchoolYear === '2025-2026')>2025-2026</option>
                        <option value="2026-2027" @selected($selectedSchoolYear === '2026-2027')>2026-2027</option>
                        <option value="2027-2028" @selected($selectedSchoolYear === '2027-2028')>2027-2028</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                    <select id="section_id" name="section_id" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500" required disabled>
                        <option value="">Select School Year First</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                    <select id="class_schedule_id" name="class_schedule_id" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500" required disabled>
                        <option value="">Select Section First</option>
                    </select>
                </div>
                <div class="md:col-span-3">
                    <button id="continue_button" type="submit" class="px-6 py-2 bg-green-700 text-white text-sm rounded hover:bg-green-800 transition disabled:bg-gray-300 disabled:cursor-not-allowed" disabled>
                        Continue to Upload
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Upload Section (Hidden until selection) -->
    @if(isset($classSchedule))
    <div class="tc-card mb-6 p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-semibold text-gray-800">
                {{ $classSchedule->subject->name ?? 'Subject' }} - {{ $classSchedule->section->name ?? 'Section' }}
            </h2>
            <span class="text-sm text-gray-500">
                {{ $classSchedule->school_year }}
            </span>
        </div>

        <!-- Upload DepEd Excel -->
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <h3 class="font-medium text-gray-800 mb-3">Upload DepEd Excel File</h3>
            <form action="{{ route('teacher.grades.process-upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="class_schedule_id" value="{{ $classSchedule->id }}">
                <input type="hidden" name="school_year" value="{{ $classSchedule->school_year }}">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Excel File</label>
                    <input type="file" name="excel_file" accept=".xlsx,.xls" class="block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-green-50 file:text-green-700
                        hover:file:bg-green-100" required>
                </div>
                
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition">
                    Upload and Import
                </button>
            </form>
            <p class="text-xs text-gray-500 mt-3">
                Upload the official DepEd Excel grading template. The system will import grades including Initial Grade, Transmuted Grade, Quarterly Grade, Final Grade, and Remarks.
            </p>
        </div>

        <!-- Preview Table (Empty initially) -->
        <div class="mb-6">
            <h3 class="font-medium text-gray-800 mb-3">Imported Grades Preview</h3>
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left py-3 px-4 font-medium text-gray-600">Student Name</th>
                            <th class="text-left py-3 px-4 font-medium text-gray-600">LRN</th>
                            <th class="text-center py-3 px-4 font-medium text-gray-600">Initial Grade</th>
                            <th class="text-center py-3 px-4 font-medium text-gray-600">Transmuted Grade</th>
                            <th class="text-center py-3 px-4 font-medium text-gray-600">Quarterly Grade</th>
                            <th class="text-center py-3 px-4 font-medium text-gray-600">Final Grade</th>
                            <th class="text-center py-3 px-4 font-medium text-gray-600">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($importedData) && !empty($importedData))
                            @foreach($importedData as $grade)
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-3 px-4 text-gray-800">{{ $grade['student_name'] }}</td>
                                    <td class="py-3 px-4 text-gray-600">{{ $grade['lrn'] }}</td>
                                    <td class="py-3 px-4 text-center text-gray-700">{{ $grade['initial_grade'] ?: '-' }}</td>
                                    <td class="py-3 px-4 text-center text-gray-700">{{ $grade['transmuted_grade'] ?: '-' }}</td>
                                    <td class="py-3 px-4 text-center text-gray-700">{{ $grade['quarterly_grade'] ?: '-' }}</td>
                                    <td class="py-3 px-4 text-center font-semibold text-gray-800">{{ $grade['final_grade'] ?: '-' }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="text-xs px-2 py-1 rounded-full
                                            {{ $grade['remarks'] == 'Passed' ? 'bg-green-50 text-green-700' : ($grade['remarks'] == 'Failed' ? 'bg-red-50 text-red-600' : 'bg-gray-50 text-gray-600') }}">
                                            {{ $grade['remarks'] ?: '-' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center py-8 text-gray-400">
                                    No grades imported yet. Upload a DepEd Excel file to begin.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Submit Grades Button (Disabled until import) -->
        <div class="flex justify-end gap-3">
            @if(isset($importedData) && !empty($importedData))
                <form action="{{ route('teacher.grades.submit-grades') }}" method="POST" onsubmit="return confirm('Are you sure you want to submit these grades to the Registrar? This will lock editing.')">
                    @csrf
                    <input type="hidden" name="class_schedule_id" value="{{ $classSchedule->id }}">
                    <button type="submit" 
                            class="px-6 py-2 bg-green-700 text-white text-sm rounded hover:bg-green-800 transition">
                        Submit Grades to Registrar
                    </button>
                </form>
            @else
                <button type="button" 
                        disabled
                        class="px-6 py-2 bg-green-700 text-white text-sm rounded hover:bg-green-800 transition disabled:bg-gray-300 disabled:cursor-not-allowed">
                    Submit Grades to Registrar
                </button>
            @endif
            @if(isset($importedData) && !empty($importedData))
                <button type="button" 
                        onclick="window.location.href='{{ route('teacher.grades.upload', $classSchedule) }}'"
                        class="px-6 py-2 bg-gray-500 text-white text-sm rounded hover:bg-gray-600 transition">
                    Cancel Import
                </button>
            @endif
        </div>

        <!-- Show submission status -->
        @if(isset($existingGrades) && $existingGrades->isNotEmpty())
            <div class="mt-4 p-4 rounded-lg {{ $existingGrades->first()->status === 'approved' ? 'bg-green-50 border border-green-200' : ($existingGrades->first()->status === 'rejected' ? 'bg-red-50 border border-red-200' : 'bg-yellow-50 border border-yellow-200') }}">
                <p class="text-sm font-medium {{ $existingGrades->first()->status === 'approved' ? 'text-green-800' : ($existingGrades->first()->status === 'rejected' ? 'text-red-800' : 'text-yellow-800') }}">
                    Status: {{ ucfirst($existingGrades->first()->status) }}
                    @if($existingGrades->first()->status === 'submitted')
                        - Pending Registrar Approval
                    @elseif($existingGrades->first()->status === 'rejected')
                        - {{ $existingGrades->first()->rejection_reason }}
                    @endif
                </p>
                @if($existingGrades->first()->status === 'rejected')
                    <button onclick="window.location.href='{{ route('teacher.grades.upload', $classSchedule) }}'" 
                            class="mt-2 text-sm text-blue-600 hover:text-blue-800 underline">
                        Upload new file to resubmit
                    </button>
                @endif
            </div>
        @endif
    </div>
    @endif

    @session('success')
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-4">
            {{ $value }}
        </div>
    @endsession

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
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
    </script>
@endsection
