<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- DIIMPOR

class Category extends Model
{
    use HasFactory, SoftDeletes; // <-- DIGUNAKAN

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'name',
        'icon',
        'color',
    ];

    /**
     * Get the user that owns the category (if it's not a default one).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

