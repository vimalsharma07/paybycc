<?php

namespace App\Models;

use App\Constants\OrderStatuses;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'order_code',
        'customer_id',
        'freelancer_id',
        'service_id',
        'subservice_id',
        'order_amount',
        'platform_fee',
        'gst_amount',
        'tds_amount',
        'tcs_amount',
        'net_settlement_amount',
        'currency',
        'order_status',
        'payment_status',
        'settlement_status',
        'safe_status',
        'completed_at',
        'notes',
        'ip_json',
    ];

    protected function casts(): array
    {
        return [
            'order_amount' => 'decimal:2',
            'platform_fee' => 'decimal:2',
            'gst_amount' => 'decimal:2',
            'tds_amount' => 'decimal:2',
            'tcs_amount' => 'decimal:2',
            'net_settlement_amount' => 'decimal:2',
            'order_status' => 'integer',
            'payment_status' => 'integer',
            'settlement_status' => 'integer',
            'safe_status' => 'integer',
            'completed_at' => 'datetime',
            'ip_json' => 'array',
        ];
    }

    public function getOrderStatusLabelAttribute(): string
    {
        return OrderStatuses::orderLabel($this->order_status);
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return OrderStatuses::paymentLabel($this->payment_status);
    }

    public function getSettlementStatusLabelAttribute(): string
    {
        return OrderStatuses::settlementLabel($this->settlement_status);
    }

    public function getSafeStatusLabelAttribute(): string
    {
        return OrderStatuses::safeLabel($this->safe_status);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function freelancer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function subservice(): BelongsTo
    {
        return $this->belongsTo(Subservice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->ofMany('id', 'max');
    }

    public function settlements(): HasMany
    {
        return $this->hasMany(Settlement::class);
    }
}
