<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerSubservice extends Model
{
    protected $table = 'seller_subservices';

    protected $fillable = [
        'user_id',
        'subservice_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subservice(): BelongsTo
    {
        return $this->belongsTo(Subservice::class);
    }
}
