<?php

namespace App\Services\Otp;

use App\Contracts\SmsSender;
use App\Enums\OtpPurpose;
use App\Models\OtpVerify;
use App\Support\PhoneNumber;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class OtpService
{
    public function __construct(
        protected SmsSender $sms,
        protected OtpRateLimiter $rateLimiter,
    ) {}

    public function sendSms(string $phone, OtpPurpose|string $purpose, ?string $ipAddress = null): OtpResult
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

        $this->expirePending($phone, $purposeKey);

        $record = OtpVerify::create([
            'user_id' => null,
            'phone' => $phone,
            'purpose' => $purposeKey,
            'otp_hash' => password_hash($otp, PASSWORD_BCRYPT),
            'status' => OtpVerify::STATUS_PENDING,
            'attempts' => 0,
            'ip_address' => $ipAddress,
            'expires_at' => now()->addSeconds((int) config('otp.ttl_seconds', 600)),
        ]);

        if (! $this->deliverSms($phone, $message, $otp, $purposeKey)) {
            $record->update(['status' => OtpVerify::STATUS_FAILED]);

            return OtpResult::fail('We could not send the OTP. Please try again in a moment.');
        }

        $record->update(['sms_sent_at' => now()]);

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

        $record = $this->activePendingRecord($phone, $purposeKey);

        if ($record === null) {
            return OtpResult::fail('This code has expired. Request a new one.');
        }

        if ($record->isExpired()) {
            $record->update(['status' => OtpVerify::STATUS_EXPIRED]);

            return OtpResult::fail('This code has expired. Request a new one.');
        }

        $maxAttempts = (int) config('otp.max_verify_attempts', 5);

        if ($record->attempts >= $maxAttempts) {
            $record->update(['status' => OtpVerify::STATUS_FAILED]);

            return OtpResult::fail('Too many incorrect attempts. Request a new code.');
        }

        if (! password_verify($code, $record->otp_hash)) {
            $record->increment('attempts');
            $remaining = $maxAttempts - $record->attempts;

            if ($record->attempts >= $maxAttempts) {
                $record->update(['status' => OtpVerify::STATUS_FAILED]);
            }

            return OtpResult::fail(
                $remaining > 0
                    ? 'Incorrect code. '.$remaining.' attempt'.($remaining === 1 ? '' : 's').' left.'
                    : 'Too many incorrect attempts. Request a new code.'
            );
        }

        $record->update([
            'status' => OtpVerify::STATUS_VERIFIED,
            'verified_at' => now(),
        ]);

        return OtpResult::ok('Mobile number verified.');
    }

    public function isVerified(string $recipient, OtpPurpose|string $purpose): bool
    {
        return $this->activeVerifiedRecord(
            PhoneNumber::normalize($recipient),
            $this->purposeKey($purpose)
        ) !== null;
    }

    public function consumeVerification(string $phone, OtpPurpose|string $purpose, int $userId): void
    {
        $phone = PhoneNumber::normalize($phone);
        $purposeKey = $this->purposeKey($purpose);

        $record = $this->activeVerifiedRecord($phone, $purposeKey);

        if ($record === null) {
            return;
        }

        $record->update([
            'user_id' => $userId,
            'consumed_at' => now(),
        ]);
    }

    protected function activeVerifiedRecord(string $phone, string $purpose): ?OtpVerify
    {
        $validMinutes = (int) config('otp.verification_valid_minutes', 30);

        return OtpVerify::query()
            ->where('phone', $phone)
            ->where('purpose', $purpose)
            ->where('status', OtpVerify::STATUS_VERIFIED)
            ->whereNull('consumed_at')
            ->where('verified_at', '>=', now()->subMinutes($validMinutes))
            ->latest('verified_at')
            ->first();
    }

    protected function activePendingRecord(string $phone, string $purpose): ?OtpVerify
    {
        return OtpVerify::query()
            ->where('phone', $phone)
            ->where('purpose', $purpose)
            ->where('status', OtpVerify::STATUS_PENDING)
            ->where('expires_at', '>', now())
            ->latest('id')
            ->first();
    }

    protected function expirePending(string $phone, string $purpose): void
    {
        OtpVerify::query()
            ->where('phone', $phone)
            ->where('purpose', $purpose)
            ->where('status', OtpVerify::STATUS_PENDING)
            ->update(['status' => OtpVerify::STATUS_EXPIRED]);
    }

    protected function purposeKey(OtpPurpose|string $purpose): string
    {
        $key = $purpose instanceof OtpPurpose ? $purpose->value : $purpose;

        if (! is_array(config('otp.purposes.'.$key))) {
            throw new InvalidArgumentException("Unknown OTP purpose [{$key}].");
        }

        return $key;
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
