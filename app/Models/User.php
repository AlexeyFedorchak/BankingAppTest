<?php

namespace App\Models;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the balance this user owns
     *
     * @return hasOne
     */
    public function balance(): hasOne
    {
        return $this->hasOne(Balance::class);
    }

    /**
     * Get the deposits this user owns
     *
     * @return hasMany
     */
    public function deposits(): hasMany
    {
        return $this->hasMany(Deposit::class);
    }

    /**
     * Get the withdraws this user owns
     *
     * @return hasMany
     */
    public function withdraws(): hasMany
    {
        return $this->hasMany(Withdraw::class);
    }

    /**
     * Get the moneyTransfers this user owns
     *
     * @return hasMany
     */
    public function moneyTransfers(): hasMany
    {
        return $this->hasMany(MoneyTransfer::class, 'sender_user_id');
    }

    /**
     * Scope a query to filter by a given email
     * @param Builder $builder
     * @param string $email
     * @return Builder
     */
    public function scopeByEmail(Builder $builder, string $email): Builder
    {
        return $builder->where('email', $email);
    }

    /**
     * Get the deposits this user owns
     *
     * @return hasMany
     */
    public function statements(): hasMany
    {
        return $this->hasMany(Statement::class);
    }
}
