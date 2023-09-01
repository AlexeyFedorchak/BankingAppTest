<?php

namespace Database\Factories;

use App\Models\Statement;
use App\Models\User;
use App\Models\Withdraw;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class StatementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create()->id,
            'owner_id' => Withdraw::factory()->create()->id,
            'owner_type' => Withdraw::class,
            'type' => Statement::TYPE_DEBIT,
            'details' => Statement::TYPE_DEBIT,
            'balance' => rand(1, 20)
        ];
    }
}
