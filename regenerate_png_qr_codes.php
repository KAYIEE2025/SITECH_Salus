<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Regenerating Old Student QR Codes in PNG Format\n";
echo "===============================================\n\n";

$oldStudents = \App\Models\Student::where('student_type', 'old')
    ->whereNotNull('qr_code_value')
    ->get();

echo "Found {$oldStudents->count()} old students.\n\n";

$regenerated = 0;
$failed = 0;

foreach ($oldStudents as $student) {
    echo "Processing Student ID {$student->id} ({$student->student_number})...\n";

    try {
        $qrValue = $student->qr_code_value;

        // Generate PNG using SimpleSoftwareIO QrCode library (same as new students)
        $qrImageData = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(300)->errorCorrection('H')->generate($qrValue);

        $newQrPath = 'qrcodes/' . $qrValue . '-png.png';
        $qrWritten = \Illuminate\Support\Facades\Storage::disk('public')->put($newQrPath, $qrImageData);

        if ($qrWritten) {
            // Delete old QR file
            if ($student->qr_code_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($student->qr_code_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($student->qr_code_path);
                echo "  Deleted old QR file: {$student->qr_code_path}\n";
            }

            $student->qr_code_path = $newQrPath;
            $student->save();

            $size = \Illuminate\Support\Facades\Storage::disk('public')->size($newQrPath);
            echo "  ✓ Generated PNG QR: {$newQrPath} ({$size} bytes)\n";
            $regenerated++;
        } else {
            echo "  ✗ Failed to generate QR code\n";
            $failed++;
        }
    } catch (\Exception $e) {
        echo "  ✗ Error: " . $e->getMessage() . "\n";
        $failed++;
    }

    echo "\n";
}

echo "===============================================\n";
echo "Regeneration Complete\n";
echo "Success: {$regenerated}\n";
echo "Failed: {$failed}\n";
echo "===============================================\n";

if ($regenerated > 0) {
    echo "\n✓ Old student QR codes have been regenerated in PNG format.\n";
    echo "  PNG format may be more compatible with web camera scanners.\n";
}
