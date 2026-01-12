<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user (idempotent - won't duplicate)
        User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $this->call([
            ClientSeeder::class,
            SupplierSeeder::class,
            TechpackSeeder::class,
            QuoteSeeder::class,
        ]);
    }
}
