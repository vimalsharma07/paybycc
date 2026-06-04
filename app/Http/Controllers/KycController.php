<?php

namespace App\Http\Controllers;

use App\Enums\LogLevel;
use App\Models\User;
use App\Services\Logging\FlowLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function skip(Request $request, FlowLog $flow): RedirectResponse
    {
        $user = $request->user();

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

    public function storePan(Request $request, FlowLog $flow): RedirectResponse
    {
        $user = auth()->user();

        if ($user->is_admin || $user->hasActiveKyc()) {
            $flow->kyc('kyc.submit.blocked', 'KYC submit blocked — not applicable', $flow->userContext($user, [
                'is_admin' => $user->is_admin,
                'kyc_active' => $user->hasActiveKyc(),
            ]), $user, LogLevel::Notice);

            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'pan' => ['required', 'string', 'size:10', 'regex:/^[A-Za-z]{5}[0-9]{4}[A-Za-z]{1}$/'],
            'pan_name' => ['required', 'string', 'max:255'],
            'aadhar' => ['nullable', 'string', 'size:12', 'regex:/^\d{12}$/'],
        ]);

        $flow->kyc('kyc.submit.attempt', 'KYC submit', array_merge(
            $flow->userContext($user),
            $flow->maskedPan(strtoupper($validated['pan'])),
            $flow->maskedAadhar($validated['aadhar'] ?? null),
            ['pan_name' => $validated['pan_name'], 'has_aadhar' => ! empty($validated['aadhar'])]
        ), $user);

        $user->pan = strtoupper($validated['pan']);
        $user->pan_name = $validated['pan_name'];
        if (! empty($validated['aadhar'])) {
            $user->aadhar = $validated['aadhar'];
        }
        $user->kyc_status = User::KYC_ACTIVE;
        $user->kyc_skipped_at = null;
        $user->save();

        $flow->kyc('kyc.submit.success', 'KYC completed', $flow->userContext($user, [
            'kyc_status' => User::KYC_ACTIVE,
        ]), $user);

        return redirect()
            ->route('dashboard')
            ->with('status', 'KYC completed successfully. You can now receive payouts and manage bank accounts.');
    }
}
