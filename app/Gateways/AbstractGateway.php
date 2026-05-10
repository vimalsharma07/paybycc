<?php

namespace App\Gateways;

use App\Gateways\Contracts\GatewayDriver;

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
}
