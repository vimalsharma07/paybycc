<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class WebsiteSetting extends Model
{
    public const CACHE_KEY = 'website_settings.singleton';

    protected $fillable = [
        'site_name',
        'tagline',
        'email',
        'support_email',
        'phone',
        'address',
        'instagram_url',
        'linkedin_url',
        'facebook_url',
        'twitter_url',
        'logo_path',
        'status',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }

    /**
     * Cached first row (singleton). Safe default object if table empty.
     */
    public static function cached(): self
    {
        return Cache::remember(self::CACHE_KEY, 3600, function () {
            $row = static::query()->first();

            return $row ?? new static([
                'site_name' => config('app.name', 'App'),
                'tagline' => null,
                'email' => null,
                'support_email' => null,
                'phone' => null,
                'address' => null,
                'instagram_url' => null,
                'linkedin_url' => null,
                'facebook_url' => null,
                'twitter_url' => null,
                'logo_path' => null,
                'status' => 'active',
            ]);
        });
    }

    public static function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public function displayName(): string
    {
        $name = trim((string) $this->site_name);

        return $name !== '' ? $name : (string) config('app.name', 'App');
    }

    /**
     * Full URL for <img src>. logo_path may be absolute URL or path relative to public (uploads/...).
     */
    public function logoUrl(): ?string
    {
        $p = trim((string) $this->logo_path);
        if ($p === '') {
            return null;
        }
        if (str_starts_with($p, 'http://') || str_starts_with($p, 'https://')) {
            return $p;
        }

        return asset(ltrim($p, '/'));
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
