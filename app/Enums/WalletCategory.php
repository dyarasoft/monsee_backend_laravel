<?php

namespace App\Enums;

enum WalletCategory: string
{
    case CASH = 'cash';
    case BANK = 'bank';
    case EWALLET = 'e-wallet';
    case CREDIT_CARD = 'credit card';
    case INVESTMENT = 'investment';
    case OTHER = 'other';
    
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}