<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentLink extends Model
{
    public const STATUS_OPEN = 'open';

    public const STATUS_PAID = 'paid';

    public const STATUS_EXHAUSTED = 'exhausted';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'link_token',
        'seller_id',
        'amount',
        'currency',
        'description',
        'max_uses',
        'uses_count',
        'status',
        'expires_at',
        'order_id',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'max_uses' => 'integer',
            'uses_count' => 'integer',
            'expires_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function payments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function publicUrl(): string
    {
        return route('payment-links.pay.show', $this->link_token, true);
    }

    public function isOpenAmount(): bool
    {
        return $this->amount === null;
    }

    public function hasUsesRemaining(): bool
    {
        return $this->max_uses === null || $this->uses_count < $this->max_uses;
    }

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function isPayable(): bool
    {
        if (! $this->isOpen()) {
            return false;
        }

        if (! $this->hasUsesRemaining()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    public function usageLimitLabel(): string
    {
        if ($this->max_uses === null) {
            return 'Unlimited';
        }

        return $this->max_uses === 1
            ? 'One-time use'
            : $this->max_uses.' uses';
    }

    public function amountLabel(): string
    {
        return $this->isOpenAmount()
            ? 'Client chooses amount'
            : '₹'.number_format((float) $this->amount, 2);
    }

    public function statusLabel(): string
    {
        if ($this->status === self::STATUS_OPEN) {
            if ($this->expires_at && $this->expires_at->isPast()) {
                return 'Expired';
            }

            if (! $this->hasUsesRemaining()) {
                return 'Used up';
            }

            if ($this->max_uses !== null && $this->uses_count > 0) {
                return 'Open · '.$this->uses_count.'/'.$this->max_uses.' used';
            }

            return 'Open';
        }

        return match ($this->status) {
            self::STATUS_PAID => 'Paid',
            self::STATUS_EXHAUSTED => 'Used up',
            self::STATUS_EXPIRED => 'Expired',
            self::STATUS_CANCELLED => 'Cancelled',
            default => ucfirst($this->status),
        };
    }
}
