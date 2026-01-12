<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // SQLite performance optimizations
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA journal_mode = WAL;');
            DB::statement('PRAGMA synchronous = NORMAL;');
            DB::statement('PRAGMA cache_size = -20000;');
            DB::statement('PRAGMA temp_store = MEMORY;');
            DB::statement('PRAGMA mmap_size = 2147483648;');
        }
    }
}
