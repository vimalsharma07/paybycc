<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gateway extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'filename',
        'credentials',
        'status',
        'is_primary',
        'min_txn',
        'max_txn',
        'daily_limit',
    ];

    protected function casts(): array
    {
        return [
            'credentials' => 'encrypted:array',
            'is_primary' => 'boolean',
            'min_txn' => 'decimal:2',
            'max_txn' => 'decimal:2',
            'daily_limit' => 'decimal:2',
        ];
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function scopeActivePrimary($query)
    {
        return $query->where('status', 'active')->where('is_primary', true);
    }

    public static function normalizeFilename(string $filename): string
    {
        $trimmed = trim($filename);
        if ($trimmed === '') {
            return '';
        }

        return preg_replace('/\.php$/i', '', $trimmed) ?: $trimmed;
    }
}
