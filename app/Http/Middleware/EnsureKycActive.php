<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureKycActive
{
    /** Wallet, banks, and receiving settlements require completed KYC. */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user->is_admin || $user->hasActiveKyc()) {
            return $next($request);
        }

        return redirect()
            ->route('kyc.index')
            ->with('status', 'Complete KYC to receive payments and manage bank payout accounts.');
    }
}
