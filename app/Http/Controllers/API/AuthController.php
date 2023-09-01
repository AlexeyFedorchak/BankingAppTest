<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Auth\{
    RegistrationRequest,
    LoginRequest
};
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    /**
     * Get bearer token or fail with 401 error
     *
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request['email'])
            ->first();

        if (!$user || !auth()->attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }

        return response()->json([
            'access_token' => $user
                ->createToken('auth_token')
                ->plainTextToken,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Registration the user
     * @param RegistrationRequest $request
     * @return JsonResponse
     */
    public function signup(RegistrationRequest $request): JsonResponse
    {
        $user = User::create($request->all(['email', 'name', 'password']));
        $user->balance()->create(['amount' => 0]);

        return response()->json([
            'message' => 'success'
        ]);
    }

    /**
     * Logout user
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth()->user()
            ->tokens()
            ->delete();

        return response()->json([
            'message' => 'Tokens Revoked'
        ]);
    }
}
