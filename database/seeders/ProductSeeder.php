<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (range(1, 1000) as $index) {
            Product::create([
                'name' => fake()->name(),
                'detail' => fake()->sentences(rand(1,10),true)
            ]);
        }
    }
}
