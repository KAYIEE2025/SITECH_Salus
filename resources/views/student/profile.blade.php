@extends('layouts.app')
@section('title', 'My Profile')
@section('content')
    @if(!$student)
        <div class="rounded-2xl border border-yellow-200 bg-gradient-to-br from-yellow-50 to-amber-50 p-8 text-center">
            <h2 class="text-xl font-semibold text-yellow-800 mb-2">Student Profile Not Found</h2>
            <p class="text-yellow-700">Your student profile has not yet been created. Please contact the Registrar.</p>
        </div>
    @else
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Photo and QR Code -->
            <div class="space-y-6">
                <!-- Profile Photo -->
                <div class="st-card p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Profile Photo</h2>
                    @if($student->photo_path)
                        <div class="flex justify-center">
                            <img src="{{ asset($student->photo_path) }}" alt="Profile Photo" class="w-48 h-48 object-cover rounded-lg">
                        </div>
                    @else
                        <div class="flex items-center justify-center h-48 bg-gray-50 rounded-lg border border-gray-200">
                            <p class="text-gray-500 text-sm">No photo available</p>
                        </div>
                    @endif
                </div>

                <!-- QR Code -->
                <div class="st-card p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">QR Code</h2>
                    @if($student && $student->qr_code_path)
                        <div class="flex justify-center mb-4">
                            <img src="{{ asset('storage/' . $student->qr_code_path) }}" alt="Student QR Code" class="w-48 h-48 object-contain">
                        </div>
                        <div class="flex flex-col gap-2">
                            <button onclick="viewQR()" class="w-full px-3 py-2 bg-green-700 text-white text-sm rounded hover:bg-green-800 transition">
                                View QR
                            </button>
                            <a href="{{ asset('storage/' . $student->qr_code_path) }}" download="qr-{{ $student->student_number }}.svg" 
                               class="w-full px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition text-center">
                                Download QR
                            </a>
                            <button onclick="printQR()" class="w-full px-3 py-2 bg-gray-600 text-white text-sm rounded hover:bg-gray-700 transition">
                                Print QR
                            </button>
                        </div>
                    @else
                        <div class="flex items-center justify-center h-48 bg-gray-50 rounded-lg border border-gray-200">
                            <p class="text-gray-500 text-sm text-center">No QR Code Available.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Profile Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Read-Only Information -->
                <div class="st-card p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-6">Profile Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Student Number</label>
                            <p class="text-base text-gray-800">{{ $student->student_number }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Full Name</label>
                            <p class="text-base text-gray-800">{{ $student->last_name }}, {{ $student->first_name }} {{ $student->middle_name }} {{ $student->suffix }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Email Address</label>
                            <p class="text-base text-gray-800">{{ auth()->user()->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Grade Level</label>
                            <p class="text-base text-gray-800">{{ $student->yearLevel->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Section</label>
                            <p class="text-base text-gray-800">{{ $student->section->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">School Year</label>
                            <p class="text-base text-gray-800">{{ $student->school_year }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Student Status</label>
                            <p class="text-base text-gray-800">{{ $student->status }}</p>
                        </div>
                    </div>
                </div>

                <!-- Editable Contact Number -->
                <div class="st-card p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-6">Contact Information</h2>
                    <form action="{{ route('student.profile.update-contact') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Contact Number</label>
                                <input type="text" 
                                       name="contact_number" 
                                       value="{{ $student->contact_number ?? '' }}" 
                                       placeholder="09XXXXXXXXX"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-green-500 focus:border-green-500"
                                       required>
                                <p class="text-xs text-gray-500 mt-1">Format: 09 followed by 9 digits (e.g., 09123456789)</p>
                                @error('contact_number')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="px-6 py-2 bg-green-700 text-white text-sm rounded-lg hover:bg-green-800 transition">
                                    Update Contact Number
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Password Change -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-6">Change Password</h2>
                    <form action="{{ route('student.profile.update-password') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                                <input type="password" 
                                       name="current_password" 
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-green-500 focus:border-green-500"
                                       required>
                                @error('current_password')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div></div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                <input type="password" 
                                       name="password" 
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-green-500 focus:border-green-500"
                                       required>
                                <p class="text-xs text-gray-500 mt-1">Minimum 8 characters</p>
                                @error('password')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                                <input type="password" 
                                       name="password_confirmation" 
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-green-500 focus:border-green-500"
                                       required>
                            </div>
                            <div class="md:col-span-2">
                                <button type="submit" class="px-6 py-2 bg-green-700 text-white text-sm rounded-lg hover:bg-green-800 transition">
                                    Change Password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- QR Code Modal --}}
        <div id="qrModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white rounded-xl max-w-sm w-full mx-4 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">My QR Code</h3>
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
                    <p class="text-sm text-gray-600 mb-1">{{ $student->last_name }}, {{ $student->first_name }}</p>
                    <p class="text-xs text-gray-500">{{ $student->student_number }}</p>
                </div>
            </div>
        </div>

        <script>
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
                                <p class="student-name">{{ $student->last_name }}, {{ $student->first_name }}</p>
                                <p class="student-number">{{ $student->student_number }}</p>
                            </div>
                        </div>
                    </body>
                    </html>
                `);
                printWindow.document.close();

                printWindow.onload = function () {
                    printWindow.print();

                    printWindow.onafterprint = function () {
                        printWindow.close();
                    };
                };
            }

            // Close modal on outside click
            document.getElementById('qrModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeQRModal();
                }
            });
        </script>
    @endif
@endsection
