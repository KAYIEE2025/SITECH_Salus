<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking Current QR Code Files for Old Students\n";
echo "================================================\n\n";

$oldStudents = \App\Models\Student::where('student_type', 'old')
    ->whereNotNull('qr_code_value')
    ->get(['id', 'student_number', 'qr_code_value', 'qr_code_path']);

foreach ($oldStudents as $student) {
    echo "Student ID {$student->id} ({$student->student_number}):\n";
    echo "  QR Code Value: {$student->qr_code_value}\n";
    echo "  Current QR Path: {$student->qr_code_path}\n";

    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($student->qr_code_path)) {
        $size = \Illuminate\Support\Facades\Storage::disk('public')->size($student->qr_code_path);
        $extension = pathinfo($student->qr_code_path, PATHINFO_EXTENSION);
        echo "  File Status: Exists ({$extension}, {$size} bytes)\n";

        // Check if it's the regenerated file
        if (str_contains($student->qr_code_path, 'regenerated')) {
            echo "  File Type: REGENERATED ✓\n";
        } else {
            echo "  File Type: OLD FILE ✗ (needs regeneration)\n";
        }
    } else {
        echo "  File Status: NOT FOUND ✗\n";
    }
    echo "\n";
}
