<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing Specific Student QR Code\n";
echo "=================================\n\n";

$student = \App\Models\Student::find(24);

if ($student) {
    echo "Student: {$student->full_name}\n";
    echo "QR Code Value: {$student->qr_code_value}\n";
    echo "QR Code Path: {$student->qr_code_path}\n";
    echo PHP_EOL;

    // Test scanner lookup
    echo "Testing scanner lookup logic:\n";
    $foundByQr = \App\Models\Student::where('qr_code_value', $student->qr_code_value)->first();
    echo "Search by qr_code_value: " . ($foundByQr ? "✓ Found (ID: {$foundByQr->id})" : "✗ Not found") . PHP_EOL;

    $foundByStudentNumber = \App\Models\Student::where('student_number', $student->student_number)->first();
    echo "Search by student_number: " . ($foundByStudentNumber ? "✓ Found (ID: {$foundByStudentNumber->id})" : "✗ Not found") . PHP_EOL;

    // Test extraction logic
    $controller = new \App\Http\Controllers\SSG\AttendanceController();
    $reflection = new \ReflectionClass($controller);
    $method = $reflection->getMethod('extractStudentNumberFromQR');
    $method->setAccessible(true);
    $extracted = $method->invoke($controller, $student->qr_code_value);
    echo "Extracted student number: " . ($extracted ?: "null") . PHP_EOL;

    echo PHP_EOL;
    echo "The database lookup should work perfectly.\n";
    echo "If the camera scanner doesn't work, try:\n";
    echo "1. File upload method (upload a photo of the QR code)\n";
    echo "2. Manual entry (type: {$student->qr_code_value})\n";
    echo "3. Print the new QR code from the profile page\n";
} else {
    echo "Student not found.\n";
}
