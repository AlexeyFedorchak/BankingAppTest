<?php

namespace App\Models;

use App\Interfaces\StatementOwnerInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Withdraw extends Model implements StatementOwnerInterface
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'amount',
        'user_id',
    ];

    /**
     * Returns the statement for this deposit
     *
     * @return MorphMany
     */
    public function statement(): MorphMany
    {
        return $this->morphMany(Statement::class, 'owner');
    }
}
