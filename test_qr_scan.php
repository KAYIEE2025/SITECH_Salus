<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing QR Scan Logic\n";
echo "====================\n\n";

$student = \App\Models\Student::find(13);
echo 'Student ID 13:' . PHP_EOL;
echo 'student_number: ' . $student->student_number . PHP_EOL;
echo 'qr_code_value: ' . $student->qr_code_value . PHP_EOL;
echo PHP_EOL;

$testQrValue = '20230824-000697';
$foundByQr = \App\Models\Student::where('qr_code_value', $testQrValue)->first();
echo 'Search by qr_code_value: ' . ($foundByQr ? 'Found - ID: ' . $foundByQr->id : 'Not found') . PHP_EOL;

// Test the extraction logic
$controller = new \App\Http\Controllers\SSG\AttendanceController();
$reflection = new \ReflectionClass($controller);
$method = $reflection->getMethod('extractStudentNumberFromQR');
$method->setAccessible(true);
$extracted = $method->invoke($controller, $testQrValue);
echo 'Extracted student number: ' . ($extracted ?: 'null') . PHP_EOL;

if ($extracted) {
    $foundByStudentNumber = \App\Models\Student::where('student_number', $extracted)->first();
    echo 'Search by extracted student_number: ' . ($foundByStudentNumber ? 'Found - ID: ' . $foundByStudentNumber->id : 'Not found') . PHP_EOL;
}

echo PHP_EOL;
echo "Testing new student QR format\n";
echo "=============================\n";

$newStudent = \App\Models\Student::find(4);
echo 'Student ID 4:' . PHP_EOL;
echo 'student_number: ' . $newStudent->student_number . PHP_EOL;
echo 'qr_code_value: ' . $newStudent->qr_code_value . PHP_EOL;
echo PHP_EOL;

$newTestQrValue = 'SITech-STUDENT|567|94eb47e3-dffb-4fa2-b236-eeff717e1766';
$foundByQr2 = \App\Models\Student::where('qr_code_value', $newTestQrValue)->first();
echo 'Search by qr_code_value: ' . ($foundByQr2 ? 'Found - ID: ' . $foundByQr2->id : 'Not found') . PHP_EOL;

$extracted2 = $method->invoke($controller, $newTestQrValue);
echo 'Extracted student number: ' . ($extracted2 ?: 'null') . PHP_EOL;

if ($extracted2) {
    $foundByStudentNumber2 = \App\Models\Student::where('student_number', $extracted2)->first();
    echo 'Search by extracted student_number: ' . ($foundByStudentNumber2 ? 'Found - ID: ' . $foundByStudentNumber2->id : 'Not found') . PHP_EOL;
}
