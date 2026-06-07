<?php

namespace App\Gateways\Contracts;

interface DefinesGatewayCredentials
{
    /**
     * Default credential keys and values for Admin → Gateways (JSON).
     *
     * @return array<string, string>
     */
    public static function defaultCredentials(): array;

    /**
     * @param  array<string, mixed>  $credentials
     */
    public static function credentialsComplete(array $credentials): bool;
}
