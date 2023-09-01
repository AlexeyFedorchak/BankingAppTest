<?php

namespace Tests\Feature\Money;

use App\Models\{
    Balance,
    MoneyTransfer,
    Statement,
    User
};
use Illuminate\Support\Str;
use Tests\Feature\BaseTestCase;

class MoneyTransferApiTest extends BaseTestCase
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
            ->postJson(route('api.money.transfer'), [
                'email' => User::factory()->create()->email
            ])
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
            ->postJson(route('api.money.transfer'), [
                'amount' => Str::random(),
                'email' => User::factory()->create()->email
            ])
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
            ->postJson(route('api.money.transfer'), [
                'amount' => rand(-100, 0),
                'email' => User::factory()->create()->email
            ])
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
            ->postJson(route('api.money.transfer'), [
                'amount' => rand(1000001, 1000010),
                'email' => User::factory()->create()->email
            ])
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
     * Assert that the amount is required.
     *
     * @test
     * @return void
     */
    public function assert_email_field_is_required()
    {
        $this->actingAs(User::factory()->create())
            ->postJson(route('api.money.transfer'), ['amount' => rand(1, 100)])
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('email')
            ->assertJson([
                'message' => 'The email field is required.',
                'errors' => [
                    'email' => [
                        'The email field is required.',
                    ]
                ]
            ]);
    }

    /**
     * Assert that the email field must be a valid email address
     *
     * @test
     * @return void
     */
    public function assert_email_field_must_be_valid_email_address()
    {
        $this->actingAs(User::factory()->create())
            ->postJson(route('api.money.transfer'), [
                'amount' => rand(1, 100),
                'email' => Str::random(4) . 'test.com'
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('email')
            ->assertJson([
                'message' => 'The email field must be a valid email address.',
                'errors' => [
                    'email' => [
                        'The email field must be a valid email address.',
                    ]
                ]
            ]);
    }

    /**
     * Assert that the email exists in DB.
     *
     * @test
     * @return void
     */
    public function assert_email_field_must_be_exists_in_db()
    {
        $this->actingAs(User::factory()->create())
            ->postJson(route('api.money.transfer'), [
                'amount' => rand(1, 100),
                'email' => Str::random(4) . '@test.com'
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('email')
            ->assertJson([
                'message' => 'The selected email is invalid.',
                'errors' => [
                    'email' => [
                        'The selected email is invalid.',
                    ]
                ]
            ]);
    }


    /**
     * Assert that the transfer and statement were stored
     *
     * @test
     * @return void
     */
    public function assert_transfer_and_statement_were_stored()
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        $senderBalance = Balance::factory()->create(['user_id' => $sender->id, 'amount' => 100]);
        $receiverBalance = Balance::factory()->create(['user_id' => $receiver->id, 'amount' => 100]);
        $amount = rand(1, 100);

        $this->actingAs($sender)
            ->postJson(route('api.money.transfer'), [
                'amount' => $amount,
                'email' => $receiver->email
            ])
            ->assertStatus(200)
            ->assertJson(['message' => 'success']);

        $this->assertDatabaseHas(MoneyTransfer::class, [
            'receiver_user_id' => $receiver->id,
            'sender_user_id' => $sender->id,
            'amount' => $amount,
        ]);

        $transfer = $sender->moneyTransfers()->where('amount', $amount)->first();

        $this->assertDatabaseHas(Statement::class, [
            'user_id' => $sender->id,
            'type' => Statement::TYPE_DEBIT,
            'details' => 'Transfer to ' . $receiver->email,
            'owner_id' => $transfer->id,
            'owner_type' => $transfer::class,
            'balance' => $senderBalance->amount - $amount,
        ]);

        $this->assertDatabaseHas(Statement::class, [
            'user_id' => $receiver->id,
            'type' => Statement::TYPE_CREDIT,
            'details' => 'Transfer from ' . $sender->email,
            'owner_id' => $transfer->id,
            'owner_type' => $transfer::class,
            'balance' => $receiverBalance->amount + $amount,
        ]);

        $expectedSenderAmount = $senderBalance->amount - $amount;
        $senderBalance->refresh();
        $this->assertEquals($expectedSenderAmount, $senderBalance->amount);

        $expectedReceiverAmount = $receiverBalance->amount + $amount;
        $receiverBalance->refresh();
        $this->assertEquals($expectedReceiverAmount, $receiverBalance->amount);
    }
}
