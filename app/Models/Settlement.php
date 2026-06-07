<?php

namespace App\Models;

use App\Constants\OrderStatuses;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Settlement extends Model
{
    protected $table = 'settlements';

    protected $fillable = [
        'order_id',
        'freelancer_id',
        'bank_id',
        'amount',
        'status',
        'reference',
        'settled_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'settled_at' => 'datetime',
            'status' => 'integer',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return OrderStatuses::settlementLabel($this->status);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function freelancer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }
}
