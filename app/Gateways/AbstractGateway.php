<?php

namespace App\Gateways;

use App\Enums\LogLevel;
use App\Gateways\Contracts\GatewayDriver;
use App\Models\Payment;
use App\Services\Logging\FlowLog;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractGateway implements GatewayDriver
{
    /**
     * @param  array<string, string|int|float|bool|null>  $credentials
     */
    public function __construct(
        protected array $credentials = []
    ) {}

    protected function credential(string $key, mixed $default = null): mixed
    {
        return $this->credentials[$key] ?? $default;
    }

    protected function flow(): FlowLog
    {
        return app(FlowLog::class);
    }

    /**
     * @param  array<string, mixed>  $context
     */
    protected function logGateway(
        string $event,
        string $message,
        array $context = [],
        ?Model $subject = null,
        LogLevel $level = LogLevel::Info,
    ): void {
        $this->flow()->gateway($event, $message, $context, $subject, $level);
    }

    /**
     * @param  array<string, mixed>  $context
     */
    protected function logGatewayForPayment(
        Payment $payment,
        string $event,
        string $message,
        array $context = [],
        LogLevel $level = LogLevel::Info,
    ): void {
        $payment->loadMissing('order');
        $this->logGateway(
            $event,
            $message,
            $this->flow()->gatewayContext($payment, $context),
            $payment->order ?? $payment,
            $level,
        );
    }
}
