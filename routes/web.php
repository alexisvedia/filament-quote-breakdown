<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Debug route to test Laravel components
Route::get('/debug', function () {
    $checks = [];

    // Check database connection
    try {
        \DB::connection()->getPdo();
        $checks['database'] = 'OK';
    } catch (\Exception $e) {
        $checks['database'] = 'FAILED: ' . $e->getMessage();
    }

    // Check session driver
    $checks['session_driver'] = config('session.driver');

    // Check cache driver
    $checks['cache_driver'] = config('cache.default');

    // Check if storage is writable
    $checks['storage_writable'] = is_writable(storage_path()) ? 'OK' : 'NOT WRITABLE';

    // Check if User model exists
    try {
        $userCount = \App\Models\User::count();
        $checks['users_table'] = "OK ($userCount users)";
    } catch (\Exception $e) {
        $checks['users_table'] = 'FAILED: ' . $e->getMessage();
    }

    // Check Filament panel provider
    try {
        $panel = \Filament\Facades\Filament::getDefaultPanel();
        $checks['filament_panel'] = $panel ? 'OK (id: ' . $panel->getId() . ')' : 'NOT FOUND';
    } catch (\Exception $e) {
        $checks['filament_panel'] = 'FAILED: ' . $e->getMessage();
    }

    return response()->json($checks);
});
