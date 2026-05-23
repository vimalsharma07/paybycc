<?php

namespace App\Services\Otp;

use App\Models\OtpVerify;
use App\Support\PhoneNumber;
use Illuminate\Support\Carbon;

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

    public function resendCooldownSeconds(string $purpose, string $phone): ?int
    {
        $phone = PhoneNumber::normalize($phone);
        $latest = OtpVerify::query()
            ->where('phone', $phone)
            ->where('purpose', $purpose)
            ->whereNotNull('sms_sent_at')
            ->latest('id')
            ->first();

        if ($latest === null || $latest->sms_sent_at === null) {
            return null;
        }

        $resendAfter = (int) config('otp.resend_seconds', 60);
        $elapsed = $latest->sms_sent_at->diffInSeconds(now());

        if ($elapsed >= $resendAfter) {
            return null;
        }

        return $resendAfter - $elapsed;
    }

    public function dailySendCount(string $phone): int
    {
        $phone = PhoneNumber::normalize($phone);

        return OtpVerify::query()
            ->where('phone', $phone)
            ->whereNotNull('sms_sent_at')
            ->whereDate('sms_sent_at', Carbon::today())
            ->count();
    }

    protected function secondsUntilEndOfDay(): int
    {
        return max(1, (int) now()->diffInSeconds(now()->endOfDay()));
    }
}
