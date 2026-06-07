<?php

namespace App\Http\Controllers;

use App\Constants\OrderStatuses;
use App\Models\Order;
use App\Models\Payment;
use App\Services\PaymentLinks\PaymentLinkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected PaymentLinkService $paymentLinks,
    ) {}

    public function index(): View|RedirectResponse
    {
        $user = auth()->user();

        if ($user->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        $user->loadCount('banks');

        $stats = [
            'payments_sent' => $user->canUsePlatform()
                ? Payment::query()->where('user_id', $user->id)->count()
                : 0,
            'payments_completed' => $user->canUsePlatform()
                ? Payment::query()->where('user_id', $user->id)->where('status', 'completed')->count()
                : 0,
            'orders_received' => $user->isSeller()
                ? Order::query()->where('freelancer_id', $user->id)->count()
                : 0,
            'received_paid' => $user->isSeller()
                ? (float) Order::query()->where('freelancer_id', $user->id)->where('payment_status', OrderStatuses::PAYMENT_PAID)->sum('net_settlement_amount')
                : 0.0,
        ];

        $recentPayments = $user->canUsePlatform()
            ? Payment::query()
                ->where('user_id', $user->id)
                ->with(['gateway:id,name', 'order.freelancer:id,name'])
                ->latest()
                ->limit(5)
                ->get()
            : collect();

        $recentTransactions = $user->canReceivePayouts()
            ? $user->transactions()->latest()->limit(5)->get()
            : collect();

        $recentOrdersReceived = $user->isSeller()
            ? Order::query()
                ->where('freelancer_id', $user->id)
                ->with('customer:id,name')
                ->latest()
                ->limit(5)
                ->get()
            : collect();

        $defaultPaymentLink = null;
        if ($user->canCreatePaymentLinks()) {
            $defaultPaymentLink = $this->paymentLinks->ensureDefaultForSeller($user);
        }

        return view('dashboard', [
            'user' => $user,
            'stats' => $stats,
            'recentPayments' => $recentPayments,
            'recentTransactions' => $recentTransactions,
            'recentOrdersReceived' => $recentOrdersReceived,
            'defaultPaymentLink' => $defaultPaymentLink,
        ]);
    }
}
