<?php

namespace App\Models;

use App\Constants\TransactionStatuses;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    public const TYPE_CARD_PAYMENT = 'card_payment';

    public const TYPE_SETTLEMENT = 'settlement';

    public const STATUS_PENDING = TransactionStatuses::PENDING;

    public const STATUS_COMPLETED = TransactionStatuses::COMPLETED;

    public const STATUS_FAILED = TransactionStatuses::FAILED;

    public const STATUS_PROCESSING = TransactionStatuses::PROCESSING;

    protected $fillable = [
        'user_id',
        'bank_id',
        'payment_id',
        'transaction_id',
        'gateway_id',
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
            'status' => 'integer',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return TransactionStatuses::label($this->status);
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

    public static function generateTransactionRef(int $id, int $userId): string
    {
        return substr('pay'.$id.$userId.time(), 0, 8);
    }
}
