<?php

namespace App\Models;
use App\Enums\WalletCategory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wallet extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'category',
        'initial_balance',
        'currency',
        'icon',
    ];

    protected $casts = [
        'category' => WalletCategory::class,
        'initial_balance' => 'float',
    ];

    /**
     * Get the user that owns the wallet.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the transactions for the wallet.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Override the delete method to also delete related transactions.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($wallet) {
            $wallet->transactions()->delete();
        });
    }
}