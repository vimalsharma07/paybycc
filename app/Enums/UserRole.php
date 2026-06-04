<?php

namespace App\Enums;

enum UserRole: string
{
    case Customer = 'customer';
    case Seller = 'seller';

    public function label(): string
    {
        return match ($this) {
            self::Customer => 'Customer / Buyer',
            self::Seller => 'Seller / Freelancer',
        };
    }
}
