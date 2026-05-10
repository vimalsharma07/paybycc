<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'balance',
        'auto_settle_to_bank',
        'default_bank_id',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'auto_settle_to_bank' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function defaultBank(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'default_bank_id');
    }
}
