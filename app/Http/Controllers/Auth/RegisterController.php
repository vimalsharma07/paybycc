<?php

namespace App\Http\Controllers\Auth;

use App\Enums\LogLevel;
use App\Enums\OtpPurpose;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Logging\FlowLog;
use App\Services\Otp\OtpService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request, OtpService $otp, FlowLog $flow): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'phone' => ['required', 'string', 'regex:/^[6-9]\d{9}$/', 'unique:users,phone'],
                'password' => ['required', 'confirmed', Password::defaults()],
            ]);
        } catch (ValidationException $e) {
            $flow->auth('register.validation_failed', 'Registration validation failed', array_merge(
                $flow->validationErrors($e),
                ['email' => $request->input('email'), 'phone' => $request->input('phone')]
            ), level: LogLevel::Notice);

            throw $e;
        }

        $flow->auth('register.attempt', 'Registration submit', [
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'name' => $validated['name'],
        ]);

        if (! $otp->isVerified($validated['phone'], OtpPurpose::Registration)) {
            $flow->auth('register.otp_not_verified', 'Registration blocked — phone not OTP verified', [
                'email' => $validated['email'],
                'phone' => $validated['phone'],
            ], level: LogLevel::Warning);

            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors([
                    'phone' => 'Please verify your mobile number with the OTP before creating your account.',
                ]);
        }

        $user = User::create([
            'user_code' => $this->uniqueUserCode(),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => $validated['password'],
            'phone_verified_at' => now(),
            'is_admin' => false,
            'kyc_status' => User::KYC_INCOMPLETE,
            'status' => 'active',
        ]);

        $otp->consumeVerification($validated['phone'], OtpPurpose::Registration, $user->id);

        event(new Registered($user));

        Auth::login($user);

        $request->session()->regenerate();

        $flow->auth('register.success', 'User registered and logged in', $flow->userContext($user, [
            'phone' => $user->phone,
            'phone_verified' => true,
        ]), $user);

        return redirect()->route('kyc.index');
    }

    protected function uniqueUserCode(): string
    {
        do {
            $code = 'PB'.strtoupper(Str::random(8));
        } while (User::where('user_code', $code)->exists());

        return $code;
    }
}
