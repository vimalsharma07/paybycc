<?php

namespace App\Services\Payments;

use App\Models\GatewayReqRes;
use App\Models\Payment;
use App\Models\Transaction;

class GatewayReqResService
{
    /**
     * @param  array<string, mixed>|null  $req
     * @param  array<string, mixed>|null  $response
     * @param  array<string, mixed>|null  $webhook
     */
    public function store(
        ?int $transactionId = null,
        ?array $req = null,
        ?array $response = null,
        ?array $webhook = null,
        ?string $status = null,
    ): GatewayReqRes {
        return GatewayReqRes::query()->create([
            'transaction_id' => $transactionId,
            'req' => $req,
            'response' => $response,
            'webhook' => $webhook,
            'status' => $status,
        ]);
    }

    public function transactionIdForPayment(?Payment $payment): ?int
    {
        if (! $payment) {
            return null;
        }

        $id = $payment->transactions()->value('id');

        return $id ? (int) $id : null;
    }

    public function transactionIdFromGatewayReference(?string $gatewayReference): ?int
    {
        if (! is_string($gatewayReference) || $gatewayReference === '') {
            return null;
        }

        $payment = Payment::query()
            ->where('gateway_reference', $gatewayReference)
            ->first();

        return $this->transactionIdForPayment($payment);
    }

    /**
     * @param  array<string, mixed>  $api
     * @return array<string, mixed>
     */
    public function normalizeApiResponse(array $api): array
    {
        return [
            'ok' => (bool) ($api['ok'] ?? false),
            'http_status' => $api['status'] ?? null,
            'error' => $api['error'] ?? null,
            'data' => $api['data'] ?? null,
        ];
    }
}
