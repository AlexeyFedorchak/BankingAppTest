<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Tests\Feature\BaseTestCase;

class AuthSignupApiTest extends BaseTestCase
{
    /**
     * Validation enabled, so correct data needs to be passed
     *
     * @test
     * @return void
     */
    public function assert_validation_enabled()
    {
        $this->postJson(route('api.auth.signup'))
            ->assertStatus(422);

        $password = Str::random(8);
        $user = User::factory()->create(['password' => $password]);

        $this->postJson(route('api.auth.signup'), ['email' => $user->email])
            ->assertStatus(422);

        $this->postJson(route('api.auth.signup'), ['password' => $password])
            ->assertStatus(422);
    }

    /**
     * Assert that you can register only with a unique email
     *
     * @test
     * @return void
     */
    public function assert_only_with_unique_email_register()
    {
        $email = User::factory()->create()->email;
        $user = User::factory()->make();

        $this->postJson(route('api.auth.signup'), [
            'email' => $email,
            'password' => $user->password,
            'name' => $user->name,
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('email');
    }

    /**
     * Response should be {'access_token' => '...', 'token_type' => Bearer}
     *
     * @test
     * @return void
     */
    public function assert_correct_json_structure_returned()
    {
        $user = User::factory()->make();

        $this->postJson(route('api.auth.signup'), [
            'email' => $user->email,
            'password' => $user->password,
            'name' => $user->name,
        ])
            ->assertStatus(200)
            ->assertJson(['message' => 'success']);

        $this->assertDatabaseHas(User::class, [
            'email' => $user->email,
            'name' => $user->name,
        ]);
    }
}
