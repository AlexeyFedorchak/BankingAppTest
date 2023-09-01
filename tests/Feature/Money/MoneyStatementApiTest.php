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

class MoneyStatementApiTest extends BaseTestCase
{
    /**
     * Assert that the page field must be an integer.
     *
     * @test
     * @return void
     */
    public function assert_page_field_must_be_integer()
    {
        $this->actingAs(User::factory()->create())
            ->getJson(route('api.money.statements', [
                'page' => Str::random()
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('page')
            ->assertJson([
                'message' => 'The page field must be an integer.',
                'errors' => [
                    'page' => [
                        'The page field must be an integer.',
                    ]
                ]
            ]);
    }

    /**
     * Assert that the page field must be at least 1.
     *
     * @test
     * @return void
     */
    public function assert_page_field_must_be_at_least_1()
    {
        $this->actingAs(User::factory()->create())
            ->getJson(route('api.money.statements', [
                'page' => rand(-100, 0)
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('page')
            ->assertJson([
                'message' => 'The page field must be at least 1.',
                'errors' => [
                    'page' => [
                        'The page field must be at least 1.',
                    ]
                ]
            ]);
    }

    /**
     * Assert that the per_page field must be an integer.
     *
     * @test
     * @return void
     */
    public function assert_per_page_field_must_be_integer()
    {
        $this->actingAs(User::factory()->create())
            ->getJson(route('api.money.statements', [
                'per_page' => Str::random()
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('per_page')
            ->assertJson([
                'message' => 'The per page field must be an integer.',
                'errors' => [
                    'per_page' => [
                        'The per page field must be an integer.',
                    ]
                ]
            ]);
    }

    /**
     * Assert that the per_page field must be at least 1.
     *
     * @test
     * @return void
     */
    public function assert_per_page_field_must_be_at_least_1()
    {
        $this->actingAs(User::factory()->create())
            ->getJson(route('api.money.statements', [
                'per_page' => rand(-100, 0)
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('per_page')
            ->assertJson([
                'message' => 'The per page field must be at least 1.',
                'errors' => [
                    'per_page' => [
                        'The per page field must be at least 1.',
                    ]
                ]
            ]);
    }

    /**
     * Assert correct JSON fragment returned
     *
     * @test
     * @return void
     */
    public function assert_correct_json_fragment_returned()
    {
        $user = User::factory()->create();
        Balance::factory()->create(['user_id' => $user->id, 'amount' => 100]);
        $withdraw = Withdraw::factory()->create(['user_id' => $user->id, 'amount' => rand(0, 1)]);

        $statement = Statement::factory()->create([
            'user_id' => $user->id,
            'owner_id' => $withdraw->id,
            'owner_type' => Withdraw::class,
            'type' => Statement::TYPE_DEBIT,
            'details' => Statement::TYPE_DEBIT
        ]);

        $this->actingAs($user)
            ->getJson(route('api.money.statements'))
            ->assertStatus(200)
            ->assertJsonFragment([
                'data' => [
                    [
                        'id' => $statement->id,
                        'user_id' => $user->id,
                        'type' => Statement::TYPE_DEBIT,
                        'details' => Statement::TYPE_DEBIT,
                        'balance' => $statement->balance,
                        'created_at' => $statement->created_at,
                        'updated_at' => $statement->updated_at,
                        'owner' => [
                            'id' => $withdraw->id,
                            'user_id' => $user->id,
                            'amount' => $withdraw->amount,
                            'created_at' => $withdraw->created_at,
                            'updated_at' => $withdraw->updated_at,
                        ]
                    ]
                ]
            ]);
    }
}
