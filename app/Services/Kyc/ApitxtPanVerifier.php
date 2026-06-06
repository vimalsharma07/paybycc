<?php

namespace App\Services\Kyc;

use App\Services\Logging\AppLogger;
use Illuminate\Support\Facades\Http;

class ApitxtPanVerifier
{
    public function __construct(
        protected AppLogger $appLog,
    ) {}

    public function verify(string $pan, string $name, string $dob): PanVerificationResult
    {
        $authkey = config('pan.apitxt.authkey');

        if (! is_string($authkey) || $authkey === '') {
            $this->appLog->error('kyc', 'pan.verify.config_missing', 'PAN verify API not configured', [
                'missing_env' => ['PAN_VERIFY_AUTHKEY or SMS_AUTHKEY'],
            ]);

            return PanVerificationResult::failure(
                'config_missing',
                userMessage: 'PAN verification is not configured. Please contact support.',
            );
        }

        $url = (string) config('pan.apitxt.url');
        $payload = [
            'authkey' => $authkey,
            'pan' => strtoupper($pan),
            'name' => $name,
            'dob' => $dob,
        ];

        $this->appLog->info('kyc', 'pan.verify.request', 'Calling apitxt panVerify', [
            'url' => $url,
            'pan_masked' => $this->maskPan($payload['pan']),
        ]);

        try {
            $response = Http::timeout(20)
                ->acceptJson()
                ->asForm()
                ->post($url, $payload);
        } catch (\Throwable $e) {
            $this->appLog->error('kyc', 'pan.verify.exception', 'PAN verify HTTP exception', [
                'pan_masked' => $this->maskPan($payload['pan']),
                'error' => $e->getMessage(),
            ]);

            return PanVerificationResult::failure(
                'http_exception',
                userMessage: 'Could not verify PAN right now. Please try again.',
            );
        }

        $body = $response->json();
        if (! is_array($body)) {
            $this->appLog->error('kyc', 'pan.verify.invalid_json', 'PAN verify invalid JSON', [
                'http_status' => $response->status(),
                'body' => $response->body(),
            ]);

            return PanVerificationResult::failure(
                'invalid_json',
                userMessage: 'Unexpected response from PAN verification. Please try again.',
            );
        }

        return $this->parseResponse($body);
    }

    /**
     * @param  array<string, mixed>  $body
     */
    protected function parseResponse(array $body): PanVerificationResult
    {
        $status = $body['status'] ?? null;
        $message = is_string($body['message'] ?? null) ? $body['message'] : '';
        $data = is_array($body['data'] ?? null) ? $body['data'] : [];

        $httpOk = (int) $status === 200 || strtolower($message) === 'success';

        if (! $httpOk) {
            $code = is_numeric($body['status'] ?? null) ? (int) $body['status'] : 0;

            $this->appLog->warning('kyc', 'pan.verify.rejected', 'PAN verify API rejected', [
                'api_status' => $status,
                'api_message' => $message,
            ]);

            return PanVerificationResult::failure(
                $message !== '' ? $message : 'rejected',
                $body,
                $this->userMessageForCode($code, $message),
            );
        }

        if (! $this->isVerifiedData($data)) {
            return PanVerificationResult::failure(
                'not_verified',
                $body,
                $this->buildMismatchMessage($data),
            );
        }

        return PanVerificationResult::success($body, $data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function isVerifiedData(array $data): bool
    {
        $verified = filter_var($data['verified'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $panStatus = strtolower((string) ($data['status'] ?? ''));
        $nameMatch = filter_var($data['name_match'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $dobMatch = filter_var($data['dob_match'] ?? false, FILTER_VALIDATE_BOOLEAN);

        return $verified
            && $panStatus === 'valid'
            && $nameMatch
            && $dobMatch;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function buildMismatchMessage(array $data): string
    {
        $nameMatch = filter_var($data['name_match'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $dobMatch = filter_var($data['dob_match'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $panStatus = strtolower((string) ($data['status'] ?? ''));

        if ($panStatus === 'invalid') {
            return 'This PAN is not valid. Please check the number and try again.';
        }

        if (! $nameMatch && ! $dobMatch) {
            return 'Name and date of birth do not match PAN records.';
        }

        if (! $nameMatch) {
            return 'Name does not match PAN records. Enter the name exactly as on your PAN card.';
        }

        if (! $dobMatch) {
            return 'Date of birth does not match PAN records. Use DD/MM/YYYY as on your PAN card.';
        }

        return 'PAN could not be verified. Please check your details and try again.';
    }

    protected function userMessageForCode(int $code, string $apiMessage): string
    {
        $mapped = match ($code) {
            105 => 'PAN verification is not configured. Please contact support.',
            106, 107, 108 => 'Please provide PAN, name, and date of birth.',
            206 => 'Invalid PAN format.',
            207 => 'Invalid date of birth. Use DD/MM/YYYY as on your PAN card.',
            301 => 'PAN verification service is temporarily unavailable.',
            304 => 'PAN verification authentication failed. Please contact support.',
            310 => 'PAN could not be verified with the registry. Please check your details.',
            default => null,
        };

        if ($mapped !== null) {
            return $mapped;
        }

        return $apiMessage !== ''
            ? $apiMessage
            : 'PAN verification failed. Please try again.';
    }

    protected function maskPan(string $pan): string
    {
        $pan = strtoupper($pan);
        if (strlen($pan) < 6) {
            return '******';
        }

        return substr($pan, 0, 2).'****'.substr($pan, -2);
    }
}
