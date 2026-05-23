<?php

namespace App\Services\Sms;

use App\Contracts\SmsSender;
use App\Services\Logging\AppLogger;

class LogSmsSender implements SmsSender
{
    public function __construct(
        protected AppLogger $appLog,
    ) {}

    /**
     * @return array{ok: bool, error?: string}
     */
    public function sendOtp(string $mobile, string $otp): array
    {
        $this->appLog->info('sms', 'sms.log_driver.sent', 'SMS log driver (no real send)', [
            'mobile' => $mobile,
            'otp' => $otp,
        ]);

        return ['ok' => true];
    }
}
