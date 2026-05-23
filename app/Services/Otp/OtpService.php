<?php

namespace App\Services\Otp;

use App\Contracts\SmsSender;
use App\Enums\OtpPurpose;
use App\Support\PhoneNumber;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use InvalidArgumentException;

class OtpService
{
    public function __construct(
        protected SmsSender $sms,
        protected OtpRateLimiter $rateLimiter,
    ) {}

    public function sendSms(string $phone, OtpPurpose|string $purpose): OtpResult
    {
        $phone = PhoneNumber::normalize($phone);
        $purposeKey = $this->purposeKey($purpose);

        if (! PhoneNumber::isValidIndianMobile($phone)) {
            return OtpResult::fail('Enter a valid 10-digit Indian mobile number.');
        }

        $dailyCheck = $this->rateLimiter->checkDailySmsLimit($phone);
        if (! $dailyCheck['allowed']) {
            return OtpResult::fail(
                (string) ($dailyCheck['message'] ?? 'Daily limit reached.'),
                $dailyCheck['retry_after'] ?? null
            );
        }

        $cooldown = $this->rateLimiter->resendCooldownSeconds($purposeKey, $phone);
        if ($cooldown !== null) {
            return OtpResult::fail(
                'Please wait before requesting another code.',
                $cooldown
            );
        }

        $otp = $this->generateCode();
        $message = $this->buildMessage($purposeKey, $otp);

        if (! $this->deliverSms($phone, $message, $otp, $purposeKey)) {
            return OtpResult::fail('We could not send the OTP. Please try again in a moment.');
        }

        $this->rateLimiter->recordDailySmsSend($phone);

        $cacheKey = $this->cacheKey($purposeKey, $phone);
        Cache::put($cacheKey, [
            'hash' => password_hash($otp, PASSWORD_BCRYPT),
            'attempts' => 0,
            'sent_at' => time(),
        ], (int) config('otp.ttl_seconds', 600));

        $this->clearVerification($purpose);

        return OtpResult::ok('Verification code sent to +91 '.$phone.'.');
    }

    public function verifySms(string $phone, OtpPurpose|string $purpose, string $code): OtpResult
    {
        $phone = PhoneNumber::normalize($phone);
        $purposeKey = $this->purposeKey($purpose);
        $code = preg_replace('/\D/', '', $code) ?? '';
        $length = (int) config('otp.length', 6);

        if (! PhoneNumber::isValidIndianMobile($phone)) {
            return OtpResult::fail('Enter a valid mobile number.');
        }

        if (strlen($code) !== $length) {
            return OtpResult::fail('Enter the '.$length.'-digit code from your SMS.');
        }

        $cacheKey = $this->cacheKey($purposeKey, $phone);
        $record = Cache::get($cacheKey);

        if (! is_array($record) || ! isset($record['hash'])) {
            return OtpResult::fail('This code has expired. Request a new one.');
        }

        $maxAttempts = (int) config('otp.max_verify_attempts', 5);
        $attempts = (int) ($record['attempts'] ?? 0);

        if ($attempts >= $maxAttempts) {
            Cache::forget($cacheKey);

            return OtpResult::fail('Too many incorrect attempts. Request a new code.');
        }

        if (! password_verify($code, (string) $record['hash'])) {
            $record['attempts'] = $attempts + 1;
            Cache::put($cacheKey, $record, (int) config('otp.ttl_seconds', 600));

            $remaining = $maxAttempts - $record['attempts'];

            return OtpResult::fail(
                $remaining > 0
                    ? 'Incorrect code. '.$remaining.' attempt'.($remaining === 1 ? '' : 's').' left.'
                    : 'Too many incorrect attempts. Request a new code.'
            );
        }

        Cache::forget($cacheKey);
        Session::put($this->sessionKey($purposeKey), $phone);

        return OtpResult::ok('Mobile number verified.');
    }

    public function isVerified(string $recipient, OtpPurpose|string $purpose): bool
    {
        $recipient = PhoneNumber::normalize($recipient);
        $verified = Session::get($this->sessionKey($this->purposeKey($purpose)));

        return is_string($verified) && $verified === $recipient;
    }

    public function clearVerification(OtpPurpose|string $purpose): void
    {
        Session::forget($this->sessionKey($this->purposeKey($purpose)));
    }

    protected function purposeKey(OtpPurpose|string $purpose): string
    {
        $key = $purpose instanceof OtpPurpose ? $purpose->value : $purpose;

        if (! is_array(config('otp.purposes.'.$key))) {
            throw new InvalidArgumentException("Unknown OTP purpose [{$key}].");
        }

        return $key;
    }

    protected function cacheKey(string $purpose, string $phone): string
    {
        return 'otp:'.$purpose.':sms:'.$phone;
    }

    protected function sessionKey(string $purpose): string
    {
        return 'otp.verified.'.$purpose;
    }

    protected function generateCode(): string
    {
        $length = max(4, (int) config('otp.length', 6));
        $max = (10 ** $length) - 1;

        return str_pad((string) random_int(0, $max), $length, '0', STR_PAD_LEFT);
    }

    protected function buildMessage(string $purpose, string $otp): string
    {
        $template = (string) config('otp.purposes.'.$purpose.'.message');

        return str_replace(
            ['{otp}', '{app}'],
            [$otp, (string) config('app.name')],
            $template
        );
    }

    protected function deliverSms(string $phone, string $message, string $otp, string $purpose): bool
    {
        if (config('sms.driver') === 'log') {
            Log::info('OTP (log driver)', [
                'purpose' => $purpose,
                'phone' => $phone,
                'otp' => $otp,
                'message' => $message,
            ]);

            return true;
        }

        $result = $this->sms->send($phone, $message);

        return ($result['ok'] ?? false) === true;
    }
}
