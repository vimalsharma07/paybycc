<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentLink extends Model
{
    public const STATUS_OPEN = 'open';

    public const STATUS_PAID = 'paid';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'link_token',
        'seller_id',
        'amount',
        'currency',
        'description',
        'status',
        'expires_at',
        'order_id',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
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

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function isPayable(): bool
    {
        if (! $this->isOpen()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_OPEN => $this->expires_at && $this->expires_at->isPast() ? 'Expired' : 'Open',
            self::STATUS_PAID => 'Paid',
            self::STATUS_EXPIRED => 'Expired',
            self::STATUS_CANCELLED => 'Cancelled',
            default => ucfirst($this->status),
        };
    }
}
