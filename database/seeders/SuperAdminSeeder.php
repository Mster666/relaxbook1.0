<?php

namespace Database\Seeders;

use App\Models\SuperAdmin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if superadmin exists in the NEW table
        if (! SuperAdmin::where('email', 'superadmin@admin.com')->exists()) {
            SuperAdmin::create([
                'name' => 'Super Admin',
                'email' => 'superadmin@admin.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
        }
    }
}
