<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (app()->environment(['local', 'testing']) && env('SEED_DEMO_ACCOUNTS', false)) {
            \App\Models\Admin::query()->firstOrCreate(
                ['email' => 'admin@admin.com'],
                [
                    'name' => 'Admin User',
                    'password' => Hash::make('password'),
                ],
            );

            User::query()->firstOrCreate(
                ['email' => 'user@user.com'],
                [
                    'name' => 'Regular User',
                    'password' => Hash::make('password'),
                ],
            );
        }

        $this->call([
            SuperAdminSeeder::class,
            TherapistSeeder::class,
            ServiceSeeder::class,
        ]);
    }
}
