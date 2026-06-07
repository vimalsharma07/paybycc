<?php

namespace App\Gateways\Contracts;

use App\Models\Payment;

interface HostedCheckoutGateway
{
    /**
     * @param  array<string, mixed>  $initResult
     */
    public function isHostedInitResult(array $initResult): bool;

    public function isHostedPayment(Payment $payment): bool;

    /**
     * @return array<string, mixed>|null  View data for payments.checkout, or null if not applicable.
     */
    public function hostedCheckoutView(Payment $payment): ?array;
}
