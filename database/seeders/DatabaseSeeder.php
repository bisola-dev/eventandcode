<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@eventsandcode.com',
            'password' => bcrypt('eventsandcode'),
            'role' => 'super_admin',
        ]);
    }
}
