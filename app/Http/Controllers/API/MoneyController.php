<?php

namespace App\Http\Controllers\API;

use App\Exceptions\DoesntHaveEnoughMoneyException;
use App\Http\Controllers\Controller;
use App\Http\Resources\StatementResource;
use App\Http\Requests\API\Money\{
    DepositRequest,
    MoneyTransferRequest,
    StatementRequest,
    WithdrawRequest
};
use App\Models\User;
use App\Services\MoneyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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

    /**
     * @param WithdrawRequest $request
     * @return JsonResponse
     */
    public function withdraw(WithdrawRequest $request): JsonResponse
    {
        try {
            $this->moneyService->withdraw(auth()->user(), $request->amount);
        } catch (DoesntHaveEnoughMoneyException) {
            return $this->notEnoughMoneyResponse();
        }

        return response()->json([
            'message' => 'success'
        ]);
    }

    /**
     * Transfer money to the user with a given email
     * @param MoneyTransferRequest $request
     * @return JsonResponse
     */
    public function moneyTransfer(MoneyTransferRequest $request): JsonResponse
    {
        try {
            $this->moneyService->moneyTransfer(auth()->user(), User::byEmail($request->email)->first(), $request->amount);
        } catch (DoesntHaveEnoughMoneyException) {
            return $this->notEnoughMoneyResponse();
        }

        return response()->json([
            'message' => 'success'
        ]);
    }

    /**
     * @param string|null $message
     * @return JsonResponse
     */
    private function notEnoughMoneyResponse(?string $message = null): JsonResponse
    {
        return response()->json([
            'message' => $message ?? 'Your doesn\'t have enough money'
        ], 422);
    }

    /**
     * Retrieve all statements
     *
     * @param StatementRequest $request
     * @return AnonymousResourceCollection
     */
    public function statements(StatementRequest $request): AnonymousResourceCollection
    {
        return StatementResource::collection(
            auth()->user()->statements()->with('owner')->paginate(
                $request->get('per_page', 15),
                page: $request->get('page', 1),
            )
        );
    }
}
