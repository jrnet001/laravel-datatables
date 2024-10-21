<?php

namespace Database\Factories;

use App\Models\Refund;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Refund>
 */
class RefundFactory extends Factory
{
    protected $model = Refund::class;

    public function definition(): array
    {
        return [
            'firstName' => $this->faker->firstName,
            'lastName' => $this->faker->lastName,
            'email' => $this->faker->freeEmail,
            'phone' => $this->faker->phoneNumber,
            'libraryCard' => $this->faker->creditCardNumber,
            'amount' => $this->faker->randomFloat(2, 5, 100), // price between 5 and 100;
            'notes' => $this->faker->sentences(3, true),
            'refund_status' => mt_rand(0, 3),
            'user_id' => User::factory(), // Create a user for each refund
        ];
    }
}
