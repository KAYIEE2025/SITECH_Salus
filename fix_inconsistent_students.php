<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Fixing Inconsistent Student Data\n";
echo "================================\n\n";

$mismatched = \App\Models\Student::whereRaw('student_number != qr_code_value')
    ->whereNotNull('qr_code_value')
    ->get();

echo "Found {$mismatched->count()} students with mismatched data:\n";

foreach ($mismatched as $student) {
    echo "\nStudent ID {$student->id}:";
    echo "  student_number='{$student->student_number}'";
    echo "  qr_code_value='{$student->qr_code_value}'";

    // For old students, we should trust the QR code value since that's what was decoded from their actual QR
    if ($student->student_type === 'old') {
        echo "  -> Fixing: making student_number match qr_code_value";
        $student->student_number = $student->qr_code_value;
        $student->save();
        echo "  -> Done!";
    } else {
        echo "  -> Skipping (new student, QR format is different)";
    }
}

echo "\n\nDone fixing old student data mismatches.\n";
