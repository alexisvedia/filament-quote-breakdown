<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Debug route - no middleware
Route::get('/debug', function () {
    $checks = [];

    // Check APP_KEY
    $appKey = config('app.key');
    $checks['app_key'] = $appKey ? 'SET (length: ' . strlen($appKey) . ')' : 'NOT SET!';

    // Check environment
    $checks['app_env'] = config('app.env');
    $checks['app_debug'] = config('app.debug') ? 'true' : 'false';

    // Check session config
    $checks['session_driver'] = config('session.driver');
    $checks['session_path'] = config('session.files');

    // Check cache config
    $checks['cache_driver'] = config('cache.default');

    // Check storage paths
    $checks['storage_path'] = storage_path();
    $checks['storage_writable'] = is_writable(storage_path()) ? 'OK' : 'NOT WRITABLE';
    $checks['views_path'] = storage_path('framework/views');
    $checks['views_writable'] = is_writable(storage_path('framework/views')) ? 'OK' : 'NOT WRITABLE';
    $checks['sessions_path'] = storage_path('framework/sessions');
    $checks['sessions_writable'] = is_writable(storage_path('framework/sessions')) ? 'OK' : 'NOT WRITABLE';

    // Check database connection
    try {
        \DB::connection()->getPdo();
        $checks['database'] = 'OK';
    } catch (\Exception $e) {
        $checks['database'] = 'FAILED: ' . $e->getMessage();
    }

    // Check if User model exists
    try {
        $userCount = \App\Models\User::count();
        $checks['users_table'] = "OK ($userCount users)";
    } catch (\Exception $e) {
        $checks['users_table'] = 'FAILED: ' . $e->getMessage();
    }

    return response()->json($checks);
})->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
