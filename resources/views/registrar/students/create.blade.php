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
                <select name="student_type" id="student_type"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    <option value="">Select student type...</option>
                    <option value="new" {{ old('student_type') == 'new' ? 'selected' : '' }}>New Student</option>
                    <option value="old" {{ old('student_type') == 'old' ? 'selected' : '' }}>Old Student</option>
                </select>
                @error('student_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <!-- <p class="text-xs text-gray-500 mt-1">New students: Scan the school-generated QR to auto-fill the Student Number. Old students: Scan existing QR or enter student number for re-enrollment.</p> -->
            </div>

            {{-- Existing Student Notice --}}
            <div id="existing-student-notice" class="mb-6 p-4 bg-green-50 rounded-lg border border-green-200 hidden">
                <p class="text-sm font-semibold text-green-800">✓ Existing Student Found</p>
                <p class="text-xs text-gray-700 mt-1">Name: <span id="existing-student-name" class="font-semibold"></span></p>
                <p class="text-xs text-gray-700 mt-1">QR Value: <span id="existing-student-number" class="font-mono font-semibold"></span></p>
                <p class="text-xs text-gray-700 mt-1">Existing Account: <span id="existing-student-account" class="font-semibold"></span></p>
                <p class="text-xs text-gray-600 mt-2">This is a re-enrollment. The student's identity and account will be preserved.</p>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

                {{-- Student Number --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">QR Value <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <input type="text" name="student_number" id="student_number" value="{{ old('student_number') }}"
                            class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
                            placeholder="2024-0001" required>
                        <button type="button" id="lookup-student-btn" class="hidden bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                            Lookup
                        </button>
                    </div>
                    @error('student_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <!-- <p class="text-xs text-gray-500 mt-1" id="student-number-hint">Enter the student's School ID (Student Number).</p> -->
                </div>

                <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-3 py-2">
                    <p class="text-sm font-medium text-green-800">QR Code</p>
                    <p class="mt-1 text-xs text-green-700" id="qr-code-hint">Scan the school-generated QR to auto-fill the QR Value.</p>
                </div>

                {{-- QR Code Scanner for New and Old Students --}}
                <div class="mb-4 hidden" id="qr-code-scanner-container">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Scan School QR Code <span class="text-red-500">*</span></label>
                    <div class="border border-gray-300 rounded-lg p-4 bg-gray-50">
                        <div id="qr-reader" class="w-full"></div>
                        <div id="qr-scan-result" class="mt-3 hidden">
                            <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                <p class="text-sm font-semibold text-green-800">✓ Student Found</p>
                                <p class="text-xs text-gray-700 mt-1">School Number: <span id="found-student-number" class="font-mono font-semibold"></span></p>
                                <p class="text-xs text-gray-700 mt-1">Name: <span id="found-student-name" class="font-semibold"></span></p>
                            </div>
                        </div>
                        <div id="qr-scan-error" class="mt-3 hidden">
                            <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                <p class="text-sm font-semibold text-red-800" id="scan-error-message">Error scanning QR code</p>
                            </div>
                        </div>
                        <button type="button" id="start-scan-btn" class="mt-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                            Start QR Scanner
                        </button>
                        <button type="button" id="stop-scan-btn" class="mt-3 bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition hidden">
                            Stop Scanner
                        </button>
                    </div>
                    <!-- <p class="text-xs text-gray-500 mt-1" id="qr-scan-instruction">Scan the school-generated QR code below to auto-fill the Student Number.</p> -->
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
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Term <span class="text-red-500">*</span></label>
            <select name="term"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                <option value="">Select term...</option>
                <option value="Term 1" {{ old('term') == 'Term 1' ? 'selected' : '' }}>Term 1</option>
                <option value="Term 2" {{ old('term') == 'Term 2' ? 'selected' : '' }}>Term 2</option>
                <option value="Term 3" {{ old('term') == 'Term 3' ? 'selected' : '' }}>Term 3</option>
            </select>
            @error('term') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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

        <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const studentTypeSelect = document.querySelector('select[name="student_type"]');
                const qrCodeScannerContainer = document.getElementById('qr-code-scanner-container');
                const qrCodeHint = document.getElementById('qr-code-hint');
                const studentNumberInput = document.getElementById('student_number');
                const firstNameInput = document.querySelector('input[name="first_name"]');
                const middleNameInput = document.querySelector('input[name="middle_name"]');
                const lastNameInput = document.querySelector('input[name="last_name"]');
                const studentNumberHint = document.getElementById('student-number-hint');
                
                // Existing student notice elements
                const existingStudentNotice = document.getElementById('existing-student-notice');
                const existingStudentName = document.getElementById('existing-student-name');
                const existingStudentNumber = document.getElementById('existing-student-number');
                const existingStudentAccount = document.getElementById('existing-student-account');
                
                // QR Scanner elements
                const startScanBtn = document.getElementById('start-scan-btn');
                const stopScanBtn = document.getElementById('stop-scan-btn');
                const qrScanResult = document.getElementById('qr-scan-result');
                const qrScanError = document.getElementById('qr-scan-error');
                const foundStudentNumber = document.getElementById('found-student-number');
                const foundStudentName = document.getElementById('found-student-name');
                const scanErrorMessage = document.getElementById('scan-error-message');
                const lookupStudentBtn = document.getElementById('lookup-student-btn');
                
                let html5QrCode = null;
                let isScanning = false;
                let scanInProgress = false;

                function toggleQRCodeScanner() {
                    if (studentTypeSelect.value === 'old') {
                        qrCodeScannerContainer.classList.remove('hidden');
                        qrCodeHint.textContent = 'Scan the student\'s existing QR code or enter student number manually above.';
                        document.getElementById('qr-scan-instruction').textContent = 'Scan the student\'s existing QR code below or enter the student number manually above and click Lookup to auto-fill their information.';
                        lookupStudentBtn.classList.remove('hidden');
                        studentNumberInput.removeAttribute('readonly');
                        studentNumberInput.setAttribute('required', 'required');
                        studentNumberHint.textContent = 'Enter student number and click Lookup, or scan QR code below.';
                    } else if (studentTypeSelect.value === 'new') {
                        qrCodeScannerContainer.classList.remove('hidden');
                        qrCodeHint.textContent = 'Scan the school-generated QR to auto-fill the QR Value.';
                        document.getElementById('qr-scan-instruction').textContent = 'Scan the school-generated QR code.';
                        lookupStudentBtn.classList.add('hidden');
                        studentNumberInput.removeAttribute('readonly');
                        studentNumberInput.setAttribute('required', 'required');
                        studentNumberHint.textContent = 'Scan QR code to auto-fill, or enter QR Value manually.';
                    } else {
                        qrCodeScannerContainer.classList.add('hidden');
                        qrCodeHint.textContent = 'Select student type to begin.';
                        lookupStudentBtn.classList.add('hidden');
                        stopScanner();
                        qrScanResult.classList.add('hidden');
                        qrScanError.classList.add('hidden');
                        existingStudentNotice.classList.add('hidden');
                        studentNumberInput.removeAttribute('readonly');
                        studentNumberInput.setAttribute('required', 'required');
                        // Only clear if not during validation error recovery
                        if (!studentNumberInput.value) {
                            studentNumberInput.value = '';
                        }
                        if (!firstNameInput.value) {
                            firstNameInput.value = '';
                        }
                        if (!middleNameInput.value) {
                            middleNameInput.value = '';
                        }
                        if (!lastNameInput.value) {
                            lastNameInput.value = '';
                        }
                        firstNameInput.removeAttribute('readonly');
                        lastNameInput.removeAttribute('readonly');
                        studentNumberHint.textContent = 'Select student type first.';
                    }
                }

                function stopScanner() {
                    if (html5QrCode && isScanning) {
                        try {
                            html5QrCode.stop().then(() => {
                                html5QrCode.clear();
                                isScanning = false;
                                startScanBtn.classList.remove('hidden');
                                stopScanBtn.classList.add('hidden');
                            }).catch(err => {
                                console.error('Failed to stop scanner:', err);
                                // Force clear state even if stop fails
                                html5QrCode.clear();
                                isScanning = false;
                                startScanBtn.classList.remove('hidden');
                                stopScanBtn.classList.add('hidden');
                            });
                        } catch (err) {
                            console.error('Error stopping scanner:', err);
                            // Force clear state
                            if (html5QrCode) {
                                html5QrCode.clear();
                            }
                            isScanning = false;
                            startScanBtn.classList.remove('hidden');
                            stopScanBtn.classList.add('hidden');
                        }
                    }
                }

                function startScanner() {
                    if (isScanning || scanInProgress) return;
                    
                    scanInProgress = true;
                    qrScanResult.classList.add('hidden');
                    qrScanError.classList.add('hidden');
                    
                    html5QrCode = new Html5Qrcode("qr-reader");
                    
                    const config = { 
                        fps: 10, 
                        qrbox: { width: 250, height: 250 },
                        aspectRatio: 1.0
                    };
                    
                    html5QrCode.start(
                        { facingMode: "environment" }, 
                        config, 
                        onScanSuccess,
                        onScanFailure
                    ).then(() => {
                        isScanning = true;
                        startScanBtn.classList.add('hidden');
                        stopScanBtn.classList.remove('hidden');
                        scanInProgress = false;
                    }).catch(err => {
                        console.error('Error starting scanner:', err);
                        scanErrorMessage.textContent = 'Unable to start camera. Please ensure camera permissions are granted.';
                        qrScanError.classList.remove('hidden');
                        scanInProgress = false;
                    });
                }

                function onScanSuccess(decodedText, decodedResult) {
                    if (scanInProgress) return;
                    scanInProgress = true;

                    // Stop scanner immediately after successful scan
                    stopScanner();

                    // Normalize QR value (remove any extra whitespace)
                    const studentNumber = decodedText.trim();

                    // Check student type to determine workflow
                    if (studentTypeSelect.value === 'new') {
                        // New Student: Just populate student number, no lookup
                        studentNumberInput.value = studentNumber;
                        studentNumberInput.readOnly = true;

                        // Show success message
                        foundStudentNumber.textContent = studentNumber;
                        foundStudentName.textContent = 'Ready for manual entry';
                        qrScanResult.classList.remove('hidden');

                        scanInProgress = false;
                    } else if (studentTypeSelect.value === 'old') {
                        // Old Student: Lookup student in system
                        scanErrorMessage.textContent = 'Looking up student...';
                        qrScanError.classList.remove('hidden');

                        // Call unified student lookup endpoint
                        fetch('{{ route('registrar.students.lookup-student-qr') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ student_number: studentNumber })
                        })
                        .then(response => response.json())
                        .then(data => {
                            qrScanError.classList.add('hidden');

                            if (data.success) {
                                if (data.student_type === 'existing') {
                                    // Handle existing student re-enrollment
                                    studentNumberInput.value = data.student.student_number;
                                    firstNameInput.value = data.student.first_name || '';
                                    middleNameInput.value = data.student.middle_name || '';
                                    lastNameInput.value = data.student.last_name || '';

                                    // Show existing student notice
                                    existingStudentName.textContent = data.student.full_name;
                                    existingStudentNumber.textContent = data.student.student_number;
                                    existingStudentAccount.textContent = data.student.has_account ? 'Yes' : 'No';
                                    existingStudentNotice.classList.remove('hidden');

                                    // Make identity fields readonly
                                    studentNumberInput.readOnly = true;
                                    firstNameInput.readOnly = true;
                                    lastNameInput.readOnly = true;

                                    // Keep student type as 'old' for unified workflow
                                    studentTypeSelect.value = 'old';
                                } else if (data.student_type === 'legacy') {
                                    // Handle legacy student
                                    studentNumberInput.value = data.student.student_number;
                                    firstNameInput.value = data.student.first_name || '';
                                    middleNameInput.value = data.student.middle_name || '';
                                    lastNameInput.value = data.student.last_name || '';

                                    // Show success message
                                    foundStudentNumber.textContent = data.student.student_number;
                                    foundStudentName.textContent = data.student.full_name;
                                    qrScanResult.classList.remove('hidden');

                                    // Make identity fields readonly
                                    studentNumberInput.readOnly = true;
                                    firstNameInput.readOnly = true;
                                    lastNameInput.readOnly = true;

                                    // Keep student type as 'old' for unified workflow
                                    studentTypeSelect.value = 'old';
                                }
                            } else {
                                // Show error message
                                scanErrorMessage.textContent = data.message || 'Student not found.';
                                qrScanError.classList.remove('hidden');
                            }
                        })
                        .catch(error => {
                            console.error('Error looking up student:', error);
                            scanErrorMessage.textContent = 'Error connecting to server. Please try again.';
                            qrScanError.classList.remove('hidden');
                        })
                        .finally(() => {
                            scanInProgress = false;
                        });
                    } else {
                        // No student type selected
                        scanErrorMessage.textContent = 'Please select Student Type first.';
                        qrScanError.classList.remove('hidden');
                        scanInProgress = false;
                    }
                }

                function onScanFailure(error) {
                    // Scan failures are normal during scanning, only log if not scanning
                    if (!isScanning) {
                        console.warn('QR scan error:', error);
                    }
                }

                studentTypeSelect.addEventListener('change', toggleQRCodeScanner);
                toggleQRCodeScanner();
                
                // QR Scanner button handlers
                startScanBtn.addEventListener('click', startScanner);
                stopScanBtn.addEventListener('click', stopScanner);
                
                // Manual lookup button handler
                lookupStudentBtn.addEventListener('click', function() {
                    const studentNumber = studentNumberInput.value.trim();

                    if (!studentNumber) {
                        alert('Please enter a student number first.');
                        return;
                    }

                    // Check student type to determine workflow
                    if (studentTypeSelect.value === 'new') {
                        // New Student: Just confirm the student number is set
                        studentNumberInput.readOnly = true;

                        // Show success message
                        foundStudentNumber.textContent = studentNumber;
                        foundStudentName.textContent = 'Ready for manual entry';
                        qrScanResult.classList.remove('hidden');
                    } else if (studentTypeSelect.value === 'old') {
                        // Old Student: Lookup student in system
                        // Show loading state
                        scanErrorMessage.textContent = 'Looking up student...';
                        qrScanError.classList.remove('hidden');
                        lookupStudentBtn.disabled = true;
                        lookupStudentBtn.textContent = 'Looking up...';

                        // Call unified student lookup endpoint
                        fetch('{{ route('registrar.students.lookup-student-qr') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ student_number: studentNumber })
                        })
                        .then(response => response.json())
                        .then(data => {
                            qrScanError.classList.add('hidden');

                            if (data.success) {
                                if (data.student_type === 'existing') {
                                    // Handle existing student re-enrollment
                                    studentNumberInput.value = data.student.student_number;
                                    firstNameInput.value = data.student.first_name || '';
                                    middleNameInput.value = data.student.middle_name || '';
                                    lastNameInput.value = data.student.last_name || '';

                                    // Show existing student notice
                                    existingStudentName.textContent = data.student.full_name;
                                    existingStudentNumber.textContent = data.student.student_number;
                                    existingStudentAccount.textContent = data.student.has_account ? 'Yes' : 'No';
                                    existingStudentNotice.classList.remove('hidden');

                                    // Make identity fields readonly
                                    studentNumberInput.readOnly = true;
                                    firstNameInput.readOnly = true;
                                    lastNameInput.readOnly = true;

                                    // Keep student type as 'old' for unified workflow
                                    studentTypeSelect.value = 'old';
                                } else if (data.student_type === 'legacy') {
                                    // Handle legacy student
                                    studentNumberInput.value = data.student.student_number;
                                    firstNameInput.value = data.student.first_name || '';
                                    middleNameInput.value = data.student.middle_name || '';
                                    lastNameInput.value = data.student.last_name || '';

                                    // Show success message
                                    foundStudentNumber.textContent = data.student.student_number;
                                    foundStudentName.textContent = data.student.full_name;
                                    qrScanResult.classList.remove('hidden');

                                    // Make identity fields readonly
                                    studentNumberInput.readOnly = true;
                                    firstNameInput.readOnly = true;
                                    lastNameInput.readOnly = true;

                                    // Keep student type as 'old' for unified workflow
                                    studentTypeSelect.value = 'old';
                                }
                            } else {
                                // Show error message
                                scanErrorMessage.textContent = data.message || 'Student not found.';
                                qrScanError.classList.remove('hidden');
                            }
                        })
                        .catch(error => {
                            console.error('Error looking up student:', error);
                            scanErrorMessage.textContent = 'Error connecting to server. Please try again.';
                            qrScanError.classList.remove('hidden');
                        })
                        .finally(() => {
                            lookupStudentBtn.disabled = false;
                            lookupStudentBtn.textContent = 'Lookup';
                        });
                    } else {
                        // No student type selected
                        alert('Please select Student Type first.');
                    }
                });
            });
        </script>
    </div>

@endsection
