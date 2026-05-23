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
    public function sendOtp(string $mobile, string $otp): array
    {
        $authkey = config('sms.apitxt.authkey');

        if (! is_string($authkey) || $authkey === '') {
            $this->appLog->error('sms', 'sms.config.missing', 'SMS API not configured', [
                'mobile' => $mobile,
                'missing_env' => ['SMS_AUTHKEY'],
            ]);

            return ['ok' => false, 'error' => 'SMS is not configured.'];
        }

        $url = (string) config('sms.apitxt.url');
        $formattedMobile = $this->formatMobile($mobile);

        $payload = [
            'authkey' => $authkey,
            'mobile' => $formattedMobile,
            'otp' => $otp,
            'channel' => (string) config('sms.apitxt.channel', 'sms'),
            'country' => (string) config('sms.apitxt.country', '91'),
        ];

        $templateId = config('sms.apitxt.template_id');
        if ($templateId !== null && $templateId !== '') {
            $payload['template_id'] = (int) $templateId;
        }

        $this->appLog->info('sms', 'sms.api.request', 'Calling apitxt sendOTP', [
            'url' => $url,
            'mobile' => $formattedMobile,
            'channel' => $payload['channel'],
            'has_template_id' => array_key_exists('template_id', $payload),
        ]);

        try {
            $response = Http::timeout(15)
                ->acceptJson()
                ->asForm()
                ->post($url, $payload);
        } catch (\Throwable $e) {
            $this->appLog->error('sms', 'sms.api.exception', 'SMS HTTP exception', [
                'mobile' => $formattedMobile,
                'error' => $e->getMessage(),
            ]);

            return ['ok' => false, 'error' => 'Could not send SMS. Please try again.'];
        }

        if (! $response->successful()) {
            $this->appLog->error('sms', 'sms.api.http_error', 'SMS API HTTP error', [
                'mobile' => $formattedMobile,
                'http_status' => $response->status(),
                'body' => $response->body(),
            ]);

            return ['ok' => false, 'error' => 'Could not send SMS. Please try again.'];
        }

        $body = $response->json();
        if (! is_array($body)) {
            $this->appLog->error('sms', 'sms.api.invalid_json', 'SMS API invalid JSON', [
                'mobile' => $formattedMobile,
                'body' => $response->body(),
            ]);

            return ['ok' => false, 'error' => 'Unexpected SMS provider response.'];
        }

        $status = strtolower((string) ($body['status'] ?? ''));
        if ($status !== 'success') {
            $apiMessage = is_string($body['message'] ?? null) && $body['message'] !== ''
                ? $body['message']
                : 'Could not send SMS. Please try again.';

            $this->appLog->error('sms', 'sms.api.rejected', 'SMS API rejected request', [
                'mobile' => $formattedMobile,
                'response' => $body,
            ]);

            return ['ok' => false, 'error' => $apiMessage];
        }

        $requestId = null;
        if (isset($body['data']) && is_array($body['data'])) {
            $requestId = $body['data']['request_id'] ?? null;
        }

        $this->appLog->info('sms', 'sms.api.success', 'SMS OTP sent successfully', [
            'mobile' => $formattedMobile,
            'request_id' => $requestId,
            'response' => $body,
        ]);

        return [
            'ok' => true,
            'request_id' => is_string($requestId) ? $requestId : null,
        ];
    }

    protected function formatMobile(string $mobile): string
    {
        $digits = preg_replace('/\D/', '', $mobile) ?? '';

        if (strlen($digits) === 10) {
            return '91'.$digits;
        }

        return $digits;
    }
}
