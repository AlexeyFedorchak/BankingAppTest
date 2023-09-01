<?php

namespace App\Services;

use App\Exceptions\DoesntHaveEnoughMoneyException;
use App\Interfaces\StatementOwnerInterface;
use App\Models\{
    Balance,
    Deposit,
    MoneyTransfer,
    Statement,
    User,
    Withdraw
};
use Illuminate\Support\Facades\DB;

class MoneyService
{
    /**
     * @param User $user
     * @param float $amount
     * @return void
     */
    public function deposit(User $user, float $amount): void
    {
        DB::transaction(function () use ($user, $amount) {
            $deposit = Deposit::create(['amount' => $amount, 'user_id' => $user->id]);

            $balance = $this->balanceIncreaseAmount($user->balance, $amount);
            $this->createStatement(
                $user,
                Statement::TYPE_CREDIT,
                Statement::TYPE_CREDIT,
                $deposit,
                $balance->amount
            );
        });
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
     * @param float $balanceAmount
     * @return Statement
     */
    public function createStatement(
        User $user,
        string $type,
        string $details,
        StatementOwnerInterface $owner,
        float $balanceAmount
    ): Statement {
        return Statement::create([
            'type' => $type,
            'user_id' => $user->id,
            'details' => $details,
            'owner_id' => $owner->id,
            'owner_type' => $owner::class,
            'balance' => $balanceAmount,
        ]);
    }

    /**
     * @param User $user
     * @param float $amount
     * @throws DoesntHaveEnoughMoneyException
     */
    public function withdraw(User $user, float $amount): void
    {
        DB::transaction(function () use ($user, $amount) {
            $balance = $this->balanceDecreaseAmount($user->balance, $amount);

            $withdraw = Withdraw::create(['amount' => $amount, 'user_id' => $user->id]);

            $this->createStatement(
                $user,
                Statement::TYPE_DEBIT,
                Statement::TYPE_DEBIT,
                $withdraw,
                $balance->amount
            );
        });
    }

    /**
     * Decrease balance amount
     *
     * @param Balance $balance
     * @param float $amount
     * @return Balance
     * @throws DoesntHaveEnoughMoneyException
     */
    public function balanceDecreaseAmount(Balance $balance, float $amount): Balance
    {
        if ($balance->amount <= 0) {
            throw new DoesntHaveEnoughMoneyException();
        }

        $balance->amount -= $amount;
        $balance->save();

        return $balance;
    }

    /**
     * @param User $sender
     * @param User $receiver
     * @param float $amount
     * @throws DoesntHaveEnoughMoneyException
     */
    public function moneyTransfer(User $sender, User $receiver, float $amount): void
    {
        DB::transaction(function () use ($sender, $receiver, $amount) {
            $senderBalance = $this->balanceDecreaseAmount($sender->balance, $amount);
            $receiverBalance = $this->balanceIncreaseAmount($receiver->balance, $amount);

            $moneyTransfer = MoneyTransfer::create([
                'amount' => $amount,
                'sender_user_id' => $sender->id,
                'receiver_user_id' => $receiver->id,
            ]);

            $this->createStatement(
                $sender,
                Statement::TYPE_DEBIT,
                'Transfer to ' . $receiver->email,
                $moneyTransfer,
                $senderBalance->amount
            );

            $this->createStatement(
                $receiver,
                Statement::TYPE_CREDIT,
                'Transfer from ' . $sender->email,
                $moneyTransfer,
                $receiverBalance->amount
            );
        });
    }
}
