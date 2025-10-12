<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- DIIMPOR
use Illuminate\Support\Facades\DB;

class Wallet extends Model
{
    use HasFactory, SoftDeletes; // <-- DIGUNAKAN

    protected $fillable = [
        'user_id',
        'name',
        'initial_balance',
        'icon',
        'currency'
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
     * Calculate the current balance of the wallet.
     * This is an accessor: $wallet->current_balance
     */
    public function getCurrentBalanceAttribute()
    {
        $income = $this->transactions()->where('type', 'income')->sum('amount');
        $expense = $this->transactions()->where('type', 'expense')->sum('amount');

        return ($this->initial_balance + $income) - $expense;
    }

    /**
     * Append the current_balance to the model's array form.
     */
    protected $appends = ['current_balance'];

    /**
     * Override the delete method to also delete related transactions.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($wallet) {
            // Saat wallet di-soft delete, transaksinya juga di-soft delete
            $wallet->transactions()->delete();
        });
    }
}

