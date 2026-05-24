<?php

namespace App\Http\Controllers;

use App\Enums\LogLevel;
use App\Models\User;
use App\Services\Logging\FlowLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
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
            $flow->kyc('kyc.index.already_complete', 'KYC already complete — sent to dashboard', $flow->userContext($user), $user);

            return redirect()->route('dashboard');
        }

        $flow->kyc('kyc.index.view', 'KYC form opened', $flow->userContext($user, [
            'has_pan' => $user->pan !== null,
            'has_aadhar' => $user->aadhar !== null,
        ]), $user);

        return view('kyc.index', ['user' => $user]);
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

        try {
            $validated = $request->validate([
                'pan' => ['required', 'string', 'size:10', 'regex:/^[A-Za-z]{5}[0-9]{4}[A-Za-z]{1}$/'],
                'pan_name' => ['required', 'string', 'max:255'],
                'aadhar' => ['nullable', 'string', 'size:12', 'regex:/^\d{12}$/'],
            ]);
        } catch (ValidationException $e) {
            $flow->kyc('kyc.submit.validation_failed', 'KYC validation failed', array_merge(
                $flow->validationErrors($e),
                $flow->userContext($user)
            ), $user, LogLevel::Notice);

            throw $e;
        }

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
        $user->save();

        $flow->kyc('kyc.submit.success', 'KYC completed', $flow->userContext($user, [
            'kyc_status' => User::KYC_ACTIVE,
        ]), $user);

        return redirect()->route('dashboard')->with('status', 'KYC completed successfully.');
    }
}
