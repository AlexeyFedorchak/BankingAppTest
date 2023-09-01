<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class MoneyTransferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sender_user_id' => User::factory()->create()->id,
            'receiver_user_id' => User::factory()->create()->id,
            'amount' => rand(1, 20)
        ];
    }
}
