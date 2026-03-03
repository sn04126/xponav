<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class SimpleTestSeeder extends Seeder
{
    public function run(): void
    {
        try {
            $this->command->info('Starting seeder...');
            
            // Test direct DB insert first
            DB::table('users')->insert([
                'name' => 'Test User',
                'username' => 'testuser',
                'email' => 'test@test.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $this->command->info('User created successfully!');
            
        } catch (\Exception $e) {
            $this->command->error('Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
