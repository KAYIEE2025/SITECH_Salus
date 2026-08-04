<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking Old Student QR Codes\n";
echo "=============================\n\n";

$oldStudents = \App\Models\Student::where('student_type', 'old')
    ->whereNotNull('qr_code_path')
    ->limit(5)
    ->get(['id', 'student_number', 'qr_code_value', 'qr_code_path']);

foreach ($oldStudents as $student) {
    echo 'Student ID: ' . $student->id . PHP_EOL;
    echo 'Student Number: ' . $student->student_number . PHP_EOL;
    echo 'QR Code Value: ' . $student->qr_code_value . PHP_EOL;
    echo 'QR Code Path: ' . $student->qr_code_path . PHP_EOL;
    echo 'File Exists: ' . (\Illuminate\Support\Facades\Storage::disk('public')->exists($student->qr_code_path) ? 'Yes' : 'No') . PHP_EOL;
    echo PHP_EOL;
}

echo "\nChecking which old students have mismatched data:\n";
echo "===============================================\n";

$mismatched = \App\Models\Student::where('student_type', 'old')
    ->whereRaw('student_number != qr_code_value')
    ->whereNotNull('qr_code_value')
    ->get(['id', 'student_number', 'qr_code_value']);

foreach ($mismatched as $student) {
    echo "Student ID {$student->id}: student_number='{$student->student_number}' but qr_code_value='{$student->qr_code_value}'" . PHP_EOL;
}

echo "\nTotal mismatched old students: " . $mismatched->count() . PHP_EOL;
