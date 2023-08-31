<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface StatementOwnerInterface
{
    /**
     * Returns the statement for this model
     * @return MorphMany
     */
    function statement(): MorphMany;
}
