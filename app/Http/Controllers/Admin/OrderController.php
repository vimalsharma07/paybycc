<?php

namespace App\Http\Controllers\Admin;

use App\Constants\OrderStatuses;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $paymentStatus = $request->query('payment_status');
        $paymentStatus = is_numeric($paymentStatus) ? (int) $paymentStatus : null;
        $orderStatus = $request->query('order_status');
        $orderStatus = is_numeric($orderStatus) ? (int) $orderStatus : null;

        $orders = Order::query()
            ->with([
                'customer:id,name,email,user_code',
                'freelancer:id,name,email,user_code,company_name',
            ])
            ->when($q !== '', function ($query) use ($q) {
                $like = '%'.$q.'%';
                $query->where(function ($query) use ($like) {
                    $query->where('order_code', 'like', $like)
                        ->orWhere('notes', 'like', $like)
                        ->orWhereHas('customer', fn ($q) => $q->where('name', 'like', $like)->orWhere('email', 'like', $like)->orWhere('user_code', 'like', $like))
                        ->orWhereHas('freelancer', fn ($q) => $q->where('name', 'like', $like)->orWhere('email', 'like', $like)->orWhere('user_code', 'like', $like)->orWhere('company_name', 'like', $like));
                });
            })
            ->when($paymentStatus !== null, fn ($query) => $query->where('payment_status', $paymentStatus))
            ->when($orderStatus !== null, fn ($query) => $query->where('order_status', $orderStatus))
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'q' => $q,
            'paymentStatus' => $paymentStatus,
            'orderStatus' => $orderStatus,
        ]);
    }

    public function show(Order $order): View
    {
        $order->load([
            'customer',
            'freelancer',
            'service',
            'subservice.service',
            'payments' => fn ($q) => $q->with('gateway:id,name,code')->orderByDesc('id'),
            'payments.transactions',
            'settlements' => fn ($q) => $q->with('bank:id,bank_name,account_holder_name')->orderByDesc('id'),
        ]);

        return view('admin.orders.show', compact('order'));
    }
}
