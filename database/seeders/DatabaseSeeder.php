<?php

namespace Database\Seeders;

use App\Models\Refund;
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
        $user = User::create([
            'name' => 'John Romano',
            'email' => 'none@none.com',
            'password' => bcrypt('password'), // Make sure to hash the password
        ]);
                // Create 10 users
        User::factory(10)
            ->has(Refund::factory()->count(500)) // Each user will have 50 refunds
            ->create();
    }
}
