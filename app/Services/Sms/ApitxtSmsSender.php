<?php

namespace App\Services\Sms;

use App\Contracts\SmsSender;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApitxtSmsSender implements SmsSender
{
    /**
     * @return array{ok: bool, error?: string}
     */
    public function send(string $mobile, string $message): array
    {
        $authkey = config('sms.apitxt.authkey');
        $sender = config('sms.apitxt.sender');
        $templateId = config('sms.apitxt.template_id');
        $peId = config('sms.apitxt.pe_id');

        if (! is_string($authkey) || $authkey === ''
            || ! is_string($sender) || $sender === ''
            || ! is_string($templateId) || $templateId === ''
            || ! is_string($peId) || $peId === '') {
            return ['ok' => false, 'error' => 'SMS is not configured.'];
        }

        try {
            $response = Http::timeout(15)
                ->acceptJson()
                ->get((string) config('sms.apitxt.url'), [
                    'authkey' => $authkey,
                    'mobiles' => $mobile,
                    'message' => $message,
                    'sender' => strtoupper($sender),
                    'route' => (string) config('sms.apitxt.route', '4'),
                    'template_id' => $templateId,
                    'pe_id' => $peId,
                    'flash' => 0,
                    'unicode' => 0,
                ]);
        } catch (\Throwable $e) {
            Log::warning('SMS request failed', ['mobile' => $mobile, 'error' => $e->getMessage()]);

            return ['ok' => false, 'error' => 'Could not send SMS. Please try again.'];
        }

        if (! $response->successful()) {
            Log::warning('SMS API HTTP error', [
                'mobile' => $mobile,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return ['ok' => false, 'error' => 'Could not send SMS. Please try again.'];
        }

        $body = $response->json();
        if (! is_array($body)) {
            return ['ok' => false, 'error' => 'Unexpected SMS provider response.'];
        }

        $status = $body['status'] ?? null;
        if ($status !== 200 && $status !== '200') {
            Log::warning('SMS API rejected request', ['mobile' => $mobile, 'body' => $body]);

            return ['ok' => false, 'error' => 'Could not send SMS. Please try again.'];
        }

        return ['ok' => true];
    }
}
