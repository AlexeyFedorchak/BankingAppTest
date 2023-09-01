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
     * Create a MoneyController instance
     * @param MoneyService $moneyService
     */
    public function __construct(protected readonly MoneyService $moneyService) {}

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
     * @throws DoesntHaveEnoughMoneyException
     */
    public function withdraw(WithdrawRequest $request): JsonResponse
    {
        $this->moneyService->withdraw(auth()->user(), $request->amount);

        return response()->json([
            'message' => 'success'
        ]);
    }

    /**
     * Transfer money to the user with a given email
     *
     * @param MoneyTransferRequest $request
     * @return JsonResponse
     * @throws DoesntHaveEnoughMoneyException
     */
    public function moneyTransfer(MoneyTransferRequest $request): JsonResponse
    {
        $this->moneyService->moneyTransfer(
            auth()->user(),
            User::byEmail($request->email)->first(),
            $request->amount
        );

        return response()->json([
            'message' => 'success'
        ]);
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
