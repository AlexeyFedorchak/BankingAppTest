<?php

namespace App\Services;

use App\Interfaces\StatementOwnerInterface;
use App\Models\{
    Balance,
    Deposit,
    Statement,
    User
};

class MoneyService
{
    /**
     * @param User $user
     * @param float $amount
     * @return void
     */
    public function deposit(User $user, float $amount): void
    {
        $deposit = Deposit::create(['amount' => $amount, 'user_id' => $user->id]);

        $this->balanceIncreaseAmount($user->balance, $amount);
        $this->createStatement($user, Statement::TYPE_CREDIT, Statement::TYPE_CREDIT, $deposit);
    }

    /**
     * Increase balance amount
     *
     * @param Balance $balance
     * @param float $amount
     * @return Balance
     */
    public function balanceIncreaseAmount(Balance $balance, float $amount): Balance
    {
        $balance->amount += $amount;
        $balance->save();

        return $balance;
    }

    /**
     * Create a statement
     *
     * @param User $user
     * @param string $type
     * @param string $details
     * @param StatementOwnerInterface $owner
     * @return Statement
     */
    public function createStatement(User $user, string $type, string $details, StatementOwnerInterface $owner): Statement
    {
        return Statement::create([
            'type' => $type,
            'user_id' => $user->id,
            'details' => $details,
            'owner_id' => $owner->id,
            'owner_type' => $owner::class,
            'balance' => $owner->amount,
        ]);
    }
}
