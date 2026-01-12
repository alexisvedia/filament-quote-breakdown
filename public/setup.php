<?php
/**
 * One-time setup script - DELETE AFTER USE
 * Creates database tables and admin user
 */

header('Content-Type: application/json');

// Security: only allow if no users exist
require dirname(__DIR__) . '/vendor/autoload.php';

$app = require dirname(__DIR__) . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$results = [];

try {
    // Check if users table exists and has users
    $userCount = \App\Models\User::count();

    if ($userCount > 0) {
        $results['error'] = 'Setup already completed. Users exist: ' . $userCount;
        $results['hint'] = 'Delete this file for security!';
        echo json_encode($results, JSON_PRETTY_PRINT);
        exit;
    }
} catch (\Exception $e) {
    // Table doesn't exist, need to migrate
    $results['users_check'] = 'Table does not exist, will migrate';
}

// Run migrations
try {
    Artisan::call('migrate', ['--force' => true]);
    $results['migrate'] = Artisan::output();
} catch (\Exception $e) {
    $results['migrate_error'] = $e->getMessage();
}

// Run seeders
try {
    Artisan::call('db:seed', ['--force' => true]);
    $results['seed'] = Artisan::output();
} catch (\Exception $e) {
    $results['seed_error'] = $e->getMessage();
}

// Create admin user
try {
    $user = \App\Models\User::firstOrCreate(
        ['email' => 'admin@admin.com'],
        [
            'name' => 'Admin',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]
    );
    $results['admin_user'] = [
        'created' => $user->wasRecentlyCreated,
        'email' => $user->email,
        'id' => $user->id,
    ];
} catch (\Exception $e) {
    $results['admin_user_error'] = $e->getMessage();
}

$results['status'] = 'SETUP COMPLETE';
$results['credentials'] = [
    'email' => 'admin@admin.com',
    'password' => 'password',
];
$results['warning'] = 'DELETE THIS FILE (setup.php) FOR SECURITY!';

echo json_encode($results, JSON_PRETTY_PRINT);
