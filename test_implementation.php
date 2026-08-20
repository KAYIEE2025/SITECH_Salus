<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Implementation ===\n\n";

// Test 1: Check database schema
echo "Test 1: Database Schema\n";
echo "-------------------------\n";
try {
    $schema = Illuminate\Support\Facades\Schema::getColumnListing('users');
    if (in_array('must_change_password', $schema)) {
        echo "✓ must_change_password column exists in users table\n";
    } else {
        echo "✗ must_change_password column NOT found in users table\n";
    }
} catch (Exception $e) {
    echo "✗ Error checking schema: " . $e->getMessage() . "\n";
}

// Test 2: Check User model
echo "\nTest 2: User Model\n";
echo "------------------\n";
try {
    $user = new App\Models\User();
    $fillable = $user->getFillable();
    if (in_array('must_change_password', $fillable)) {
        echo "✓ must_change_password is in User fillable array\n";
    } else {
        echo "✗ must_change_password NOT in User fillable array\n";
    }

    $casts = $user->getCasts();
    if (array_key_exists('must_change_password', $casts)) {
        echo "✓ must_change_password is cast to boolean\n";
    } else {
        echo "✗ must_change_password NOT cast to boolean\n";
    }
} catch (Exception $e) {
    echo "✗ Error checking User model: " . $e->getMessage() . "\n";
}

// Test 3: Check current users
echo "\nTest 3: Current Users\n";
echo "--------------------\n";
try {
    $users = App\Models\User::all();
    echo "Total users: " . $users->count() . "\n";
    foreach ($users as $user) {
        echo "  - {$user->name} ({$user->email}): must_change_password = " . ($user->must_change_password ? 'true' : 'false') . "\n";
    }
} catch (Exception $e) {
    echo "✗ Error checking users: " . $e->getMessage() . "\n";
}

// Test 4: Check middleware registration
echo "\nTest 4: Middleware Registration\n";
echo "-------------------------------\n";
try {
    if (class_exists('App\Http\Middleware\EnsureStudentMustChangePassword')) {
        echo "✓ EnsureStudentMustChangePassword middleware class exists\n";
    } else {
        echo "✗ EnsureStudentMustChangePassword middleware class NOT found\n";
    }

    $middlewareAliases = app('router')->getMiddleware();
    if (array_key_exists('must_change_password', $middlewareAliases)) {
        echo "✓ must_change_password middleware is registered\n";
        echo "  Class: " . $middlewareAliases['must_change_password'] . "\n";
    } else {
        echo "✗ must_change_password middleware NOT registered via alias\n";
        echo "  Note: Laravel 11 may use different middleware registration\n";
    }
} catch (Exception $e) {
    echo "✗ Error checking middleware: " . $e->getMessage() . "\n";
}

// Test 5: Check routes
echo "\nTest 5: Routes\n";
echo "-------------\n";
try {
    $routes = Illuminate\Support\Facades\Route::getRoutes();
    $studentRoutes = [];
    foreach ($routes as $route) {
        if (strpos($route->uri, 'student') !== false) {
            $studentRoutes[] = [
                'method' => implode('|', $route->methods),
                'uri' => $route->uri,
                'name' => $route->getName(),
                'middleware' => implode(', ', $route->middleware())
            ];
        }
    }

    $forcedPasswordRoute = collect($studentRoutes)->firstWhere('name', 'student.forced-password-change');
    if ($forcedPasswordRoute) {
        echo "✓ Forced password change route exists:\n";
        echo "  - Method: {$forcedPasswordRoute['method']}\n";
        echo "  - URI: {$forcedPasswordRoute['uri']}\n";
        echo "  - Name: {$forcedPasswordRoute['name']}\n";
        echo "  - Middleware: {$forcedPasswordRoute['middleware']}\n";
    } else {
        echo "✗ Forced password change route NOT found\n";
    }

    $dashboardRoute = collect($studentRoutes)->firstWhere('name', 'student.dashboard');
    if ($dashboardRoute) {
        echo "\n✓ Student dashboard route exists:\n";
        echo "  - Method: {$dashboardRoute['method']}\n";
        echo "  - URI: {$dashboardRoute['uri']}\n";
        echo "  - Name: {$dashboardRoute['name']}\n";
        echo "  - Middleware: {$dashboardRoute['middleware']}\n";
    } else {
        echo "✗ Student dashboard route NOT found\n";
    }
} catch (Exception $e) {
    echo "✗ Error checking routes: " . $e->getMessage() . "\n";
}

// Test 6: Check controller exists
echo "\nTest 6: Controller\n";
echo "------------------\n";
try {
    if (class_exists('App\Http\Controllers\Student\ForcedPasswordChangeController')) {
        echo "✓ ForcedPasswordChangeController exists\n";
        $controller = new App\Http\Controllers\Student\ForcedPasswordChangeController();
        $methods = get_class_methods($controller);
        echo "  Methods: " . implode(', ', $methods) . "\n";
    } else {
        echo "✗ ForcedPasswordChangeController NOT found\n";
    }
} catch (Exception $e) {
    echo "✗ Error checking controller: " . $e->getMessage() . "\n";
}

// Test 7: Check view exists
echo "\nTest 7: View\n";
echo "------------\n";
try {
    if (view()->exists('student.forced-password-change')) {
        echo "✓ Forced password change view exists\n";
    } else {
        echo "✗ Forced password change view NOT found\n";
    }
} catch (Exception $e) {
    echo "✗ Error checking view: " . $e->getMessage() . "\n";
}

echo "\n=== Implementation Test Complete ===\n";