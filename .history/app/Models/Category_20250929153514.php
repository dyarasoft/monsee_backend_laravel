<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * Atribut yang bisa diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id', // null jika kategori default
        'name',
        'icon',
        'color',
        'is_default',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Mendefinisikan relasi "milik" ke User.
     * Kategori custom dimiliki oleh satu user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendefinisikan relasi "memiliki banyak" ke Transaction.
     * Satu kategori bisa memiliki banyak transaksi.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Mendefinisikan relasi "memiliki banyak" ke Budget.
     * Satu kategori bisa memiliki banyak budget (misal budget bulanan).
     */
    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }
}

