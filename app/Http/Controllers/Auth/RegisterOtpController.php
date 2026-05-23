<?php

namespace App\Http\Controllers\Auth;

use App\Enums\OtpPurpose;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Logging\AppLogger;
use App\Services\Otp\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class RegisterOtpController extends Controller
{
    public function send(Request $request, OtpService $otp, AppLogger $appLog): JsonResponse
    {
        try {
            $validated = $request->validate([
                'phone' => ['required', 'string', 'regex:/^[6-9]\d{9}$/'],
            ]);
        } catch (ValidationException $e) {
            $appLog->notice('otp', 'otp.http.validation_failed', 'OTP send validation failed', [
                'errors' => $e->errors(),
                'ip' => $request->ip(),
            ]);

            throw $e;
        }

        if (User::where('phone', $validated['phone'])->exists()) {
            $appLog->notice('otp', 'otp.send.phone_registered', 'OTP send blocked — phone already registered', [
                'phone' => $validated['phone'],
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'This mobile number is already registered.',
            ], 422);
        }

        try {
            $result = $otp->sendSms(
                $validated['phone'],
                OtpPurpose::Registration,
                $request->ip()
            );
        } catch (Throwable $e) {
            $appLog->error('otp', 'otp.send.exception', 'OTP send uncaught exception', [
                'phone' => $validated['phone'],
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }

        return response()->json([
            'ok' => $result->success,
            'message' => $result->message,
            'retry_after' => $result->retryAfter,
        ], $result->success ? 200 : 422);
    }

    public function verify(Request $request, OtpService $otp, AppLogger $appLog): JsonResponse
    {
        $length = (int) config('otp.length', 6);

        try {
            $validated = $request->validate([
                'phone' => ['required', 'string', 'regex:/^[6-9]\d{9}$/'],
                'otp' => ['required', 'string', 'regex:/^\d{'.$length.'}$/'],
            ]);
        } catch (ValidationException $e) {
            $appLog->notice('otp', 'otp.http.validation_failed', 'OTP verify validation failed', [
                'errors' => $e->errors(),
            ]);

            throw $e;
        }

        if (User::where('phone', $validated['phone'])->exists()) {
            return response()->json([
                'ok' => false,
                'message' => 'This mobile number is already registered.',
            ], 422);
        }

        $result = $otp->verifySms($validated['phone'], OtpPurpose::Registration, $validated['otp']);

        return response()->json([
            'ok' => $result->success,
            'message' => $result->message,
        ], $result->success ? 200 : 422);
    }
}
