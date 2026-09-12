@extends('layouts.app')
@section('title', 'Edit Student Profile')
@section('content')

    @session('success')
        <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg px-4 py-3 mb-6">
            {{ $value }}
        </div>
    @endsession

    @session('info')
        <div class="bg-blue-50 border border-blue-200 text-blue-800 text-sm rounded-lg px-4 py-3 mb-6">
            {{ $value }}
        </div>
    @endsession

    <div class="ra-card p-6 sm:p-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-base font-semibold text-gray-800">Edit Student Profile</h2>
            <div class="flex gap-2">
                <a href="{{ route('registrar.students.show', $student) }}" 
                    class="text-sm text-blue-700 hover:text-blue-800 font-medium">
                    View Profile
                </a>
                <a href="{{ route('registrar.students') }}" 
                    class="text-sm text-green-700 hover:text-green-800 font-medium">
                    ← Back to Students
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('registrar.students.update', $student) }}">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                {{-- Student Number (Read-only) --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Student Number</label>
                    <input type="text" value="{{ $student->student_number }}" readonly
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-600">
                </div>

                {{-- First Name --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
                    <input type="text" name="first_name" value="{{ $student->first_name }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    @error('first_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Middle Name --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                    <input type="text" name="middle_name" value="{{ $student->middle_name }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                </div>

                {{-- Last Name --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                    <input type="text" name="last_name" value="{{ $student->last_name }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    @error('last_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Suffix --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Suffix</label>
                    <input type="text" name="suffix" value="{{ $student->suffix }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
                        placeholder="Jr., Sr., III">
                </div>

                {{-- Gender --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                    <select name="gender"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                        <option value="">Select...</option>
                        <option value="Male" {{ $student->gender == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ $student->gender == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ $student->gender == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                {{-- Date of Birth --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                    <input type="date" name="date_of_birth" value="{{ $student->date_of_birth }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                </div>

                {{-- Contact Number --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                    <input type="text" name="contact_number" value="{{ $student->contact_number }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
                        placeholder="09XXXXXXXXX">
                </div>

                {{-- Email --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ $student->email }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                </div>

                {{-- Address --}}
                <div class="mb-4 col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <textarea name="address" rows="2"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">{{ $student->address }}</textarea>
                </div>

            </div>

            {{-- Guardian Information --}}
            <div class="border-t border-gray-100 pt-4 mt-2 mb-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Guardian Information</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Guardian Name</label>
                        <input type="text" name="guardian_name" value="{{ $student->guardian_name }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Guardian Contact</label>
                        <input type="text" name="guardian_contact" value="{{ $student->guardian_contact }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Relationship</label>
                        <input type="text" name="guardian_relationship" value="{{ $student->guardian_relationship }}"
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
                                <option value="{{ $yr->id }}" {{ $student->year_level_id == $yr->id ? 'selected' : '' }}>
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
                                <option value="{{ $section->id }}" {{ $student->section_id == $section->id ? 'selected' : '' }}>
                                    {{ $section->yearLevel->name ?? '' }} — Section {{ $section->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('section_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">School Year <span class="text-red-500">*</span></label>
                        @if($activeSchoolYear)
                            <input type="text" name="school_year" value="{{ old('school_year', $student->school_year) }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                        @else
                            <input type="text" name="school_year" value="{{ old('school_year', $student->school_year) }}"
                                class="w-full border border-red-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
                            <p class="text-red-500 text-xs mt-1">No active school year set. Please contact Super Admin to set an active school year.</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Term <span class="text-red-500">*</span></label>
                        <select name="term"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                            <option value="">Select term...</option>
                            <option value="Term 1" {{ old('term', $student->term) == 'Term 1' ? 'selected' : '' }}>Term 1</option>
                            <option value="Term 2" {{ old('term', $student->term) == 'Term 2' ? 'selected' : '' }}>Term 2</option>
                            <option value="Term 3" {{ old('term', $student->term) == 'Term 3' ? 'selected' : '' }}>Term 3</option>
                        </select>
                        @error('term') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- QR Code Section --}}
            <div class="border-t border-gray-100 pt-4 mt-2 mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Student QR Code</h3>
                @if($student->qr_code_path)
                    <div class="flex items-start gap-6">
                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                            <img src="{{ asset('storage/' . $student->qr_code_path) }}" alt="Student QR Code" class="w-32 h-32">
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-green-700 mb-2">✓ QR Already Assigned</p>
                            <p class="text-sm text-gray-600 mb-2">QR Code Value: <code class="bg-gray-100 px-2 py-1 rounded text-xs">{{ $student->qr_code_value }}</code></p>
                            <div class="flex gap-2 flex-wrap">
                                <button type="button" onclick="viewQR()" class="px-3 py-1.5 bg-green-700 text-white text-sm rounded hover:bg-green-800 transition">
                                    View QR
                                </button>
                                <a href="{{ asset('storage/' . $student->qr_code_path) }}" download="qr-{{ $student->student_number }}.svg"
                                   class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition">
                                    Download QR
                                </a>
                                <button type="button" onclick="printQR()" class="px-3 py-1.5 bg-gray-600 text-white text-sm rounded hover:bg-gray-700 transition">
                                    Print QR
                                </button>
                                <button type="button" onclick="openReplaceQRUpload()" class="px-3 py-1.5 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition">
                                    Replace QR
                                </button>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <p class="text-sm text-yellow-800 mb-3">No QR code generated for this student. This is an old student record.</p>
                        <button type="button" onclick="openQRScannerModal()" class="px-4 py-2 bg-green-700 text-white text-sm rounded hover:bg-green-800 transition">
                            Assign Existing QR
                        </button>
                    </div>
                @endif
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="bg-green-800 hover:bg-green-900 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition">
                    Update Student Profile
                </button>
                <a href="{{ route('registrar.students') }}"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-6 py-2.5 rounded-lg transition">
                    Cancel
                </a>
            </div>

        </form>
    </div>

    {{-- QR Code Modal --}}
    <div id="qrModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl max-w-sm w-full mx-4 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Student QR Code</h3>
                <button onclick="closeQRModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="flex justify-center mb-4">
                <img src="{{ asset('storage/' . $student->qr_code_path) }}" alt="Student QR Code" class="w-64 h-64">
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-600 mb-1">{{ $student->full_name }}</p>
                <p class="text-xs text-gray-500">{{ $student->student_number }}</p>
            </div>
        </div>
    </div>

    {{-- QR Scanner Modal --}}
    <div id="qrScannerModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl max-w-md w-full mx-4 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Scan Existing QR Code</h3>
                <button onclick="closeQRScannerModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div id="qr-reader" class="mb-4"></div>
            
            <div id="qrResult" class="hidden mb-4">
                <p class="text-sm font-medium text-gray-700 mb-2">Detected QR:</p>
                <p id="detectedQRValue" class="text-sm font-mono bg-gray-100 px-3 py-2 rounded"></p>
            </div>
            
            <div class="flex gap-3 justify-end">
                <button onclick="closeQRScannerModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                    Cancel
                </button>
                <button id="saveQRBtn" onclick="saveQRCode()" class="hidden px-4 py-2 bg-green-700 hover:bg-green-800 text-white text-sm font-medium rounded-lg transition">
                    Save
                </button>
            </div>
        </div>
    </div>

    {{-- Replace QR Upload Modal --}}
    <div id="replaceQRUploadModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl max-w-md w-full mx-4 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Replace QR Code</h3>
                <button onclick="closeReplaceQRUpload()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <p class="text-sm text-gray-600 mb-4">
                Upload a new QR code image to replace the existing one. The old QR code will no longer work for attendance.
            </p>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select QR Code Image</label>
                <input type="file" id="replaceQRFile" accept="image/png,image/jpeg,image/jpg,image/svg+xml"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                @error('replace_qr_file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div id="replaceQRPreview" class="hidden mb-4">
                <p class="text-sm font-medium text-gray-700 mb-2">Preview:</p>
                <img id="replaceQRPreviewImg" src="" alt="QR Preview" class="w-32 h-32 border border-gray-200 rounded-lg">
            </div>

            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeReplaceQRUpload()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                    Cancel
                </button>
                <button type="button" onclick="submitReplaceQR()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                    Replace QR
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>

    <script>
        let html5QrCode;
        let detectedQRValue = '';

        function viewQR() {
            document.getElementById('qrModal').classList.remove('hidden');
            document.getElementById('qrModal').classList.add('flex');
        }

        function closeQRModal() {
            document.getElementById('qrModal').classList.add('hidden');
            document.getElementById('qrModal').classList.remove('flex');
        }

        function printQR() {
            const qrImage = document.querySelector('img[alt="Student QR Code"]');
            const qrSrc = qrImage.src;
            
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Student QR Code - {{ $student->student_number }}</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            display: flex;
                            flex-direction: column;
                            align-items: center;
                            justify-content: center;
                            min-height: 100vh;
                            margin: 0;
                            padding: 20px;
                        }
                        .qr-container {
                            text-align: center;
                        }
                        .qr-image {
                            width: 300px;
                            height: 300px;
                        }
                        .student-info {
                            margin-top: 20px;
                        }
                        .student-name {
                            font-size: 18px;
                            font-weight: bold;
                            margin: 5px 0;
                        }
                        .student-number {
                            font-size: 14px;
                            color: #666;
                        }
                    </style>
                </head>
                <body>
                    <div class="qr-container">
                        <img src="${qrSrc}" alt="Student QR Code" class="qr-image">
                        <div class="student-info">
                            <p class="student-name">{{ $student->full_name }}</p>
                            <p class="student-number">{{ $student->student_number }}</p>
                        </div>
                    </div>
                    <script>
                        window.onload = function() {
                            window.print();
                            window.onafterprint = function() {
                                window.close();
                            };
                        };
                    <\/script>
                </body>
                </html>
            `);
            printWindow.document.close();
        }

        function openQRScannerModal() {
            document.getElementById('qrScannerModal').classList.remove('hidden');
            document.getElementById('qrScannerModal').classList.add('flex');
            document.getElementById('qrResult').classList.add('hidden');
            document.getElementById('saveQRBtn').classList.add('hidden');
            
            // Start QR scanner
            html5QrCode = new Html5Qrcode("qr-reader");
            html5QrCode.start(
                { facingMode: "environment" },
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 }
                },
                onScanSuccess,
                onScanFailure
            ).catch(err => {
                console.error("Error starting scanner", err);
            });
        }

        function openReplaceQRUpload() {
            document.getElementById('replaceQRUploadModal').classList.remove('hidden');
            document.getElementById('replaceQRUploadModal').classList.add('flex');
            document.getElementById('replaceQRFile').value = '';
            document.getElementById('replaceQRPreview').classList.add('hidden');
        }

        function closeReplaceQRUpload() {
            document.getElementById('replaceQRUploadModal').classList.add('hidden');
            document.getElementById('replaceQRUploadModal').classList.remove('flex');
        }

        // Show preview when file is selected
        document.getElementById('replaceQRFile').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('replaceQRPreviewImg').src = e.target.result;
                    document.getElementById('replaceQRPreview').classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });

        function submitReplaceQR() {
            const fileInput = document.getElementById('replaceQRFile');
            const file = fileInput.files[0];

            if (!file) {
                alert('Please select a QR code image to upload.');
                return;
            }

            const formData = new FormData();
            formData.append('qr_code_file', file);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            fetch(`/registrar/students/{{ $student->id }}/replace-qr`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeReplaceQRUpload();
                    location.reload();
                } else {
                    alert(data.message || 'Failed to replace QR code.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while replacing the QR code.');
            });
        }

        function closeQRScannerModal() {
            document.getElementById('qrScannerModal').classList.add('hidden');
            document.getElementById('qrScannerModal').classList.remove('flex');
            
            // Stop QR scanner
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    html5QrCode.clear();
                }).catch(err => {
                    console.error("Error stopping scanner", err);
                });
            }
        }

        function onScanSuccess(decodedText, decodedResult) {
            console.log('REPLACE QR STEP 2 - QR scanned successfully:', decodedText);
            // Stop scanning after successful read
            html5QrCode.stop().then(() => {
                html5QrCode.clear();
            }).catch(err => {
                console.error("Error stopping scanner", err);
            });

            // Display the detected QR value
            detectedQRValue = decodedText;
            document.getElementById('detectedQRValue').textContent = decodedText;
            document.getElementById('qrResult').classList.remove('hidden');
            document.getElementById('saveQRBtn').classList.remove('hidden');
        }

        function onScanFailure(error) {
            // Handle scan failure silently
        }

        function saveQRCode() {
            const studentId = '{{ $student->id }}';
            console.log('REPLACE QR STEP 3 - Sending QR assignment request', { studentId, qrValue: detectedQRValue });

            fetch(`/registrar/students/${studentId}/assign-qr`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    qr_code_value: detectedQRValue
                })
            })
            .then(response => {
                console.log('REPLACE QR STEP 4 - Response received', response.status);
                return response.json();
            })
            .then(data => {
                console.log('REPLACE QR STEP 5 - Response data', data);
                if (data.success) {
                    alert(data.message);
                    closeQRScannerModal();
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('REPLACE QR ERROR - Fetch error:', error);
                alert('An error occurred while saving the QR code.');
            });
        }

        // Close modal on outside click
        document.getElementById('qrModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeQRModal();
            }
        });

        document.getElementById('qrScannerModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeQRScannerModal();
            }
        });

        document.getElementById('replaceQRUploadModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeReplaceQRUpload();
            }
        });
    </script>
@endsection
