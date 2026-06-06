<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentSettingsRequest;
use App\Services\Payments\SellerReceiveLimitService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentSettingsController extends Controller
{
    public function __construct(
        protected SellerReceiveLimitService $receiveLimits,
    ) {}

    public function edit(): View|RedirectResponse
    {
        $user = request()->user();

        if (! $user->hasActiveKyc()) {
            return redirect()
                ->route('kyc.index')
                ->with('status', 'Complete KYC to manage payment settings.');
        }

        return view('settings.payment', [
            'user' => $user,
            'limits' => $this->receiveLimits->snapshot($user),
            'payerMode' => $user->paymentLinkPayerMode(),
        ]);
    }

    public function update(StorePaymentSettingsRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->applyPaymentLinkPayerMode($request->input('payer_mode'));
        $user->save();

        return redirect()
            ->route('settings.payment')
            ->with('status', 'Payment settings saved.');
    }
}
