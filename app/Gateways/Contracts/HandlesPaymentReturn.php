<?php

namespace App\Gateways\Contracts;

use App\Models\Payment;
use Illuminate\Http\Request;

interface HandlesPaymentReturn
{
    public static function gatewayCode(): string;

    public function returnUrl(Payment $payment): string;

    public function handleReturn(Request $request, Payment $payment): Payment;
}
