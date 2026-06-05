<?php

namespace App\Services\Wallet;

use App\Models\User;
use App\Models\Wallet;

/**
 * Internal wallet ledger — not exposed in the customer UI.
 * All users are provisioned with auto_settle_to_bank enabled.
 */
class WalletService
{
    public function ensureForUser(User|int $user): Wallet
    {
        $userId = $user instanceof User ? $user->id : $user;

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $userId],
            [
                'balance' => 0,
                'auto_settle_to_bank' => true,
                'default_bank_id' => null,
            ]
        );

        if (! $wallet->auto_settle_to_bank) {
            $wallet->forceFill(['auto_settle_to_bank' => true])->save();
        }

        if ($user instanceof User) {
            $this->syncDefaultBankFromPrimary($user, $wallet);
        } else {
            $loaded = User::query()->find($userId);
            if ($loaded) {
                $this->syncDefaultBankFromPrimary($loaded, $wallet);
            }
        }

        return $wallet->fresh();
    }

    public function syncDefaultBankFromPrimary(User $user, ?Wallet $wallet = null): void
    {
        $wallet ??= $this->ensureForUser($user);

        $primaryBank = $user->banks()
            ->where('status', 'active')
            ->where('is_primary', true)
            ->orderByDesc('updated_at')
            ->first();

        if (! $primaryBank) {
            return;
        }

        if ((int) $wallet->default_bank_id !== (int) $primaryBank->id) {
            $wallet->forceFill([
                'default_bank_id' => $primaryBank->id,
                'auto_settle_to_bank' => true,
            ])->save();
        }
    }

    public function setDefaultBank(User $user, ?int $bankId): void
    {
        $wallet = $this->ensureForUser($user);

        $wallet->forceFill([
            'default_bank_id' => $bankId,
            'auto_settle_to_bank' => true,
        ])->save();
    }
}
