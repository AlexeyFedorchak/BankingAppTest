<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Statement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'owner_type',
        'owner_id',
        'details',
        'balance',
    ];

    /**
     * Possible types
     */
    const TYPE_CREDIT = 'Credit';
    const TYPE_DEBIT = 'DEBIT';

    /**
     * Get the user who owns this statement
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
