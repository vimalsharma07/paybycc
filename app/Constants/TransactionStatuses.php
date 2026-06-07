<?php

namespace App\Constants;

final class TransactionStatuses
{
    public const PENDING = 0;

    public const COMPLETED = 1;

    public const FAILED = 2;

    public const PROCESSING = 3;

    /** @var array<int, string> */
    private const LABELS = [
        self::PENDING => 'Pending',
        self::COMPLETED => 'Completed',
        self::FAILED => 'Failed',
        self::PROCESSING => 'Processing',
    ];

    /** @var array<string, int> */
    private const LEGACY = [
        'pending' => self::PENDING,
        'completed' => self::COMPLETED,
        'failed' => self::FAILED,
        'processing' => self::PROCESSING,
        'success' => self::COMPLETED,
        'succeeded' => self::COMPLETED,
        'paid' => self::COMPLETED,
        'error' => self::FAILED,
        'declined' => self::FAILED,
        'rejected' => self::FAILED,
    ];

    public static function label(int|string|null $value): string
    {
        if (is_int($value) || (is_string($value) && is_numeric($value))) {
            return self::LABELS[(int) $value] ?? 'Unknown';
        }

        if (is_string($value)) {
            $key = strtolower(trim($value));

            return self::LABELS[self::LEGACY[$key] ?? -1] ?? ucfirst($key);
        }

        return 'Unknown';
    }

    public static function slug(int|string|null $value): string
    {
        /** @var array<int, string> */
        $reverse = [
            self::PENDING => 'pending',
            self::COMPLETED => 'completed',
            self::FAILED => 'failed',
            self::PROCESSING => 'processing',
        ];

        if (is_int($value) || (is_string($value) && is_numeric($value))) {
            return $reverse[(int) $value] ?? 'unknown';
        }

        if (is_string($value)) {
            $key = strtolower(trim($value));

            return $reverse[self::LEGACY[$key] ?? -1] ?? $key;
        }

        return 'unknown';
    }

    private function __construct() {}
}
