<?php

namespace App\Gateways\Contracts;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

interface HandlesPaymentWebhook
{
    public function handleWebhook(Request $request): Response;
}
