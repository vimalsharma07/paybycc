<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'gateway_id',
        'amount',
        'currency',
        'status',
        'gateway_reference',
        'driver_payload',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'driver_payload' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function gateway(): BelongsTo
    {
        return $this->belongsTo(Gateway::class);
    }
}
