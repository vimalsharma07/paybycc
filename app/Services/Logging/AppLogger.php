<?php

namespace App\Services\Logging;

use App\Enums\LogLevel;
use App\Models\ApplicationLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Throwable;

class AppLogger
{
    protected static ?string $requestId = null;

    public function log(
        LogLevel $level,
        string $channel,
        string $event,
        string $message,
        array $context = [],
        ?Model $subject = null,
        ?int $userId = null,
        ?string $ipAddress = null,
    ): ?ApplicationLog {
        if (! config('app_log.enabled', true)) {
            return null;
        }

        try {
            return ApplicationLog::create([
                'level' => $level->value,
                'channel' => $channel,
                'event' => $event,
                'message' => $message,
                'context' => $context === [] ? null : $this->sanitizeContext($context),
                'user_id' => $userId ?? auth()->id(),
                'subject_type' => $subject?->getMorphClass(),
                'subject_id' => $subject?->getKey(),
                'ip_address' => $ipAddress ?? Request::ip(),
                'request_id' => $this->requestId(),
            ]);
        } catch (Throwable $e) {
            report($e);

            return null;
        }
    }

    public function debug(string $channel, string $event, string $message, array $context = [], ?Model $subject = null): ?ApplicationLog
    {
        return $this->log(LogLevel::Debug, $channel, $event, $message, $context, $subject);
    }

    public function info(string $channel, string $event, string $message, array $context = [], ?Model $subject = null): ?ApplicationLog
    {
        return $this->log(LogLevel::Info, $channel, $event, $message, $context, $subject);
    }

    public function notice(string $channel, string $event, string $message, array $context = [], ?Model $subject = null): ?ApplicationLog
    {
        return $this->log(LogLevel::Notice, $channel, $event, $message, $context, $subject);
    }

    public function warning(string $channel, string $event, string $message, array $context = [], ?Model $subject = null): ?ApplicationLog
    {
        return $this->log(LogLevel::Warning, $channel, $event, $message, $context, $subject);
    }

    public function error(string $channel, string $event, string $message, array $context = [], ?Model $subject = null): ?ApplicationLog
    {
        return $this->log(LogLevel::Error, $channel, $event, $message, $context, $subject);
    }

    public function requestId(): string
    {
        if (self::$requestId === null) {
            self::$requestId = (string) Str::uuid();
        }

        return self::$requestId;
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    protected function sanitizeContext(array $context): array
    {
        $sensitive = ['otp', 'password', 'token', 'authkey', 'secret', 'api_key', 'pan', 'aadhar'];

        foreach ($context as $key => $value) {
            if (is_string($key) && in_array(strtolower($key), $sensitive, true)) {
                $context[$key] = '[redacted]';

                continue;
            }

            if (is_array($value)) {
                $context[$key] = $this->sanitizeContext($value);
            }
        }

        return $context;
    }
}
