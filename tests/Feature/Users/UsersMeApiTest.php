<?php

namespace Tests\Feature\Users;

use App\Models\{
    Balance
};
use App\Models\{
    User
};
use Tests\Feature\BaseTestCase;

class UsersMeApiTest extends BaseTestCase
{
    /**
     * Assert correct JSON returned
     *
     * @test
     * @return void
     */
    public function assert_correct_json_returned()
    {
        $user = User::factory()->create();
        $balance = Balance::factory()->create(['user_id' => $user->id, 'amount' => 100]);

        $this->actingAs($user)
            ->getJson(route('api.users.me'))
            ->assertStatus(200)
            ->assertJsonFragment([
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'email_verified_at' => $user->email_verified_at,
                    'balance' => [
                        'id' => $balance->id,
                        'user_id' => $user->id,
                        'amount' => $balance->amount,
                        'created_at' => $balance->created_at,
                        'updated_at' => $balance->updated_at,
                    ]
                ]
            ]);
    }
}
