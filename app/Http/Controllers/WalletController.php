<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateWalletRequest;
use App\Services\Wallet\WalletService;
use Illuminate\Http\RedirectResponse;

/**
 * Wallet UI is hidden — routes redirect to bank accounts.
 * Backend wallet ledger remains active for settlements.
 */
class WalletController extends Controller
{
    public function __construct(
        protected WalletService $wallets,
    ) {}

    public function index(): RedirectResponse
    {
        return redirect()->route('banks.index');
    }

    public function update(UpdateWalletRequest $request): RedirectResponse
    {
        $bankId = $request->validated()['default_bank_id'] ?? null;
        $bankId = $bankId === '' || $bankId === null ? null : (int) $bankId;

        if ($bankId !== null) {
            abort_unless($request->user()->banks()->whereKey($bankId)->exists(), 422);
        }

        $this->wallets->setDefaultBank($request->user(), $bankId);

        return redirect()
            ->route('banks.index')
            ->with('status', 'Payout bank preference saved.');
    }
}
