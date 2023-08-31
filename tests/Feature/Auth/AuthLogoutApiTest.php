<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Tests\Feature\BaseTestCase;

class AuthLogoutApiTest extends BaseTestCase
{
    /**
     * Only authorized user can log out
     *
     * @test
     * @return void
     */
    public function assert_only_user_can_logout()
    {
        $this->getJson(route('api.auth.logout'))
            ->assertStatus(401);
    }

    /**
     * Response should be {'message' => '...'}
     *
     * @test
     * @return void
     */
    public function assert_correct_json_structure_returned()
    {

        $this->actingAs($user = User::factory()->create())
            ->getJson(route('api.auth.logout'))
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
            ]);
    }
}
