<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Settlement;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function payments(): View
    {
        $user = auth()->user();

        $payments = Payment::query()
            ->where('user_id', $user->id)
            ->with([
                'gateway:id,name,code',
                'order:id,order_code,freelancer_id,order_amount,net_settlement_amount',
                'order.freelancer:id,name,user_code',
            ])
            ->latest()
            ->paginate(15);

        return view('account.payments', compact('payments'));
    }

    public function settlements(): View
    {
        $user = auth()->user();

        $ordersReceived = Order::query()
            ->where('freelancer_id', $user->id)
            ->with(['customer:id,name,user_code'])
            ->latest()
            ->paginate(10, ['*'], 'orders_page');

        $settlements = Settlement::query()
            ->where('freelancer_id', $user->id)
            ->with(['order:id,order_code', 'bank:id,bank_name'])
            ->latest()
            ->paginate(10, ['*'], 'settlements_page');

        $receivedTotal = (float) Order::query()
            ->where('freelancer_id', $user->id)
            ->where('payment_status', 'paid')
            ->sum('net_settlement_amount');

        return view('account.settlements', compact('ordersReceived', 'settlements', 'receivedTotal'));
    }

    public function transactions(): View
    {
        $user = auth()->user();

        $transactions = $user->transactions()
            ->with(['bank:id,bank_name', 'payment:id,amount,remark'])
            ->latest()
            ->paginate(20);

        return view('account.transactions', compact('transactions'));
    }
}
