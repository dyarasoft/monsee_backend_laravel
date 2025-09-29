<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- DIIMPOR

class Budget extends Model
{
    use HasFactory, SoftDeletes; // <-- DIGUNAKAN

    protected $fillable = [
        'user_id',
        'category_id',
        'amount',
        'period',
        'start_date',
    ];

    /**
     * Get the user that owns the budget.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category for the budget.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

