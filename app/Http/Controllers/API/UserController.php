<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    /**
     * Return user instance with balance
     * @return UserResource
     */
    public function me(): UserResource
    {
        return UserResource::make(auth()->user()->with('balance')->get());
    }
}
