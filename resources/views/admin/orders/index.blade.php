@extends('layouts.admin')

@section('title', 'Orders — '.config('app.name'))

@section('content')
    <div class="mb-6 overflow-hidden rounded-2xl border border-indigo-200/70 bg-gradient-to-br from-white via-indigo-50/40 to-violet-50/50 p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Orders</h1>
                <p class="mt-1 max-w-xl text-sm text-slate-600">Customer → freelancer payments. Newest first.</p>
            </div>
            <form method="GET" action="{{ route('admin.orders.index') }}" class="flex w-full max-w-3xl flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center">
                <input name="q" type="search" value="{{ $q }}" placeholder="Order code, user, email…"
                    class="block min-w-0 flex-1 rounded-xl border border-indigo-200/80 bg-white/90 px-3 py-2.5 text-sm shadow-inner focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-400/30">
                <select name="payment_status" class="rounded-xl border border-indigo-200/80 bg-white px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-400/30">
                    <option value="">All payments</option>
                    @foreach ([
                        \App\Constants\OrderStatuses::PAYMENT_PENDING,
                        \App\Constants\OrderStatuses::PAYMENT_AUTHORIZED,
                        \App\Constants\OrderStatuses::PAYMENT_PAID,
                        \App\Constants\OrderStatuses::PAYMENT_FAILED,
                        \App\Constants\OrderStatuses::PAYMENT_REFUNDED,
                    ] as $st)
                        <option value="{{ $st }}" @selected($paymentStatus === $st)>Payment: {{ \App\Constants\OrderStatuses::paymentLabel($st) }}</option>
                    @endforeach
                </select>
                <select name="order_status" class="rounded-xl border border-indigo-200/80 bg-white px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-400/30">
                    <option value="">All order status</option>
                    @foreach ([
                        \App\Constants\OrderStatuses::ORDER_CREATED,
                        \App\Constants\OrderStatuses::ORDER_PENDING,
                        \App\Constants\OrderStatuses::ORDER_ACCEPTED,
                        \App\Constants\OrderStatuses::ORDER_IN_PROGRESS,
                        \App\Constants\OrderStatuses::ORDER_COMPLETED,
                        \App\Constants\OrderStatuses::ORDER_CANCELLED,
                        \App\Constants\OrderStatuses::ORDER_DISPUTED,
                    ] as $st)
                        <option value="{{ $st }}" @selected($orderStatus === $st)>Order: {{ \App\Constants\OrderStatuses::orderLabel($st) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="shrink-0 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Filter</button>
            </form>
        </div>
    </div>

    <div class="w-full overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-md ring-1 ring-slate-900/5">
        <div class="w-full min-w-0 overflow-x-auto">
            <table class="w-full min-w-[64rem] border-collapse divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-gradient-to-r from-slate-100 via-indigo-50/80 to-violet-50/90 text-xs font-semibold uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">Order</th>
                        <th class="px-4 py-3 sm:px-6">Customer</th>
                        <th class="px-4 py-3 sm:px-6">Freelancer</th>
                        <th class="whitespace-nowrap px-4 py-3 text-right sm:px-6">Amount</th>
                        <th class="whitespace-nowrap px-4 py-3 text-right sm:px-6">Net settle</th>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">Order</th>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">Payment</th>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">Settlement</th>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6">Created</th>
                        <th class="whitespace-nowrap px-4 py-3 sm:px-6"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($orders as $order)
                        <tr class="transition-colors hover:bg-slate-50/90">
                            <td class="whitespace-nowrap px-4 py-4 sm:px-6">
                                <a href="{{ route('admin.orders.show', $order) }}" class="font-mono text-xs font-bold text-indigo-700 hover:underline">{{ $order->order_code }}</a>
                                <p class="text-xs text-slate-500">#{{ $order->id }}</p>
                            </td>
                            <td class="px-4 py-4 sm:px-6">
                                @if ($order->customer)
                                    <p class="font-medium text-slate-900">{{ $order->customer->name }}</p>
                                    <p class="text-xs text-slate-600">{{ $order->customer->email }}</p>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 sm:px-6">
                                @if ($order->freelancer)
                                    <p class="font-medium text-slate-900">{{ $order->freelancer->name }}</p>
                                    <p class="text-xs text-slate-600">{{ $order->freelancer->user_code }}</p>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-right font-mono tabular-nums sm:px-6">{{ $order->currency }} {{ number_format((float) $order->order_amount, 2) }}</td>
                            <td class="whitespace-nowrap px-4 py-4 text-right font-mono tabular-nums text-slate-700 sm:px-6">{{ number_format((float) $order->net_settlement_amount, 2) }}</td>
                            <td class="whitespace-nowrap px-4 py-4 sm:px-6"><x-admin-status-pill :status="$order->order_status" type="order" /></td>
                            <td class="whitespace-nowrap px-4 py-4 sm:px-6"><x-admin-status-pill :status="$order->payment_status" type="payment" /></td>
                            <td class="whitespace-nowrap px-4 py-4 sm:px-6"><x-admin-status-pill :status="$order->settlement_status" type="settlement" /></td>
                            <td class="whitespace-nowrap px-4 py-4 text-xs text-slate-600 sm:px-6">{{ $order->created_at?->format('d M Y H:i') }}</td>
                            <td class="whitespace-nowrap px-4 py-4 sm:px-6">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-sm text-slate-600">No orders yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($orders->hasPages())
            <div class="border-t border-slate-100 px-4 py-4 sm:px-6">{{ $orders->links() }}</div>
        @endif
    </div>
@endsection
