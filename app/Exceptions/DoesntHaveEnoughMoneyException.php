<?php

namespace App\Exceptions;

use Exception;

class DoesntHaveEnoughMoneyException extends Exception
{
    public function render()
    {
        return response()->json([
            'message' => 'Transaction failed. Not enough money.'
        ], 422);
    }
}
