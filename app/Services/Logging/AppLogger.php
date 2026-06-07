<?php

namespace App\Services\Logging;

use App\Enums\LogLevel;
use App\Models\ApplicationLog;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Transaction;
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
            $links = $this->resolveLinkIds($context, $subject);

            return ApplicationLog::create([
                'level' => $level->value,
                'channel' => $channel,
                'event' => $event,
                'message' => $message,
                'context' => $context === [] ? null : $this->sanitizeContext($context),
                'user_id' => $userId ?? auth()->id(),
                'order_id' => $links['order_id'],
                'transaction_id' => $links['transaction_id'],
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
     * @return array{order_id: ?int, transaction_id: ?int}
     */
    protected function resolveLinkIds(array $context, ?Model $subject): array
    {
        $orderId = $this->positiveInt($context['order_id'] ?? null);
        $transactionId = $this->positiveInt($context['transaction_id'] ?? null);

        if ($subject instanceof Order) {
            $orderId ??= $this->positiveInt($subject->getKey());
        } elseif ($subject instanceof Transaction) {
            $transactionId ??= $this->positiveInt($subject->getKey());
            $orderId ??= $this->orderIdFromPaymentId($subject->payment_id);
        } elseif ($subject instanceof Payment) {
            $orderId ??= $this->positiveInt($subject->order_id);
        }

        if ($orderId === null) {
            $orderId = $this->orderIdFromPaymentId($context['payment_id'] ?? null);
        }

        return [
            'order_id' => $orderId,
            'transaction_id' => $transactionId,
        ];
    }

    protected function orderIdFromPaymentId(mixed $paymentId): ?int
    {
        $id = $this->positiveInt($paymentId);
        if ($id === null) {
            return null;
        }

        $orderId = Payment::query()->whereKey($id)->value('order_id');

        return $this->positiveInt($orderId);
    }

    protected function positiveInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $int = (int) $value;

        return $int > 0 ? $int : null;
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    protected function sanitizeContext(array $context): array
    {
        $sensitive = [
            'otp', 'password', 'token', 'authkey', 'secret', 'api_key', 'pan', 'aadhar',
            'account_no', 'account_number', 'client_secret', 'client_id', 'payment_session_id',
            'credentials',
        ];

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
