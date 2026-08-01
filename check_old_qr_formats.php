<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Analyzing Old Student QR Code Formats\n";
echo "====================================\n\n";

$oldStudents = \App\Models\Student::where('student_type', 'old')
    ->whereNotNull('qr_code_value')
    ->get(['id', 'student_number', 'qr_code_value', 'qr_code_path']);

echo "Total old students with QR codes: " . $oldStudents->count() . "\n\n";

$formats = [];
foreach ($oldStudents as $student) {
    $qrValue = $student->qr_code_value;
    $format = 'unknown';

    if (str_contains($qrValue, 'SITech-STUDENT|')) {
        $format = 'new_format';
    } elseif (preg_match('/^\d{8}-\d+$/', $qrValue)) {
        $format = 'date_dash_number';
    } elseif (preg_match('/^\d+$/', $qrValue)) {
        $format = 'plain_number';
    } elseif (str_contains($qrValue, '|')) {
        $format = 'pipe_separated';
    }

    if (!isset($formats[$format])) {
        $formats[$format] = [];
    }
    $formats[$format][] = [
        'id' => $student->id,
        'student_number' => $student->student_number,
        'qr_value' => $qrValue,
        'length' => strlen($qrValue)
    ];
}

echo "QR Code Format Analysis:\n";
foreach ($formats as $format => $students) {
    echo "\n$format (" . count($students) . " students):\n";
    foreach (array_slice($students, 0, 3) as $student) {
        echo "  ID {$student['id']}: {$student['qr_value']} (length: {$student['length']})\n";
    }
    if (count($students) > 3) {
        echo "  ... and " . (count($students) - 3) . " more\n";
    }
}

echo "\n\nChecking QR file formats:\n";
foreach ($oldStudents->take(5) as $student) {
    $path = $student->qr_code_path;
    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $size = \Illuminate\Support\Facades\Storage::disk('public')->size($path);
        echo "ID {$student->id}: {$path} ({$extension}, {$size} bytes)\n";
    } else {
        echo "ID {$student->id}: File not found\n";
    }
}
