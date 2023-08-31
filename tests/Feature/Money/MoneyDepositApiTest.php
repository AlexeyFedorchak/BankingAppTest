<?php

namespace Tests\Feature\Money;

use App\Models\{
    Balance,
    Deposit,
    Statement,
    User
};
use Tests\Feature\BaseTestCase;

class MoneyDepositApiTest extends BaseTestCase
{
    /**
     * Validation enabled, so correct data needs to be passed
     *
     * @test
     * @return void
     */
    public function assert_validation_enabled()
    {
        $this->actingAs(User::factory()->create())
            ->postJson(route('api.money.deposit'))
            ->assertStatus(422);
    }

    /**
     * Assert that the deposit and statement were stored
     *
     * @test
     * @return void
     */
    public function assert_deposit_and_statement_were_stored()
    {
        $user = User::factory()->create();
        $balance = Balance::factory()->create(['user_id' => $user->id]);
        $amount = rand(1, 100);

        $this->actingAs($user)
            ->postJson(route('api.money.deposit'), ['amount' => $amount])
            ->assertStatus(200)
            ->assertJson(['message' => 'success']);

        $this->assertDatabaseHas(Deposit::class, [
            'user_id' => $user->id,
            'amount' => $balance->amount + $amount,
        ]);

        $deposit = $user->deposits()->where('amount', $balance->amount + $amount)->first();

        $this->assertDatabaseHas(Statement::class, [
            'user_id' => $user->id,
            'type' => Statement::TYPE_CREDIT,
            'details' => Statement::TYPE_CREDIT,
            'owner_id' => $deposit->id,
            'owner_type' => $deposit::class,
            'balance' => $deposit->amount,
        ]);

        $expectedAmount = $balance->amount + $amount;
        $balance->refresh();
        $this->assertEquals($expectedAmount, $balance->amount);
    }
}
