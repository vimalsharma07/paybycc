<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    public const TYPE_CARD_PAYMENT = 'card_payment';

    public const TYPE_SETTLEMENT = 'settlement';

    protected $fillable = [
        'user_id',
        'bank_id',
        'payment_id',
        'parent_transaction_id',
        'type',
        'amount',
        'currency',
        'status',
        'settlement_trigger_at',
        'settled_at',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'settlement_trigger_at' => 'datetime',
            'settled_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function parentTransaction(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_transaction_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_CARD_PAYMENT => 'Card payment',
            self::TYPE_SETTLEMENT => 'Settlement to bank',
            default => $this->type,
        };
    }
}
