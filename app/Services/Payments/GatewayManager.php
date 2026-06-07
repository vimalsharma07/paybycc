<?php

namespace App\Services\Payments;

use App\Gateways\Contracts\DefinesGatewayCredentials;
use App\Gateways\Contracts\GatewayDriver;
use App\Models\Gateway;
use App\Models\Payment;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

class GatewayManager
{
    public function __construct(
        protected Container $container
    ) {}

    public function primaryGateway(): ?Gateway
    {
        return Gateway::query()->activePrimary()->first();
    }

    /**
     * Resolve the PHP class for a gateway row (App\Gateways\{Filename}).
     */
    public function resolveDriver(Gateway $gateway): GatewayDriver
    {
        $base = Gateway::normalizeFilename($gateway->filename);
        if ($base === '') {
            throw new InvalidArgumentException('Gateway filename is empty.');
        }

        $class = 'App\\Gateways\\'.Str::studly($base);

        if (! class_exists($class)) {
            throw new RuntimeException(
                "Gateway class not found: {$class}. Create app/Gateways/{$base}.php implementing ".GatewayDriver::class
            );
        }

        $instance = $this->container->make($class, [
            'credentials' => is_array($gateway->credentials) ? $gateway->credentials : [],
        ]);

        if (! $instance instanceof GatewayDriver) {
            throw new RuntimeException("{$class} must implement ".GatewayDriver::class);
        }

        return $instance;
    }

    /**
     * Primary active gateway driver, or null if none configured.
     */
    public function primaryDriver(): ?GatewayDriver
    {
        $gateway = $this->primaryGateway();

        if (! $gateway || ! $gateway->isActive()) {
            return null;
        }

        return $this->resolveDriver($gateway);
    }

    public function resolveDriverByCode(string $code): GatewayDriver
    {
        $gateway = Gateway::query()->where('code', $code)->first();

        if (! $gateway) {
            throw new InvalidArgumentException("Gateway not found: {$code}");
        }

        return $this->resolveDriver($gateway);
    }

    public function primaryReady(): bool
    {
        $gateway = $this->primaryGateway();

        if (! $gateway || ! $gateway->isActive()) {
            return false;
        }

        try {
            $driver = $this->resolveDriver($gateway);
            $class = $driver::class;

            if (is_subclass_of($class, DefinesGatewayCredentials::class)
                || in_array(DefinesGatewayCredentials::class, class_implements($class) ?: [], true)) {
                $credentials = is_array($gateway->credentials) ? $gateway->credentials : [];

                return $class::credentialsComplete($credentials);
            }
        } catch (\Throwable $e) {
            report($e);

            return false;
        }

        return true;
    }

    public function driverForPayment(Payment $payment): ?GatewayDriver
    {
        $payment->loadMissing('gateway');
        $gateway = $payment->gateway;

        if (! $gateway) {
            return null;
        }

        try {
            return $this->resolveDriver($gateway);
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }
}
