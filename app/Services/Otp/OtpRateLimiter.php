<?php

namespace App\Services\Otp;

use App\Support\PhoneNumber;
use Illuminate\Support\Facades\Cache;

/**
 * OTP send rate limits (applied inside OtpService for every purpose).
 */
class OtpRateLimiter
{
    public function dailyLimit(): int
    {
        return max(0, (int) config('otp.daily_sms_limit', 10));
    }

    /**
     * @return array{allowed: bool, message?: string, retry_after?: int, remaining?: int}
     */
    public function checkDailySmsLimit(string $phone): array
    {
        $phone = PhoneNumber::normalize($phone);
        $limit = $this->dailyLimit();

        if ($limit === 0) {
            return ['allowed' => true, 'remaining' => PHP_INT_MAX];
        }

        $count = $this->dailySendCount($phone);

        if ($count >= $limit) {
            return [
                'allowed' => false,
                'message' => 'Daily verification limit reached for this number ('.$limit.' per day). Please try again tomorrow.',
                'retry_after' => $this->secondsUntilEndOfDay(),
            ];
        }

        return [
            'allowed' => true,
            'remaining' => $limit - $count,
        ];
    }

    public function recordDailySmsSend(string $phone): void
    {
        if ($this->dailyLimit() === 0) {
            return;
        }

        $phone = PhoneNumber::normalize($phone);
        $key = $this->dailyCountCacheKey($phone);
        $count = $this->dailySendCount($phone);

        Cache::put($key, $count + 1, now()->endOfDay());
    }

    public function resendCooldownSeconds(string $purpose, string $phone): ?int
    {
        $phone = PhoneNumber::normalize($phone);
        $cacheKey = 'otp:'.$purpose.':sms:'.$phone;
        $existing = Cache::get($cacheKey);

        if (! is_array($existing) || ! isset($existing['sent_at'])) {
            return null;
        }

        $resendAfter = (int) config('otp.resend_seconds', 60);
        $elapsed = time() - (int) $existing['sent_at'];

        if ($elapsed >= $resendAfter) {
            return null;
        }

        return $resendAfter - $elapsed;
    }

    public function dailySendCount(string $phone): int
    {
        return (int) Cache::get($this->dailyCountCacheKey(PhoneNumber::normalize($phone)), 0);
    }

    protected function dailyCountCacheKey(string $phone): string
    {
        return 'otp:sms_daily:'.$phone.':'.now()->format('Y-m-d');
    }

    protected function secondsUntilEndOfDay(): int
    {
        return max(1, (int) now()->diffInSeconds(now()->endOfDay()));
    }
}
