@extends('layouts.app')
@section('title', 'Edit Student Profile')
@section('content')

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg px-4 py-3 mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="ra-card p-6 sm:p-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-base font-semibold text-gray-800">Edit Student Profile</h2>
            <a href="{{ route('registrar.students') }}" 
                class="text-sm text-green-700 hover:text-green-800 font-medium">
                ← Back to Students
            </a>
        </div>

        <form method="POST" action="{{ route('registrar.students.update', $student) }}">
            @csrf @method('PUT')

            <div class="grid grid-cols-3 gap-4">
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
                <div class="grid grid-cols-3 gap-4">
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
                <div class="grid grid-cols-3 gap-4">
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
                        <input type="text" name="school_year" value="{{ $student->school_year }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
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
                                <button onclick="viewQR()" class="px-3 py-1.5 bg-green-700 text-white text-sm rounded hover:bg-green-800 transition">
                                    View QR
                                </button>
                                <a href="{{ asset('storage/' . $student->qr_code_path) }}" download="qr-{{ $student->student_number }}.svg" 
                                   class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition">
                                    Download QR
                                </a>
                                <button onclick="printQR()" class="px-3 py-1.5 bg-gray-600 text-white text-sm rounded hover:bg-gray-700 transition">
                                    Print QR
                                </button>
                                <button onclick="openReplaceQRConfirmation()" class="px-3 py-1.5 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition">
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

    {{-- Replace QR Confirmation Modal --}}
    <div id="replaceQRConfirmationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl max-w-md w-full mx-4 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Replace QR Code</h3>
                <button onclick="closeReplaceQRConfirmation()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <p class="text-sm text-gray-600 mb-6">
                Are you sure you want to replace the existing QR code for this student? The old QR code will no longer work for attendance.
            </p>
            
            <div class="flex gap-3 justify-end">
                <button onclick="closeReplaceQRConfirmation()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                    Cancel
                </button>
                <button onclick="confirmReplaceQR()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                    Yes, Replace QR
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

        function openReplaceQRConfirmation() {
            document.getElementById('replaceQRConfirmationModal').classList.remove('hidden');
            document.getElementById('replaceQRConfirmationModal').classList.add('flex');
        }

        function closeReplaceQRConfirmation() {
            document.getElementById('replaceQRConfirmationModal').classList.add('hidden');
            document.getElementById('replaceQRConfirmationModal').classList.remove('flex');
        }

        function confirmReplaceQR() {
            closeReplaceQRConfirmation();
            openQRScannerModal();
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
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeQRScannerModal();
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
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

        document.getElementById('replaceQRConfirmationModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeReplaceQRConfirmation();
            }
        });
    </script>
@endsection
