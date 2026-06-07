<?php

namespace App\Constants;

final class OrderStatuses
{
    public const ORDER_CREATED = 0;

    public const ORDER_PENDING = 1;

    public const ORDER_ACCEPTED = 2;

    public const ORDER_IN_PROGRESS = 3;

    public const ORDER_COMPLETED = 4;

    public const ORDER_CANCELLED = 5;

    public const ORDER_DISPUTED = 6;

    public const PAYMENT_PENDING = 0;

    public const PAYMENT_AUTHORIZED = 1;

    public const PAYMENT_PAID = 2;

    public const PAYMENT_FAILED = 3;

    public const PAYMENT_REFUNDED = 4;

    public const PAYMENT_PARTIALLY_REFUNDED = 5;

    public const SETTLEMENT_PENDING = 0;

    public const SETTLEMENT_ELIGIBLE = 1;

    public const SETTLEMENT_SETTLED = 2;

    public const SETTLEMENT_FAILED = 3;

    public const SAFE_PENDING_REVIEW = 0;

    public const SAFE_SAFE = 1;

    public const SAFE_HOLD = 2;

    public const SAFE_REJECTED = 3;

    /** @var array<int, string> */
    private const ORDER_LABELS = [
        self::ORDER_CREATED => 'Created',
        self::ORDER_PENDING => 'Pending',
        self::ORDER_ACCEPTED => 'Accepted',
        self::ORDER_IN_PROGRESS => 'In progress',
        self::ORDER_COMPLETED => 'Completed',
        self::ORDER_CANCELLED => 'Cancelled',
        self::ORDER_DISPUTED => 'Disputed',
    ];

    /** @var array<int, string> */
    private const PAYMENT_LABELS = [
        self::PAYMENT_PENDING => 'Pending',
        self::PAYMENT_AUTHORIZED => 'Authorized',
        self::PAYMENT_PAID => 'Paid',
        self::PAYMENT_FAILED => 'Failed',
        self::PAYMENT_REFUNDED => 'Refunded',
        self::PAYMENT_PARTIALLY_REFUNDED => 'Partially refunded',
    ];

    /** @var array<int, string> */
    private const SETTLEMENT_LABELS = [
        self::SETTLEMENT_PENDING => 'Pending',
        self::SETTLEMENT_ELIGIBLE => 'Eligible',
        self::SETTLEMENT_SETTLED => 'Settled',
        self::SETTLEMENT_FAILED => 'Failed',
    ];

    /** @var array<int, string> */
    private const SAFE_LABELS = [
        self::SAFE_PENDING_REVIEW => 'Pending review',
        self::SAFE_SAFE => 'Safe',
        self::SAFE_HOLD => 'Hold',
        self::SAFE_REJECTED => 'Rejected',
    ];

    /** @var array<string, int> */
    private const LEGACY_ORDER = [
        'created' => self::ORDER_CREATED,
        'pending' => self::ORDER_PENDING,
        'accepted' => self::ORDER_ACCEPTED,
        'in_progress' => self::ORDER_IN_PROGRESS,
        'completed' => self::ORDER_COMPLETED,
        'cancelled' => self::ORDER_CANCELLED,
        'disputed' => self::ORDER_DISPUTED,
    ];

    /** @var array<string, int> */
    private const LEGACY_PAYMENT = [
        'pending' => self::PAYMENT_PENDING,
        'authorized' => self::PAYMENT_AUTHORIZED,
        'paid' => self::PAYMENT_PAID,
        'failed' => self::PAYMENT_FAILED,
        'refunded' => self::PAYMENT_REFUNDED,
        'partially_refunded' => self::PAYMENT_PARTIALLY_REFUNDED,
    ];

    /** @var array<string, int> */
    private const LEGACY_SETTLEMENT = [
        'pending' => self::SETTLEMENT_PENDING,
        'eligible' => self::SETTLEMENT_ELIGIBLE,
        'settled' => self::SETTLEMENT_SETTLED,
        'failed' => self::SETTLEMENT_FAILED,
    ];

    /** @var array<string, int> */
    private const LEGACY_SAFE = [
        'pending_review' => self::SAFE_PENDING_REVIEW,
        'safe' => self::SAFE_SAFE,
        'hold' => self::SAFE_HOLD,
        'rejected' => self::SAFE_REJECTED,
    ];

    public static function orderLabel(int|string|null $value): string
    {
        return self::labelFrom(self::ORDER_LABELS, self::LEGACY_ORDER, $value, 'Unknown');
    }

    public static function paymentLabel(int|string|null $value): string
    {
        return self::labelFrom(self::PAYMENT_LABELS, self::LEGACY_PAYMENT, $value, 'Unknown');
    }

    public static function settlementLabel(int|string|null $value): string
    {
        return self::labelFrom(self::SETTLEMENT_LABELS, self::LEGACY_SETTLEMENT, $value, 'Unknown');
    }

    public static function safeLabel(int|string|null $value): string
    {
        return self::labelFrom(self::SAFE_LABELS, self::LEGACY_SAFE, $value, 'Unknown');
    }

    public static function paymentSlug(int|string|null $value): string
    {
        return self::slugFrom(self::LEGACY_PAYMENT, $value);
    }

    public static function statusSlug(int|string|null $value, string $type = 'order'): string
    {
        $legacy = match ($type) {
            'payment' => self::LEGACY_PAYMENT,
            'settlement' => self::LEGACY_SETTLEMENT,
            'safe' => self::LEGACY_SAFE,
            default => self::LEGACY_ORDER,
        };

        return self::slugFrom($legacy, $value);
    }

    /**
     * @param  array<int, string>  $labels
     * @param  array<string, int>  $legacy
     */
    private static function labelFrom(array $labels, array $legacy, int|string|null $value, string $fallback): string
    {
        if (is_int($value) || (is_string($value) && is_numeric($value))) {
            return $labels[(int) $value] ?? $fallback;
        }

        if (is_string($value)) {
            $key = strtolower(trim($value));

            return $labels[$legacy[$key] ?? -1] ?? \Illuminate\Support\Str::title(str_replace('_', ' ', $key));
        }

        return $fallback;
    }

    /** @param  array<string, int>  $legacy */
    private static function slugFrom(array $legacy, int|string|null $value): string
    {
        if (is_int($value) || (is_string($value) && is_numeric($value))) {
            $int = (int) $value;
            foreach ($legacy as $slug => $code) {
                if ($code === $int) {
                    return $slug;
                }
            }

            return 'unknown';
        }

        if (is_string($value)) {
            return strtolower(trim($value));
        }

        return 'unknown';
    }

    private function __construct() {}
}
