<?php

namespace App\Services\Sms;

use App\Contracts\SmsSender;
use App\Services\Logging\AppLogger;
use Illuminate\Support\Facades\Http;

class ApitxtSmsSender implements SmsSender
{
    public function __construct(
        protected AppLogger $appLog,
    ) {}

    /**
     * @return array{ok: bool, error?: string, request_id?: string}
     */
    public function send(string $mobile, string $message): array
    {
        $authkey = config('sms.apitxt.authkey');
        $sender = config('sms.apitxt.sender');
        $templateId = config('sms.apitxt.template_id');
        $peId = config('sms.apitxt.pe_id');

        $missing = array_values(array_filter([
            ! is_string($authkey) || $authkey === '' ? 'SMS_AUTHKEY' : null,
            ! is_string($sender) || $sender === '' ? 'SMS_SENDER' : null,
            ! is_string($templateId) || $templateId === '' ? 'SMS_TEMPLATE_ID' : null,
            ! is_string($peId) || $peId === '' ? 'SMS_PE_ID' : null,
        ]));

        if ($missing !== []) {
            $this->appLog->error('sms', 'sms.config.missing', 'SMS API not configured', [
                'mobile' => $mobile,
                'missing_env' => $missing,
            ]);

            return ['ok' => false, 'error' => 'SMS is not configured.'];
        }

        $url = (string) config('sms.apitxt.url');

        $this->appLog->info('sms', 'sms.api.request', 'Calling SMS API', [
            'mobile' => $mobile,
            'url' => $url,
            'sender' => strtoupper($sender),
            'route' => (string) config('sms.apitxt.route', '4'),
            'template_id' => $templateId,
            'message_length' => strlen($message),
        ]);

        try {
            $response = Http::timeout(15)
                ->acceptJson()
                ->get($url, [
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
            $this->appLog->error('sms', 'sms.api.exception', 'SMS HTTP exception', [
                'mobile' => $mobile,
                'error' => $e->getMessage(),
            ]);

            return ['ok' => false, 'error' => 'Could not send SMS. Please try again.'];
        }

        if (! $response->successful()) {
            $this->appLog->error('sms', 'sms.api.http_error', 'SMS API HTTP error', [
                'mobile' => $mobile,
                'http_status' => $response->status(),
                'body' => $response->body(),
            ]);

            return ['ok' => false, 'error' => 'Could not send SMS. Please try again.'];
        }

        $body = $response->json();
        if (! is_array($body)) {
            $this->appLog->error('sms', 'sms.api.invalid_json', 'SMS API invalid JSON', [
                'mobile' => $mobile,
                'body' => $response->body(),
            ]);

            return ['ok' => false, 'error' => 'Unexpected SMS provider response.'];
        }

        $status = $body['status'] ?? null;
        if ($status !== 200 && $status !== '200') {
            $this->appLog->error('sms', 'sms.api.rejected', 'SMS API rejected request', [
                'mobile' => $mobile,
                'response' => $body,
            ]);

            return ['ok' => false, 'error' => 'Could not send SMS. Please try again.'];
        }

        $this->appLog->info('sms', 'sms.api.success', 'SMS API accepted request', [
            'mobile' => $mobile,
            'request_id' => $body['request_id'] ?? null,
            'response' => $body,
        ]);

        return [
            'ok' => true,
            'request_id' => is_string($body['request_id'] ?? null) ? $body['request_id'] : null,
        ];
    }
}
