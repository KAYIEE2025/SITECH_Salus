<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Verifying Old Student QR Code Values\n";
echo "====================================\n\n";

$oldStudents = \App\Models\Student::where('student_type', 'old')
    ->whereNotNull('qr_code_value')
    ->get(['id', 'student_number', 'qr_code_value', 'qr_code_path']);

echo "Checking QR code values in database:\n\n";

foreach ($oldStudents as $student) {
    echo "Student ID {$student->id}:\n";
    echo "  Student Number: {$student->student_number}\n";
    echo "  QR Code Value: {$student->qr_code_value}\n";
    echo "  QR Code Path: {$student->qr_code_path}\n";

    // Check if the new QR file exists and can be decoded
    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($student->qr_code_path)) {
        echo "  QR File: Exists ✓\n";

        try {
            $fileContent = \Illuminate\Support\Facades\Storage::disk('public')->get($student->qr_code_path);
            $tempPath = tempnam(sys_get_temp_dir(), 'qr');
            file_put_contents($tempPath, $fileContent);

            $qrCode = new \chillerlan\QRCode\QRCode(new \chillerlan\QRCode\QROptions);
            $result = $qrCode->readFromFile($tempPath);

            if ($result) {
                $decodedValue = $result->data;
                echo "  Decoded from file: {$decodedValue}\n";

                if ($decodedValue === $student->qr_code_value) {
                    echo "  Match: ✓ Database and file match\n";
                } else {
                    echo "  Match: ✗ MISMATCH! Database has '{$student->qr_code_value}' but file contains '{$decodedValue}'\n";
                }
            } else {
                echo "  Decoded from file: Failed to decode\n";
            }

            unlink($tempPath);
        } catch (\Exception $e) {
            echo "  Decode error: " . $e->getMessage() . "\n";
        }
    } else {
        echo "  QR File: Not found ✗\n";
    }

    echo "\n";
}

echo "\nIMPORTANT: If you still have the original old QR code images, please upload one so I can decode it and compare it to the database values.\n";
