<?php

namespace App\Support;

final class PhoneNumber
{
    public static function normalize(string $value): string
    {
        return preg_replace('/\D/', '', $value) ?? '';
    }

    public static function isValidIndianMobile(string $value): bool
    {
        return (bool) preg_match('/^[6-9]\d{9}$/', self::normalize($value));
    }
}
