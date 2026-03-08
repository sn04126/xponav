<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Daily',
                'total_fee' => 99,
                'daily_fee' => 99,
                'features' => ['Full AR Navigation', 'All Exhibition Areas', 'Real-time Path Guidance', 'QR Code Scanning'],
                'status' => 'active',
            ],
            [
                'name' => 'Weekly',
                'total_fee' => 499,
                'daily_fee' => 71.29,
                'features' => ['Full AR Navigation', 'All Exhibition Areas', 'Real-time Path Guidance', 'QR Code Scanning', 'Priority Support', 'Offline Mode'],
                'status' => 'active',
            ],
            [
                'name' => 'Monthly',
                'total_fee' => 1499,
                'daily_fee' => 49.97,
                'features' => ['Full AR Navigation', 'All Exhibition Areas', 'Real-time Path Guidance', 'QR Code Scanning', 'Priority Support', 'Offline Mode', 'Analytics Dashboard', 'Unlimited Sessions'],
                'status' => 'active',
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(
                ['name' => $plan['name']],
                $plan
            );
        }
    }
}
