<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Regenerating Old Student QR Codes\n";
echo "==================================\n\n";

$oldStudents = \App\Models\Student::where('student_type', 'old')
    ->whereNotNull('qr_code_value')
    ->get();

echo "Found {$oldStudents->count()} old students with QR codes.\n\n";

$regenerated = 0;
$failed = 0;

foreach ($oldStudents as $student) {
    echo "Processing Student ID {$student->id} ({$student->student_number})...\n";

    try {
        // Keep the existing QR code value (the actual encoded data)
        // But regenerate the QR image file using the new high-quality method
        $qrValue = $student->qr_code_value;

        // Generate new QR code using the same method as new students
        $newQrPath = 'qrcodes/' . $qrValue . '-regenerated.svg';
        $qrWritten = \Illuminate\Support\Facades\Storage::disk('public')->put(
            $newQrPath,
            \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(300)->generate($qrValue)
        );

        if ($qrWritten) {
            // Delete old QR file
            if ($student->qr_code_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($student->qr_code_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($student->qr_code_path);
                echo "  Deleted old QR file: {$student->qr_code_path}\n";
            }

            // Update student record
            $student->qr_code_path = $newQrPath;
            $student->save();

            echo "  ✓ Generated new QR: {$newQrPath}\n";
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

echo "==================================\n";
echo "Regeneration Complete\n";
echo "Success: {$regenerated}\n";
echo "Failed: {$failed}\n";
echo "==================================\n";

if ($regenerated > 0) {
    echo "\n✓ Old student QR codes have been regenerated with high quality.\n";
    echo "  Please print the new QR codes from the student profile pages.\n";
}
