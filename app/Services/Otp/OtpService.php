<?php

namespace App\Services\Otp;

use App\Contracts\SmsSender;
use App\Enums\OtpPurpose;
use App\Models\OtpVerify;
use App\Services\Logging\AppLogger;
use App\Support\PhoneNumber;
use InvalidArgumentException;

class OtpService
{
    public function __construct(
        protected SmsSender $sms,
        protected OtpRateLimiter $rateLimiter,
        protected AppLogger $appLog,
    ) {}

    public function sendSms(string $phone, OtpPurpose|string $purpose, ?string $ipAddress = null): OtpResult
    {
        $phone = PhoneNumber::normalize($phone);
        $purposeKey = $this->purposeKey($purpose);

        $this->appLog->info('otp', 'otp.send.requested', 'OTP send requested', [
            'phone' => $phone,
            'purpose' => $purposeKey,
            'ip' => $ipAddress,
        ]);

        if (! PhoneNumber::isValidIndianMobile($phone)) {
            $this->appLog->warning('otp', 'otp.send.invalid_phone', 'Invalid mobile number', [
                'phone' => $phone,
                'purpose' => $purposeKey,
            ]);

            return OtpResult::fail('Enter a valid 10-digit Indian mobile number.');
        }

        $dailyCheck = $this->rateLimiter->checkDailySmsLimit($phone);
        if (! $dailyCheck['allowed']) {
            $this->appLog->warning('otp', 'otp.send.daily_limit', 'Daily SMS limit reached', [
                'phone' => $phone,
                'purpose' => $purposeKey,
                'retry_after' => $dailyCheck['retry_after'] ?? null,
            ]);

            return OtpResult::fail(
                (string) ($dailyCheck['message'] ?? 'Daily limit reached.'),
                $dailyCheck['retry_after'] ?? null
            );
        }

        $cooldown = $this->rateLimiter->resendCooldownSeconds($purposeKey, $phone);
        if ($cooldown !== null) {
            $this->appLog->notice('otp', 'otp.send.cooldown', 'Resend cooldown active', [
                'phone' => $phone,
                'purpose' => $purposeKey,
                'retry_after' => $cooldown,
            ]);

            return OtpResult::fail(
                'Please wait before requesting another code.',
                $cooldown
            );
        }

        $otp = $this->generateCode();

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

        $this->appLog->info('otp', 'otp.record.created', 'OTP record created', [
            'phone' => $phone,
            'purpose' => $purposeKey,
            'otp_verify_id' => $record->id,
            'expires_at' => $record->expires_at?->toIso8601String(),
        ], $record);

        $smsContext = [
            'phone' => $phone,
            'purpose' => $purposeKey,
            'sms_driver' => config('sms.driver'),
        ];
        if (config('app_log.log_otp_code')) {
            $smsContext['otp'] = $otp;
        }

        $smsResult = $this->deliverSms($phone, $otp, $purposeKey, $smsContext, $record);
        if (! ($smsResult['ok'] ?? false)) {
            $record->update(['status' => OtpVerify::STATUS_FAILED]);

            $this->appLog->error('otp', 'otp.sms.failed', 'SMS delivery failed', [
                'phone' => $phone,
                'purpose' => $purposeKey,
                'otp_verify_id' => $record->id,
                'sms_driver' => config('sms.driver'),
                'sms_error' => $smsResult['error'] ?? null,
            ], $record);

            return OtpResult::fail($this->userFacingSmsError($smsResult['error'] ?? null));
        }

        $record->update(['sms_sent_at' => now()]);

        $this->appLog->info('otp', 'otp.sms.sent', 'OTP SMS sent successfully', [
            'phone' => $phone,
            'purpose' => $purposeKey,
            'otp_verify_id' => $record->id,
        ], $record);

        return OtpResult::ok('Verification code sent to +91 '.$phone.'.');
    }

    public function verifySms(string $phone, OtpPurpose|string $purpose, string $code): OtpResult
    {
        $phone = PhoneNumber::normalize($phone);
        $purposeKey = $this->purposeKey($purpose);
        $code = preg_replace('/\D/', '', $code) ?? '';
        $length = (int) config('otp.length', 6);

        $this->appLog->info('otp', 'otp.verify.requested', 'OTP verify requested', [
            'phone' => $phone,
            'purpose' => $purposeKey,
        ]);

        if (! PhoneNumber::isValidIndianMobile($phone)) {
            return OtpResult::fail('Enter a valid mobile number.');
        }

        if (strlen($code) !== $length) {
            $this->appLog->notice('otp', 'otp.verify.invalid_format', 'OTP wrong length', [
                'phone' => $phone,
                'purpose' => $purposeKey,
            ]);

            return OtpResult::fail('Enter the '.$length.'-digit code from your SMS.');
        }

        $record = $this->activePendingRecord($phone, $purposeKey);

        if ($record === null) {
            $this->appLog->warning('otp', 'otp.verify.no_pending', 'No active pending OTP', [
                'phone' => $phone,
                'purpose' => $purposeKey,
            ]);

            return OtpResult::fail('This code has expired. Request a new one.');
        }

        if ($record->isExpired()) {
            $record->update(['status' => OtpVerify::STATUS_EXPIRED]);

            $this->appLog->warning('otp', 'otp.verify.expired', 'OTP expired', [
                'phone' => $phone,
                'otp_verify_id' => $record->id,
            ], $record);

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

            $this->appLog->warning('otp', 'otp.verify.wrong_code', 'Incorrect OTP', [
                'phone' => $phone,
                'otp_verify_id' => $record->id,
                'attempts' => $record->attempts,
                'remaining' => max(0, $remaining),
            ], $record);

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

        $this->appLog->info('otp', 'otp.verify.success', 'OTP verified', [
            'phone' => $phone,
            'purpose' => $purposeKey,
            'otp_verify_id' => $record->id,
        ], $record);

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
            $this->appLog->warning('otp', 'otp.consume.missing', 'No verified OTP to consume on register', [
                'phone' => $phone,
                'purpose' => $purposeKey,
                'user_id' => $userId,
            ]);

            return;
        }

        $record->update([
            'user_id' => $userId,
            'consumed_at' => now(),
        ]);

        $this->appLog->info('otp', 'otp.consume.success', 'OTP consumed for registration', [
            'phone' => $phone,
            'user_id' => $userId,
            'otp_verify_id' => $record->id,
        ], $record);
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
        $count = OtpVerify::query()
            ->where('phone', $phone)
            ->where('purpose', $purpose)
            ->where('status', OtpVerify::STATUS_PENDING)
            ->update(['status' => OtpVerify::STATUS_EXPIRED]);

        if ($count > 0) {
            $this->appLog->debug('otp', 'otp.pending.expired', 'Expired previous pending OTP rows', [
                'phone' => $phone,
                'purpose' => $purpose,
                'count' => $count,
            ]);
        }
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

    /**
     * @param  array<string, mixed>  $context
     * @return array{ok: bool, error?: string}
     */
    protected function deliverSms(string $phone, string $otp, string $purpose, array $context, OtpVerify $record): array
    {
        if (config('sms.driver') === 'log') {
            $this->appLog->info('sms', 'sms.log_driver', 'SMS skipped (log driver)', array_merge($context, [
                'otp' => $otp,
            ]), $record);

            return ['ok' => true];
        }

        return $this->sms->sendOtp($phone, $otp);
    }

    protected function userFacingSmsError(?string $providerError): string
    {
        if ($providerError === 'SMS is not configured.') {
            return 'SMS is not set up on the server. Set SMS_AUTHKEY in .env (apitxt dashboard).';
        }

        if (! is_string($providerError) || $providerError === '') {
            return 'We could not send the OTP. Please try again in a moment.';
        }

        if (config('app.debug') || preg_match('/template|sender|DLT|not found|invalid|rejected/i', $providerError)) {
            return 'SMS provider: '.$providerError;
        }

        return 'We could not send the OTP. Please try again in a moment.';
    }
}
