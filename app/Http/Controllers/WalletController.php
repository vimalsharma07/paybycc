<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateWalletRequest;
use App\Models\Wallet;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WalletController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            [
                'balance' => 0,
                'auto_settle_to_bank' => true,
                'default_bank_id' => null,
            ]
        );

        $transactions = $user->transactions()
            ->with(['bank:id,bank_name', 'payment:id,amount'])
            ->latest()
            ->paginate(20);

        $banks = $user->banks()
            ->where('status', 'active')
            ->orderByDesc('is_primary')
            ->orderBy('bank_name')
            ->get();

        return view('wallet.index', compact('wallet', 'transactions', 'banks'));
    }

    public function update(UpdateWalletRequest $request): RedirectResponse
    {
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $request->user()->id],
            [
                'balance' => 0,
                'auto_settle_to_bank' => true,
                'default_bank_id' => null,
            ]
        );

        $data = $request->validated();
        if (($data['default_bank_id'] ?? null) === '' || $data['default_bank_id'] === null) {
            $data['default_bank_id'] = null;
        }

        $wallet->update($data);

        return back()->with('status', 'Wallet settings saved.');
    }
}
