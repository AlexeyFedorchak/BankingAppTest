<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Money\DepositRequest;
use App\Services\MoneyService;
use Illuminate\Http\JsonResponse;

class MoneyController extends Controller
{
    /**
     * @var MoneyService
     */
    public MoneyService $moneyService;

    /**
     * Create a MoneyController instance
     * @param MoneyService $moneyService
     */
    public function __construct(MoneyService $moneyService)
    {
        $this->moneyService = $moneyService;
    }

    /**
     * @param DepositRequest $request
     * @return JsonResponse
     */
    public function deposit(DepositRequest $request): JsonResponse
    {
        $this->moneyService->deposit(auth()->user(), $request->amount);

        return response()->json([
            'message' => 'success'
        ]);
    }
}
