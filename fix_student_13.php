<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Fixing Student ID 13 Data\n";
echo "========================\n\n";

$student = \App\Models\Student::find(13);

echo "Current Data:\n";
echo "Student Number: " . $student->student_number . PHP_EOL;
echo "QR Code Value: " . $student->qr_code_value . PHP_EOL;
echo "QR Code Path: " . $student->qr_code_path . PHP_EOL;
echo PHP_EOL;

// Check what's actually in the QR file
if (\Illuminate\Support\Facades\Storage::disk('public')->exists($student->qr_code_path)) {
    $fileContent = \Illuminate\Support\Facades\Storage::disk('public')->get($student->qr_code_path);
    echo "QR file exists. Trying to decode...\n";

    try {
        $tempPath = tempnam(sys_get_temp_dir(), 'qr');
        file_put_contents($tempPath, $fileContent);

        $qrCode = new \chillerlan\QRCode\QRCode(new \chillerlan\QRCode\QROptions);
        $result = $qrCode->readFromFile($tempPath);

        echo "Actual QR value in file: " . ($result ? $result->data : 'Could not decode') . PHP_EOL;

        unlink($tempPath);
    } catch (\Exception $e) {
        echo "Error decoding QR file: " . $e->getMessage() . PHP_EOL;
    }
} else {
    echo "QR file does not exist!\n";
}

echo PHP_EOL;
echo "Fixing by making student_number match qr_code_value...\n";

// Update the student to have consistent data
$student->student_number = $student->qr_code_value;
$student->save();

echo "Updated student_number to: " . $student->student_number . PHP_EOL;

// Regenerate the QR file with the correct name
$newQrPath = 'qrcodes/' . $student->student_number . '-fixed.svg';
$qrWritten = \Illuminate\Support\Facades\Storage::disk('public')->put(
    $newQrPath,
    \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(200)->generate($student->qr_code_value)
);

if ($qrWritten) {
    // Delete old QR file
    \Illuminate\Support\Facades\Storage::disk('public')->delete($student->qr_code_path);

    $student->qr_code_path = $newQrPath;
    $student->save();

    echo "QR file regenerated: " . $newQrPath . PHP_EOL;
} else {
    echo "Failed to regenerate QR file\n";
}

echo PHP_EOL;
echo "Final Data:\n";
echo "Student Number: " . $student->student_number . PHP_EOL;
echo "QR Code Value: " . $student->qr_code_value . PHP_EOL;
echo "QR Code Path: " . $student->qr_code_path . PHP_EOL;
