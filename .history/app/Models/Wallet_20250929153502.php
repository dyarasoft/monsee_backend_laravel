<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    /**
     * Atribut yang bisa diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'initial_balance',
        'icon',
        'color',
    ];

    /**
     * Menambahkan atribut custom ke JSON response.
     *
     * @var array
     */
    protected $appends = ['current_balance'];


    /**
     * Mendefinisikan relasi "milik" ke User.
     * Setiap dompet dimiliki oleh satu user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendefinisikan relasi "memiliki banyak" ke Transaction.
     * Satu dompet bisa memiliki banyak transaksi.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Accessor untuk menghitung saldo saat ini.
     * Ini akan menghitung saldo secara otomatis.
     */
    protected function currentBalance(): Attribute
    {
        return Attribute::make(
            get: function () {
                $income = $this->transactions()->where('type', 'income')->sum('amount');
                $expense = $this->transactions()->where('type', 'expense')->sum('amount');
                return $this->initial_balance + $income - $expense;
            }
        );
    }
}

