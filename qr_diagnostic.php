<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "QR Code Diagnostic Tool\n";
echo "======================\n\n";

// Test if a specific QR value can be found
echo "Enter a QR code value to test (or press Enter to see all old students): ";
$testValue = trim(fgets(STDIN));

if ($testValue) {
    echo "\nTesting QR value: '{$testValue}'\n";
    echo "================================\n";

    // Try to find by qr_code_value
    $foundByQr = \App\Models\Student::where('qr_code_value', $testValue)->first();
    if ($foundByQr) {
        echo "✓ Found by qr_code_value:\n";
        echo "  Student ID: {$foundByQr->id}\n";
        echo "  Name: {$foundByQr->full_name}\n";
        echo "  Student Number: {$foundByQr->student_number}\n";
        echo "  QR Code Value: {$foundByQr->qr_code_value}\n";
    } else {
        echo "✗ Not found by qr_code_value\n";
    }

    // Try to find by student_number
    $foundByStudentNumber = \App\Models\Student::where('student_number', $testValue)->first();
    if ($foundByStudentNumber) {
        echo "✓ Found by student_number:\n";
        echo "  Student ID: {$foundByStudentNumber->id}\n";
        echo "  Name: {$foundByStudentNumber->full_name}\n";
        echo "  Student Number: {$foundByStudentNumber->student_number}\n";
        echo "  QR Code Value: {$foundByStudentNumber->qr_code_value}\n";
    } else {
        echo "✗ Not found by student_number\n";
    }

    // Test the extraction logic
    $controller = new \App\Http\Controllers\SSG\AttendanceController();
    $reflection = new \ReflectionClass($controller);
    $method = $reflection->getMethod('extractStudentNumberFromQR');
    $method->setAccessible(true);
    $extracted = $method->invoke($controller, $testValue);

    if ($extracted) {
        echo "Extracted student number: '{$extracted}'\n";
        $foundByExtracted = \App\Models\Student::where('student_number', $extracted)->first();
        if ($foundByExtracted) {
            echo "✓ Found by extracted student_number:\n";
            echo "  Student ID: {$foundByExtracted->id}\n";
            echo "  Name: {$foundByExtracted->full_name}\n";
        } else {
            echo "✗ Not found by extracted student_number\n";
        }
    } else {
        echo "Could not extract student number from QR value\n";
    }

} else {
    echo "\nAll Old Students:\n";
    echo "==================\n\n";

    $oldStudents = \App\Models\Student::where('student_type', 'old')
        ->whereNotNull('qr_code_value')
        ->get(['id', 'student_number', 'qr_code_value', 'first_name', 'last_name']);

    foreach ($oldStudents as $student) {
        echo "ID {$student->id}: {$student->last_name}, {$student->first_name}\n";
        echo "  Student Number: {$student->student_number}\n";
        echo "  QR Code Value: {$student->qr_code_value}\n";
        echo "  Test this value by entering: {$student->qr_code_value}\n\n";
    }
}

echo "\n================================\n";
echo "IMPORTANT: If you have the physical old QR codes, please:\n";
echo "1. Scan one with your phone QR reader to see what value it contains\n";
echo "2. Enter that value above to test if it matches our database\n";
echo "3. If they don't match, we need to update the database with the correct values\n";
