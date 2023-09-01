<?php

use App\Http\Controllers\API\{
    MoneyController,
    UserController,
    AuthController
};
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/** Auth **/
Route::prefix('/auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('api.auth.login');
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
    });
    Route::middleware('throttle:3,1')
        ->post('/signup', [AuthController::class, 'signup'])->name('api.auth.signup');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('/money')->group(function () {
        Route::post('/deposit', [MoneyController::class, 'deposit'])->name('api.money.deposit');
        Route::post('/withdraw', [MoneyController::class, 'withdraw'])->name('api.money.withdraw');
        Route::post('/transfer', [MoneyController::class, 'moneyTransfer'])->name('api.money.transfer');
        Route::get('/statements', [MoneyController::class, 'statements'])->name('api.money.statements');
    });

    Route::get('/users/me', [UserController::class, 'me'])->name('api.users.me');
});
