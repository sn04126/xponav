<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Plan;
use App\Models\Exhibit;
use App\Models\InteractiveSession;
use Carbon\Carbon;

class TestContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding test data...');

        // 1. Create Users
        $admin = User::create([
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'phone' => '1234567890',
            'country' => 'USA',
            'email_verified_at' => now(),
        ]);
        $this->command->info('Admin user created');

        $user = User::create([
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'status' => 'active',
            'phone' => '0987654321',
            'country' => 'UK',
            'email_verified_at' => now(),
        ]);
        $this->command->info('Regular user created');

        // 2. Create Plans
        $basicPlan = Plan::create([
            'name' => 'Basic Pass',
            'total_fee' => 29.99,
            'daily_fee' => 5.00,
            'features' => ['General Access', 'Audio Guide'],
            'status' => 'active',
        ]);
        $this->command->info('Basic plan created');

        $premiumPlan = Plan::create([
            'name' => 'Premium Experience',
            'total_fee' => 99.99,
            'daily_fee' => 15.00,
            'features' => ['All Access', 'Priority Entry', 'Private Tour', 'AR Experience'],
            'status' => 'active',
        ]);
        $this->command->info('Premium plan created');

        // 3. Create Exhibits
        $exhibit1 = Exhibit::create([
            'title' => 'The Renaissance Revival',
            'artist_name' => 'Leonardo da Vinci',
            'category' => 'Classic',
            'location' => 'Main Hall',
            'status' => 'ongoing',
            'description' => 'A journey through the masterpieces of the Renaissance period.',
            'start_date' => Carbon::now()->subDays(10)->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays(20)->format('Y-m-d'),
            'is_promoted' => true,
        ]);
        $this->command->info('Exhibit 1 created');

        $exhibit2 = Exhibit::create([
            'title' => 'Modern Abstract',
            'artist_name' => 'Jackson Pollock',
            'category' => 'Modern',
            'location' => 'Gallery B',
            'status' => 'upcoming',
            'description' => 'Exploring chaos and order through abstract expressionism.',
            'start_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays(35)->format('Y-m-d'),
            'is_promoted' => false,
        ]);
        $this->command->info('Exhibit 2 created');

        // 4. Create Interactive Session
        InteractiveSession::create([
            'name' => 'Oil Painting Workshop',
            'date' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'time' => '14:00:00',
            'location' => 'Studio 1',
            'type' => 'Workshop',
            'hosted_by' => 'Sarah Jenkins',
            'description' => 'Learn the basics of oil painting with a professional artist.',
        ]);
        $this->command->info('Interactive session created');

        $this->command->info('✓ All test data seeded successfully!');
    }
}
