<?php

namespace App\Support;

use App\Models\User;

final class AppNavigation
{
    /**
     * @return list<array{href: string, label: string, icon: string, active: bool, badge?: string|null}>
     */
    public static function main(User $user): array
    {
        $canPay = $user->canUsePlatform();
        $canPayout = $user->canReceivePayouts();
        $isSeller = $user->isSeller();

        $items = [
            self::item('dashboard', 'Overview', 'home', ['dashboard']),
        ];

        if ($canPay) {
            $items[] = self::item('marketplace.index', 'Marketplace', 'grid', ['marketplace.*']);
            $items[] = self::item('account.payments', 'Payments sent', 'card', ['account.payments']);
            if ($isSeller) {
                $items[] = self::item('account.settlements', 'Received & settlements', 'inbox', ['account.settlements']);
            }
        }

        if ($canPayout) {
            $items[] = self::item('account.transactions', 'Transaction history', 'chart', ['account.transactions']);
            $items[] = self::item('wallet.index', 'Wallet', 'wallet', ['wallet.*']);
            $items[] = self::item('banks.index', 'Bank accounts', 'bank', ['banks.*']);
        } elseif ($canPay) {
            $items[] = self::item('kyc.index', 'Complete KYC', 'shield', ['kyc.*']);
        }

        $items[] = self::item('profile.show', 'Profile', 'user', ['profile.*']);

        return $items;
    }

    /**
     * @param list<string> $routePatterns
     * @return array{href: string, label: string, icon: string, active: bool}
     */
    protected static function item(string $routeName, string $label, string $icon, array $routePatterns): array
    {
        $active = false;
        foreach ($routePatterns as $pattern) {
            if (request()->routeIs($pattern)) {
                $active = true;
                break;
            }
        }

        return [
            'href' => route($routeName),
            'label' => $label,
            'icon' => $icon,
            'active' => $active,
        ];
    }
}
