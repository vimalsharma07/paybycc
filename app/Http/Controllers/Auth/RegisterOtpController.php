<?php

namespace App\Http\Controllers\Auth;

use App\Enums\LogLevel;
use App\Enums\OtpPurpose;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Logging\AppLogger;
use App\Services\Logging\FlowLog;
use App\Services\Otp\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class RegisterOtpController extends Controller
{
    public function send(Request $request, OtpService $otp, AppLogger $appLog, FlowLog $flow): JsonResponse
    {
        try {
            $validated = $request->validate([
                'phone' => ['required', 'string', 'regex:/^[6-9]\d{9}$/'],
            ]);
        } catch (ValidationException $e) {
            $flow->auth('register.otp.validation_failed', 'OTP send validation failed', $flow->validationErrors($e), level: LogLevel::Notice);
            $appLog->notice('otp', 'otp.http.validation_failed', 'OTP send validation failed', [
                'errors' => $e->errors(),
                'ip' => $request->ip(),
            ]);

            throw $e;
        }

        if (User::where('phone', $validated['phone'])->exists()) {
            $flow->auth('register.otp.phone_taken', 'OTP send blocked — phone registered', [
                'phone' => $validated['phone'],
            ], level: LogLevel::Notice);

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
            $flow->auth('register.otp.exception', 'OTP send exception', [
                'phone' => $validated['phone'],
                'error' => $e->getMessage(),
            ], level: LogLevel::Error);
            $appLog->error('otp', 'otp.send.exception', 'OTP send uncaught exception', [
                'phone' => $validated['phone'],
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }

        if ($result->success) {
            $flow->auth('register.otp.sent', 'Registration OTP sent', ['phone' => $validated['phone']]);
        } else {
            $flow->auth('register.otp.send_failed', 'Registration OTP send failed', [
                'phone' => $validated['phone'],
                'message' => $result->message,
            ], level: LogLevel::Warning);
        }

        return response()->json([
            'ok' => $result->success,
            'message' => $result->message,
            'retry_after' => $result->retryAfter,
        ], $result->success ? 200 : 422);
    }

    public function verify(Request $request, OtpService $otp, FlowLog $flow): JsonResponse
    {
        $length = (int) config('otp.length', 6);

        try {
            $validated = $request->validate([
                'phone' => ['required', 'string', 'regex:/^[6-9]\d{9}$/'],
                'otp' => ['required', 'string', 'regex:/^\d{'.$length.'}$/'],
            ]);
        } catch (ValidationException $e) {
            $flow->auth('register.otp.validation_failed', 'OTP verify validation failed', $flow->validationErrors($e), level: LogLevel::Notice);

            throw $e;
        }

        if (User::where('phone', $validated['phone'])->exists()) {
            $flow->auth('register.otp.phone_taken', 'OTP verify blocked — phone registered', [
                'phone' => $validated['phone'],
            ], level: LogLevel::Notice);

            return response()->json([
                'ok' => false,
                'message' => 'This mobile number is already registered.',
            ], 422);
        }

        $result = $otp->verifySms($validated['phone'], OtpPurpose::Registration, $validated['otp']);

        if ($result->success) {
            $flow->auth('register.otp.verified', 'Registration phone verified', [
                'phone' => $validated['phone'],
            ]);
        } else {
            $flow->auth('register.otp.verify_failed', 'Registration OTP verify failed', [
                'phone' => $validated['phone'],
                'message' => $result->message,
            ], level: LogLevel::Warning);
        }

        return response()->json([
            'ok' => $result->success,
            'message' => $result->message,
        ], $result->success ? 200 : 422);
    }
}
