<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking QR File Content for Student 13\n";
echo "========================================\n\n";

$student = \App\Models\Student::find(13);

echo "Database Data:\n";
echo "Student Number: " . $student->student_number . PHP_EOL;
echo "QR Code Value: " . $student->qr_code_value . PHP_EOL;
echo "QR Code Path: " . $student->qr_code_path . PHP_EOL;
echo PHP_EOL;

// Check what's actually in the QR file
if (\Illuminate\Support\Facades\Storage::disk('public')->exists($student->qr_code_path)) {
    $fileContent = \Illuminate\Support\Facades\Storage::disk('public')->get($student->qr_code_path);
    echo "QR file exists (" . strlen($fileContent) . " bytes)\n";

    try {
        $tempPath = tempnam(sys_get_temp_dir(), 'qr');
        file_put_contents($tempPath, $fileContent);

        $qrCode = new \chillerlan\QRCode\QRCode(new \chillerlan\QRCode\QROptions);
        $result = $qrCode->readFromFile($tempPath);

        echo "Actual QR value in file: " . ($result ? $result->data : 'Could not decode') . PHP_EOL;

        if ($result && $result->data !== $student->qr_code_value) {
            echo "MISMATCH! File contains different value than database.\n";
        } elseif ($result && $result->data === $student->qr_code_value) {
            echo "MATCH! File contains the same value as database.\n";
        }

        unlink($tempPath);
    } catch (\Exception $e) {
        echo "Error decoding QR file: " . $e->getMessage() . PHP_EOL;
    }
} else {
    echo "QR file does not exist!\n";
}

echo PHP_EOL;
echo "Testing scanner lookup:\n";
echo "======================\n";

$testQrValue = '20230824-000697';
$foundByQr = \App\Models\Student::where('qr_code_value', $testQrValue)->first();
echo "Search by qr_code_value '$testQrValue': " . ($foundByQr ? 'Found - ID: ' . $foundByQr->id . ', Name: ' . $foundByQr->full_name : 'Not found') . PHP_EOL;

$foundByStudentNumber = \App\Models\Student::where('student_number', '86346')->first();
echo "Search by student_number '86346': " . ($foundByStudentNumber ? 'Found - ID: ' . $foundByStudentNumber->id . ', Name: ' . $foundByStudentNumber->full_name : 'Not found') . PHP_EOL;
