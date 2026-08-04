@extends('layouts.app')
@section('title', 'Encode Student Profile')
@section('content')

    <div class="ra-card p-6 sm:p-8">
        <h2 class="text-base font-semibold text-gray-800 mb-6">Encode Student Profile</h2>

        <form method="POST" action="{{ route('registrar.students.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Student Type --}}
            <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <label class="block text-sm font-medium text-gray-700 mb-1">Student Type <span class="text-red-500">*</span></label>
                <select name="student_type"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    <option value="">Select student type...</option>
                    <option value="new" {{ old('student_type') == 'new' ? 'selected' : '' }}>New Student</option>
                    <option value="old" {{ old('student_type') == 'old' ? 'selected' : '' }}>Old Student</option>
                </select>
                @error('student_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <p class="text-xs text-gray-500 mt-1">New students will have a QR code automatically generated. Old students will have QR codes imported later.</p>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

                {{-- Student Number --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Student Number <span class="text-red-500">*</span></label>
                    <input type="text" name="student_number" id="student_number" value="{{ old('student_number') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
                        placeholder="2024-0001" required>
                    @error('student_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-500 mt-1" id="student-number-hint">Enter the student's School ID (Student Number).</p>
                </div>

                <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-3 py-2">
                    <p class="text-sm font-medium text-green-800">QR Code</p>
                    <p class="mt-1 text-xs text-green-700" id="qr-code-hint">A unique QR code will be generated automatically after saving.</p>
                </div>

                {{-- QR Code Upload for Old Students --}}
                <div class="mb-4 hidden" id="qr-code-upload-container">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload Existing QR Code <span class="text-red-500">*</span></label>
                    <input type="file" name="qr_code_file" id="qr_code_file"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
                        accept="image/png,image/jpeg,image/svg+xml">
                    @error('qr_code_file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-500 mt-1">Upload the student's existing QR code image (PNG, JPG, or SVG).</p>
                    <div id="qr-decode-result" class="mt-2 hidden">
                        <p class="text-xs font-semibold text-green-600">✓ QR Successfully Read</p>
                        <p class="text-xs text-gray-700 mt-1">Decoded Value:</p>
                        <p id="decoded-value" class="text-sm font-mono text-gray-800 bg-gray-100 p-2 rounded mt-1"></p>
                    </div>
                    <div id="qr-decode-error" class="mt-2 hidden">
                        <p class="text-xs text-red-500">Unable to read QR code. Please upload a valid QR image.</p>
                    </div>
                </div>

                {{-- Gender --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                    <select name="gender"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                        <option value="">Select...</option>
                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                {{-- First Name --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    @error('first_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Middle Name --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                    <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                </div>

                {{-- Last Name --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    @error('last_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Suffix --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Suffix</label>
                    <input type="text" name="suffix" value="{{ old('suffix') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
                        placeholder="Jr., Sr., III">
                </div>

                {{-- Date of Birth --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                </div>

                {{-- Contact Number --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                    <input type="text" name="contact_number" value="{{ old('contact_number') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
                        placeholder="09XXXXXXXXX">
                </div>

                {{-- Email --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                </div>

                {{-- Address --}}
                <div class="mb-4 md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <textarea name="address" rows="2"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">{{ old('address') }}</textarea>
                </div>

            </div>

            {{-- Guardian Information --}}
            <div class="border-t border-gray-100 pt-4 mt-2 mb-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Guardian Information</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Guardian Name</label>
                        <input type="text" name="guardian_name" value="{{ old('guardian_name') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Guardian Contact</label>
                        <input type="text" name="guardian_contact" value="{{ old('guardian_contact') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Relationship</label>
                        <input type="text" name="guardian_relationship" value="{{ old('guardian_relationship') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
                            placeholder="Parent, Guardian...">
                    </div>
                </div>
            </div>

            {{-- Academic Information --}}
<div class="border-t border-gray-100 pt-4 mt-2 mb-6">
    <h3 class="text-sm font-semibold text-gray-700 mb-3">Academic Information</h3>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Grade Level <span class="text-red-500">*</span></label>
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
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Section <span class="text-red-500">*</span></label>
            <select name="section_id"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                <option value="">Select section...</option>
                @foreach($sections as $section)
                    <option value="{{ $section->id }}" {{ old('section_id') == $section->id ? 'selected' : '' }}>
                        {{ $section->yearLevel->name ?? '' }} — Section {{ $section->name }}
                    </option>
                @endforeach
            </select>
            @error('section_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">School Year <span class="text-red-500">*</span></label>
            @if($activeSchoolYear)
                <input type="text" name="school_year" value="{{ old('school_year', $activeSchoolYear) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
            @else
                <input type="text" name="school_year" value="{{ old('school_year') }}"
                    class="w-full border border-red-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
                <p class="text-red-500 text-xs mt-1">No active school year set. Please contact Super Admin to set an active school year.</p>
            @endif
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-600 md:col-span-3">
            <p class="font-semibold text-gray-800">Enrollment covers the entire School Year (Term 1, Term 2, Term 3)</p>
            <p class="mt-1">Saving this profile sets the student status to <span class="font-semibold text-gray-800">Pending Student Account</span> and creates study load records from the selected section's class schedules.</p>
        </div>
    </div>
</div>

            <div class="flex gap-3">
                <button type="submit"
                    class="bg-green-800 hover:bg-green-900 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition">
                    Save Student Profile
                </button>
                <a href="{{ route('registrar.students') }}"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-6 py-2.5 rounded-lg transition">
                    Cancel
                </a>
            </div>

        </form>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const studentTypeSelect = document.querySelector('select[name="student_type"]');
                const qrCodeUploadContainer = document.getElementById('qr-code-upload-container');
                const qrCodeHint = document.getElementById('qr-code-hint');
                const qrCodeFile = document.getElementById('qr_code_file');
                const qrDecodeResult = document.getElementById('qr-decode-result');
                const qrDecodeError = document.getElementById('qr-decode-error');
                const decodedValue = document.getElementById('decoded-value');
                const studentNumberInput = document.getElementById('student_number');
                const studentNumberHint = document.getElementById('student-number-hint');

                function toggleQRCodeUpload() {
                    if (studentTypeSelect.value === 'old') {
                        qrCodeUploadContainer.classList.remove('hidden');
                        qrCodeHint.textContent = 'Upload the student\'s existing QR code. The School ID will be auto-filled from the QR code.';
                        qrCodeFile.required = true;
                        studentNumberInput.removeAttribute('required');
                        studentNumberHint.textContent = 'School ID will be auto-filled from the uploaded QR code.';
                    } else {
                        qrCodeUploadContainer.classList.add('hidden');
                        qrCodeHint.textContent = 'A unique QR code will be generated automatically after saving.';
                        qrCodeFile.required = false;
                        qrCodeFile.value = '';
                        qrDecodeResult.classList.add('hidden');
                        qrDecodeError.classList.add('hidden');
                        studentNumberInput.readOnly = false;
                        studentNumberInput.setAttribute('required', 'required');
                        studentNumberInput.value = '';
                        studentNumberHint.textContent = 'Enter the student\'s School ID (Student Number).';
                    }
                }

                studentTypeSelect.addEventListener('change', toggleQRCodeUpload);
                toggleQRCodeUpload();

                // QR Code decoding functionality
                qrCodeFile.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (!file) {
                        qrDecodeResult.classList.add('hidden');
                        qrDecodeError.classList.add('hidden');
                        studentNumberInput.readOnly = false;
                        studentNumberInput.value = '';
                        return;
                    }

                    // Reset previous state
                    qrDecodeResult.classList.add('hidden');
                    qrDecodeError.classList.add('hidden');
                    studentNumberInput.readOnly = false;
                    studentNumberInput.value = '';

                    const formData = new FormData();
                    formData.append('qr_code_file', file);

                    fetch('{{ route('registrar.students.decode-qr') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            decodedValue.textContent = data.decoded_value;
                            qrDecodeResult.classList.remove('hidden');
                            // Auto-populate School ID textbox with decoded value
                            studentNumberInput.value = data.decoded_value;
                            // Make School ID textbox readonly
                            studentNumberInput.readOnly = true;
                        } else {
                            qrDecodeError.classList.remove('hidden');
                            // Keep textbox empty and editable on error
                            studentNumberInput.readOnly = false;
                            studentNumberInput.value = '';
                        }
                    })
                    .catch(error => {
                        console.error('Error decoding QR:', error);
                        qrDecodeError.classList.remove('hidden');
                        // Keep textbox empty and editable on error
                        studentNumberInput.readOnly = false;
                        studentNumberInput.value = '';
                    });
                });
            });
        </script>
    </div>

@endsection
