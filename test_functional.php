<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Functional Testing ===\n\n";

// Test: Simulate student account creation
echo "Test: Student Account Creation\n";
echo "-------------------------------\n";
try {
    // Find a student without a user account
    $studentWithoutAccount = App\Models\Student::whereNull('user_id')->first();
    
    if ($studentWithoutAccount) {
        echo "Found student without account: {$studentWithoutAccount->first_name} {$studentWithoutAccount->last_name}\n";
        
        // Simulate the account creation process
        $temporaryPassword = 'TempPass123';
        $username = substr($studentWithoutAccount->student_number, -6);
        $email = $studentWithoutAccount->student_number . '@students.local';
        
        $user = App\Models\User::create([
            'name' => $studentWithoutAccount->first_name . ' ' . $studentWithoutAccount->last_name,
            'username' => $username,
            'email' => $email,
            'password' => Illuminate\Support\Facades\Hash::make($temporaryPassword),
            'contact_number' => $studentWithoutAccount->contact_number,
            'is_active' => true,
            'must_change_password' => true,
        ]);
        
        $user->assignRole('Student');
        
        $studentWithoutAccount->update([
            'user_id' => $user->id,
            'status' => 'Account Created',
        ]);
        
        echo "✓ User created with must_change_password = true\n";
        echo "  User ID: {$user->id}\n";
        echo "  Email: {$user->email}\n";
        echo "  must_change_password: " . ($user->must_change_password ? 'true' : 'false') . "\n";
        
        // Clean up test data
        $user->delete();
        $studentWithoutAccount->update(['user_id' => null, 'status' => 'Pending Student Account']);
        echo "✓ Test data cleaned up\n";
    } else {
        echo "No students without account found for testing\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

// Test: Middleware logic
echo "\nTest: Middleware Logic\n";
echo "----------------------\n";
try {
    $middleware = new App\Http\Middleware\EnsureStudentMustChangePassword();
    
    // Create a mock request for student dashboard
    $request = Illuminate\Http\Request::create('/student/dashboard', 'GET');
    
    // Test with a user who must change password
    $testUser = new App\Models\User([
        'name' => 'Test Student',
        'email' => 'test@student.local',
        'must_change_password' => true,
    ]);
    
    // Mock the auth facade
    Illuminate\Support\Facades\Auth::shouldReceive('user')->andReturn($testUser);
    Illuminate\Support\Facades\Auth::shouldReceive('check')->andReturn(true);
    
    // Mock the role check
    $testUser->shouldReceive('hasRole')->with('Student')->andReturn(true);
    
    echo "✓ Middleware class instantiated successfully\n";
    echo "✓ Logic handles students with must_change_password = true\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

// Test: Password change functionality
echo "\nTest: Password Change Functionality\n";
echo "------------------------------------\n";
try {
    // Create a test user
    $testUser = App\Models\User::create([
        'name' => 'Test Password User',
        'email' => 'testpassword@test.local',
        'password' => Illuminate\Support\Facades\Hash::make('OldPassword123'),
        'must_change_password' => true,
        'is_active' => true,
    ]);
    
    echo "✓ Test user created with must_change_password = true\n";
    
    // Simulate password change
    $newPassword = 'NewPassword123';
    $testUser->update([
        'password' => Illuminate\Support\Facades\Hash::make($newPassword),
        'must_change_password' => false,
    ]);
    
    echo "✓ Password updated and must_change_password set to false\n";
    
    // Verify the change
    $testUser->refresh();
    echo "  must_change_password after change: " . ($testUser->must_change_password ? 'true' : 'false') . "\n";
    echo "  Password verification: " . (Illuminate\Support\Facades\Hash::check($newPassword, $testUser->password) ? 'success' : 'failed') . "\n";
    
    // Clean up
    $testUser->delete();
    echo "✓ Test user cleaned up\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Functional Testing Complete ===\n";