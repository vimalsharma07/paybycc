<?php

namespace App\Services\Logging;

use App\Enums\LogLevel;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

/**
 * Structured logs for user-facing flows (auth, kyc, etc.).
 */
class FlowLog
{
    public function __construct(
        protected AppLogger $logger,
    ) {}

    public function auth(
        string $event,
        string $message,
        array $context = [],
        ?Model $subject = null,
        LogLevel $level = LogLevel::Info,
    ): void {
        $this->logger->log($level, 'auth', $event, $message, $context, $subject);
    }

    public function kyc(
        string $event,
        string $message,
        array $context = [],
        ?Model $subject = null,
        LogLevel $level = LogLevel::Info,
    ): void {
        $this->logger->log($level, 'kyc', $event, $message, $context, $subject);
    }

    /**
     * @return array<string, mixed>
     */
    public function userContext(User $user, array $extra = []): array
    {
        return array_merge([
            'user_id' => $user->id,
            'user_code' => $user->user_code,
            'email' => $user->email,
            'kyc_status' => $user->kyc_status,
        ], $extra);
    }

    /**
     * @return array<string, mixed>
     */
    public function validationErrors(ValidationException $e): array
    {
        return ['fields' => array_keys($e->errors())];
    }

    /**
     * @return array<string, mixed>
     */
    public function maskedPan(?string $pan): array
    {
        if ($pan === null || $pan === '') {
            return [];
        }

        $pan = strtoupper($pan);

        return ['pan_masked' => substr($pan, 0, 2).'****'.substr($pan, -2)];
    }

    /**
     * @return array<string, mixed>
     */
    public function maskedAadhar(?string $aadhar): array
    {
        if ($aadhar === null || $aadhar === '') {
            return [];
        }

        return ['aadhar_masked' => '****'.substr($aadhar, -4)];
    }
}
