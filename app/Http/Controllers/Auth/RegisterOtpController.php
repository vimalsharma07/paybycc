<?php

namespace App\Http\Controllers\Auth;

use App\Enums\OtpPurpose;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Otp\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegisterOtpController extends Controller
{
    public function send(Request $request, OtpService $otp): JsonResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'regex:/^[6-9]\d{9}$/'],
        ]);

        if (User::where('phone', $validated['phone'])->exists()) {
            return response()->json([
                'ok' => false,
                'message' => 'This mobile number is already registered.',
            ], 422);
        }

        $result = $otp->sendSms(
            $validated['phone'],
            OtpPurpose::Registration,
            $request->ip()
        );

        return response()->json([
            'ok' => $result->success,
            'message' => $result->message,
            'retry_after' => $result->retryAfter,
        ], $result->success ? 200 : 422);
    }

    public function verify(Request $request, OtpService $otp): JsonResponse
    {
        $length = (int) config('otp.length', 6);

        $validated = $request->validate([
            'phone' => ['required', 'string', 'regex:/^[6-9]\d{9}$/'],
            'otp' => ['required', 'string', 'regex:/^\d{'.$length.'}$/'],
        ]);

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
