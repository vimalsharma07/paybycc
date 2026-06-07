<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GatewayReqRes extends Model
{
    protected $table = 'gateway_req_res';

    protected $fillable = [
        'transaction_id',
        'req',
        'response',
        'webhook',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'req' => 'array',
            'response' => 'array',
            'webhook' => 'array',
        ];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
