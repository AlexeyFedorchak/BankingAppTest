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
Route::prefix('/auth')->name('api.auth.')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    Route::middleware('auth:sanctum')
        ->get('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('throttle:3,1')
        ->post('/signup', [AuthController::class, 'signup'])->name('signup');
});

Route::middleware('auth:sanctum')->name('api.')->group(function () {
    Route::prefix('/money')->name('money.')->group(function () {
        Route::post('/deposit', [MoneyController::class, 'deposit'])->name('deposit');
        Route::post('/withdraw', [MoneyController::class, 'withdraw'])->name('withdraw');
        Route::post('/transfer', [MoneyController::class, 'moneyTransfer'])->name('transfer');
        Route::get('/statements', [MoneyController::class, 'statements'])->name('statements');
    });

    Route::get('/users/me', [UserController::class, 'me'])->name('users.me');
});
