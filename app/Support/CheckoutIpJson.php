<?php

namespace App\Support;

use Illuminate\Http\Request;

final class CheckoutIpJson
{
    /**
     * @return array<string, mixed>
     */
    public static function fromRequest(?Request $request = null): array
    {
        $request ??= request();
        if (! $request instanceof Request) {
            return self::emptyPayload();
        }

        $client = $request->input('checkout_meta');
        if (is_string($client) && $client !== '') {
            $decoded = json_decode($client, true);
            $client = is_array($decoded) ? $decoded : [];
        } elseif (! is_array($client)) {
            $client = [];
        }

        return [
            'ip' => self::stringOrNull($request->ip()),
            'userAgent' => self::stringOrNull($request->userAgent()),
            'os' => self::stringOrNull($client['os'] ?? null),
            'browser' => self::stringOrNull($client['browser'] ?? null),
            'deviceType' => self::stringOrNull($client['deviceType'] ?? null),
            'screenWidth' => self::intOrNull($client['screenWidth'] ?? null),
            'screenHeight' => self::intOrNull($client['screenHeight'] ?? null),
            'timezone' => self::stringOrNull($client['timezone'] ?? null),
            'language' => self::stringOrNull($client['language'] ?? null),
        ];
    }

    /**
     * @return array<string, null>
     */
    private static function emptyPayload(): array
    {
        return [
            'ip' => null,
            'userAgent' => null,
            'os' => null,
            'browser' => null,
            'deviceType' => null,
            'screenWidth' => null,
            'screenHeight' => null,
            'timezone' => null,
            'language' => null,
        ];
    }

    private static function stringOrNull(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value !== '' ? $value : null;
    }

    private static function intOrNull(mixed $value): ?int
    {
        if (! is_numeric($value)) {
            return null;
        }

        $int = (int) $value;

        return $int > 0 ? $int : null;
    }
}
