<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'base_currency',
        'rates',
    ];

    /**
     * Atribut casting untuk konversi otomatis antara format database dan PHP.
     */
    protected $casts = [
        'date' => 'date',
        'rates' => 'array',
    ];
}