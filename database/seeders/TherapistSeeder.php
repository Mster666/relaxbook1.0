<?php

namespace Database\Seeders;

use App\Models\Therapist;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TherapistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Therapist::create([
            'name' => 'Sarah Johnson',
            'email' => 'sarah@example.com',
            'phone' => '09171234567',
            'bio' => 'Certified massage therapist with 5 years of experience in Swedish and Deep Tissue massage.',
            'is_active' => true,
        ]);

        Therapist::create([
            'name' => 'Mike Santos',
            'email' => 'mike@example.com',
            'phone' => '09187654321',
            'bio' => 'Specializes in Sports Massage and reflexology.',
            'is_active' => true,
        ]);
    }
}
