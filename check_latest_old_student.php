<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking Latest Old Student\n";
echo "==========================\n\n";

$latestOldStudent = \App\Models\Student::where('student_type', 'old')->latest()->first();

if ($latestOldStudent) {
    echo 'Latest Old Student:' . PHP_EOL;
    echo 'ID: ' . $latestOldStudent->id . PHP_EOL;
    echo 'Name: ' . $latestOldStudent->full_name . PHP_EOL;
    echo 'Student Number: ' . $latestOldStudent->student_number . PHP_EOL;
    echo 'QR Code Value: ' . $latestOldStudent->qr_code_value . PHP_EOL;
    echo 'QR Code Path: ' . $latestOldStudent->qr_code_path . PHP_EOL;
    echo 'Created At: ' . $latestOldStudent->created_at . PHP_EOL;
    echo PHP_EOL;

    // Check if the QR file exists and is regenerated
    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($latestOldStudent->qr_code_path)) {
        echo 'QR File Status: Exists ✓' . PHP_EOL;
        if (str_contains($latestOldStudent->qr_code_path, 'regenerated')) {
            echo 'File Type: REGENERATED ✓' . PHP_EOL;
        } else {
            echo 'File Type: ORIGINAL FILE ✗ (not regenerated)' . PHP_EOL;
        }
    } else {
        echo 'QR File Status: NOT FOUND ✗' . PHP_EOL;
    }
} else {
    echo 'No old students found.' . PHP_EOL;
}
