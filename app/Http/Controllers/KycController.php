<?php

namespace App\Http\Controllers;

use App\Enums\LogLevel;
use App\Http\Requests\Kyc\StoreKycPanRequest;
use App\Models\User;
use App\Services\Kyc\PanVerificationService;
use App\Services\Logging\FlowLog;
use App\Services\PaymentLinks\PaymentLinkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class KycController extends Controller
{
    public function index(FlowLog $flow): View|RedirectResponse
    {
        $user = auth()->user();

        if ($user->is_admin) {
            $flow->kyc('kyc.index.redirect_admin', 'Admin redirected from KYC page', $flow->userContext($user), $user);

            return redirect()->route('admin.dashboard');
        }

        if ($user->hasActiveKyc()) {
            return redirect()->route('dashboard');
        }

        $flow->kyc('kyc.index.view', 'KYC form opened', $flow->userContext($user, [
            'has_pan' => $user->pan !== null,
        ]), $user);

        return view('kyc.index', ['user' => $user]);
    }

    public function skip(FlowLog $flow): RedirectResponse
    {
        $user = request()->user();

        if ($user->is_admin || $user->hasActiveKyc()) {
            return redirect()->route('dashboard');
        }

        if ($user->hasSkippedKyc()) {
            return redirect()->route('dashboard');
        }

        $user->kyc_skipped_at = now();
        $user->save();

        $flow->kyc('kyc.skip', 'User skipped KYC for now', $flow->userContext($user), $user);

        return redirect()
            ->route('dashboard')
            ->with('status', 'You can explore the platform and pay with card. Complete KYC anytime from your profile to receive payouts.');
    }

    public function storePan(StoreKycPanRequest $request, PanVerificationService $panVerification, PaymentLinkService $paymentLinks, FlowLog $flow): RedirectResponse
    {
        $user = $request->user();
        $pan = $request->normalizedPan();

        $flow->kyc('kyc.submit.attempt', 'KYC submit', array_merge(
            $flow->userContext($user),
            $flow->maskedPan($pan),
            $flow->maskedAadhar($request->aadhar()),
            ['pan_name' => $request->panName(), 'has_aadhar' => $request->aadhar() !== null]
        ), $user);

        if ($message = $panVerification->assertPanNotUsedByAnotherUser($pan, $user)) {
            $flow->kyc('kyc.submit.duplicate_pan', 'PAN already used by another user', $flow->userContext($user, $flow->maskedPan($pan)), $user, LogLevel::Notice);

            return back()
                ->withInput()
                ->withErrors(['pan' => $message]);
        }

        $result = $panVerification->verify($pan, $request->panName(), $request->dob());

        if (! $result->ok) {
            $flow->kyc('kyc.submit.verify_failed', 'PAN API verification failed', $flow->userContext($user, [
                'from_cache' => $result->fromCache,
                'api_message' => $result->message,
            ]), $user, LogLevel::Notice);

            return back()
                ->withInput()
                ->withErrors(['pan' => $result->userMessage()]);
        }

        $panVerification->applyToUser(
            $user,
            $pan,
            $request->panName(),
            $request->dob(),
            $result->data,
            $request->aadhar(),
        );

        $user->refresh();

        if ($user->canCreatePaymentLinks()) {
            $paymentLinks->ensureDefaultForSeller($user);
        }

        $flow->kyc('kyc.submit.success', 'KYC completed via PAN verification', $flow->userContext($user, [
            'kyc_status' => User::KYC_ACTIVE,
            'from_cache' => $result->fromCache,
            'pan_type' => $user->pan_type,
        ]), $user);

        $status = $user->canCreatePaymentLinks()
            ? 'KYC verified. Your default payment link is ready on the dashboard — share it or download a QR code.'
            : 'KYC verified successfully. You can now receive payouts and manage bank accounts.';

        return redirect()
            ->route('dashboard')
            ->with('status', $status);
    }
}
