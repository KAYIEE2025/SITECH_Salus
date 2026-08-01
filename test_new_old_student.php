<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing QR Code Generation for New Old Student\n";
echo "==============================================\n\n";

// Simulate what happens when adding a new old student
$testQrValue = '20250101-000999'; // Test QR value

echo "Simulating QR code generation for value: {$testQrValue}\n";

// Test the generation method I implemented
$qrPath = 'qrcodes/' . $testQrValue . '-test.svg';
$qrWritten = \Illuminate\Support\Facades\Storage::disk('public')->put(
    $qrPath,
    \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(300)->generate($testQrValue)
);

if ($qrWritten) {
    echo "✓ QR code generated successfully: {$qrPath}\n";
    $size = \Illuminate\Support\Facades\Storage::disk('public')->size($qrPath);
    echo "  File size: {$size} bytes\n";

    // Try to decode it
    try {
        $fileContent = \Illuminate\Support\Facades\Storage::disk('public')->get($qrPath);
        $tempPath = tempnam(sys_get_temp_dir(), 'qr');
        file_put_contents($tempPath, $fileContent);

        $qrCode = new \chillerlan\QRCode\QRCode(new \chillerlan\QRCode\QROptions);
        $result = $qrCode->readFromFile($tempPath);

        if ($result) {
            echo "  Decoded value: {$result->data}\n";
            if ($result->data === $testQrValue) {
                echo "  ✓ QR code contains correct value\n";
            } else {
                echo "  ✗ QR code contains wrong value\n";
            }
        } else {
            echo "  ✗ Could not decode QR code\n";
        }

        unlink($tempPath);
    } catch (\Exception $e) {
        echo "  ✗ Decode error: " . $e->getMessage() . "\n";
    }

    // Clean up
    \Illuminate\Support\Facades\Storage::disk('public')->delete($qrPath);
} else {
    echo "✗ Failed to generate QR code\n";
}

echo "\n==============================================\n";
echo "If this test passes, the generation code is correct.\n";
echo "The issue might be in how the controller processes the request.\n";
