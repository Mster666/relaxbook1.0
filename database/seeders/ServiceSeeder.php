<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Service::create([
            'name' => 'Swedish Massage',
            'description' => 'A gentle, full-body massage that relaxes muscle tension and improves circulation.',
            'price' => 500.00,
            'duration_minutes' => 60,
            'is_active' => true,
        ]);

        Service::create([
            'name' => 'Deep Tissue Massage',
            'description' => 'A massage technique that focuses on the deeper layers of muscle tissue.',
            'price' => 700.00,
            'duration_minutes' => 60,
            'is_active' => true,
        ]);

        Service::create([
            'name' => 'Aromatherapy',
            'description' => 'Massage using essential oils to promote healing and a feeling of well-being and relaxation.',
            'price' => 800.00,
            'duration_minutes' => 90,
            'is_active' => true,
        ]);
    }
}
