<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OtpVerify extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_VERIFIED = 'verified';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_FAILED = 'failed';

    protected $table = 'otp_verify';

    protected $fillable = [
        'user_id',
        'phone',
        'purpose',
        'otp_hash',
        'status',
        'attempts',
        'ip_address',
        'expires_at',
        'verified_at',
        'sms_sent_at',
        'consumed_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'verified_at' => 'datetime',
            'sms_sent_at' => 'datetime',
            'consumed_at' => 'datetime',
            'attempts' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function scopeActivePending($query)
    {
        return $query
            ->where('status', self::STATUS_PENDING)
            ->where('expires_at', '>', now());
    }
}
