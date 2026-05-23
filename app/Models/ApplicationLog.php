<?php

namespace App\Models;

use App\Enums\LogLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ApplicationLog extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'logs';

    protected $fillable = [
        'level',
        'channel',
        'event',
        'message',
        'context',
        'user_id',
        'subject_type',
        'subject_id',
        'ip_address',
        'request_id',
    ];

    protected function casts(): array
    {
        return [
            'context' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function levelEnum(): LogLevel
    {
        return LogLevel::tryFrom($this->level) ?? LogLevel::Info;
    }

    public function subjectLabel(): ?string
    {
        if ($this->subject_type === null) {
            return null;
        }

        $base = class_basename($this->subject_type);

        return $this->subject_id !== null
            ? $base.' #'.$this->subject_id
            : $base;
    }
}
