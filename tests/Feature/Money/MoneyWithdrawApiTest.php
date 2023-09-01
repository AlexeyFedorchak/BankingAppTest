<?php

namespace Tests\Feature\Money;

use App\Models\{
    Balance,
    withdraw,
    Statement,
    User
};
use Illuminate\Support\Str;
use Tests\Feature\BaseTestCase;

class MoneyWithdrawApiTest extends BaseTestCase
{
    /**
     * Assert that the amount is required.
     *
     * @test
     * @return void
     */
    public function assert_amount_field_is_required()
    {
        $this->actingAs(User::factory()->create())
            ->postJson(route('api.money.withdraw'))
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('amount')
            ->assertJson([
                'message' => 'The amount field is required.',
                'errors' => [
                    'amount' => [
                        'The amount field is required.',
                    ]
                ]
            ]);
    }

    /**
     * Assert that the amount field must be a number.
     *
     * @test
     * @return void
     */
    public function assert_amount_field_must_be_numeric()
    {
        $this->actingAs(User::factory()->create())
            ->postJson(route('api.money.withdraw'), ['amount' => Str::random()])
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('amount')
            ->assertJson([
                'message' => 'The amount field must be a number.',
                'errors' => [
                    'amount' => [
                        'The amount field must be a number.',
                    ]
                ]
            ]);
    }

    /**
     * Assert that the amount field must be at least 1.
     *
     * @test
     * @return void
     */
    public function assert_amount_field_must_be_at_least_1()
    {
        $this->actingAs(User::factory()->create())
            ->postJson(route('api.money.withdraw'), ['amount' => rand(-100, 0)])
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('amount')
            ->assertJson([
                'message' => 'The amount field must be at least 1.',
                'errors' => [
                    'amount' => [
                        'The amount field must be at least 1.',
                    ]
                ]
            ]);
    }

    /**
     * Assert that the amount field must not be greater than 1000000
     *
     * @test
     * @return void
     */
    public function assert_amount_field_have_max_value()
    {
        $this->actingAs(User::factory()->create())
            ->postJson(route('api.money.withdraw'), ['amount' => rand(1000001, 1000010)])
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('amount')
            ->assertJson([
                'message' => 'The amount field must not be greater than 1000000.',
                'errors' => [
                    'amount' => [
                        'The amount field must not be greater than 1000000.',
                    ]
                ]
            ]);
    }

    /**
     * Assert that the withdraw and statement were stored
     *
     * @test
     * @return void
     */
    public function assert_withdraw_and_statement_were_stored()
    {
        $user = User::factory()->create();
        $balance = Balance::factory()->create(['user_id' => $user->id, 'amount' => 100]);
        $amount = rand(1, 100);

        $this->actingAs($user)
            ->postJson(route('api.money.withdraw'), ['amount' => $amount])
            ->assertStatus(200)
            ->assertJson(['message' => 'success']);

        $this->assertDatabaseHas(Withdraw::class, [
            'user_id' => $user->id,
            'amount' => $amount,
        ]);

        $withdraw = $user->withdraws()->where('amount', $amount)->first();

        $this->assertDatabaseHas(Statement::class, [
            'user_id' => $user->id,
            'type' => Statement::TYPE_DEBIT,
            'details' => Statement::TYPE_DEBIT,
            'owner_id' => $withdraw->id,
            'owner_type' => $withdraw::class,
            'balance' => $balance->amount - $amount,
        ]);

        $expectedAmount = $balance->amount - $amount;
        $balance->refresh();
        $this->assertEquals($expectedAmount, $balance->amount);
    }
}
