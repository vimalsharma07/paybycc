@extends('layouts.app')

@section('title', 'Received & settlements — '.config('app.name'))
@section('page_heading', 'Received & settlements')
@section('page_subheading', 'Payments received as a seller and payout status')

@section('content')
    <div class="mb-6 grid gap-4 sm:grid-cols-2">
        <div class="rounded-2xl border border-emerald-200/80 bg-gradient-to-br from-emerald-50 to-teal-50 p-5 shadow-sm ring-1 ring-emerald-900/5">
            <p class="text-xs font-bold uppercase tracking-wide text-emerald-800">Total received (net)</p>
            <p class="mt-2 font-mono text-3xl font-bold tabular-nums text-slate-900">₹{{ number_format($receivedTotal, 2) }}</p>
        </div>
        <div class="rounded-2xl border border-indigo-200/80 bg-gradient-to-br from-indigo-50 to-violet-50 p-5 shadow-sm ring-1 ring-indigo-900/5">
            <p class="text-xs font-bold uppercase tracking-wide text-indigo-800">Wallet balance</p>
            <p class="mt-2 font-mono text-3xl font-bold tabular-nums text-slate-900">₹{{ number_format((float) $wallet->balance, 2) }}</p>
            <a href="{{ route('wallet.index') }}" class="mt-3 inline-flex text-xs font-bold text-indigo-700 hover:underline">Wallet settings →</a>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-lg ring-1 ring-slate-900/5">
        <div class="border-b border-slate-100 bg-slate-50/80 px-6 py-4">
            <h2 class="text-sm font-bold uppercase tracking-wide text-slate-600">Orders paid to you</h2>
        </div>
        @if ($ordersReceived->isEmpty())
            <p class="px-6 py-10 text-center text-sm text-slate-600">No one has paid you through the marketplace yet.</p>
        @else
            <div class="divide-y divide-slate-100">
                @foreach ($ordersReceived as $order)
                    <div class="px-5 py-4 sm:px-6">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="font-mono text-sm font-bold text-indigo-700">{{ $order->order_code }}</p>
                                <p class="mt-1 text-sm text-slate-800">From {{ $order->customer?->name ?? 'Customer' }}</p>
                                <p class="mt-2 font-mono text-lg font-bold text-emerald-800">₹{{ number_format((float) $order->net_settlement_amount, 2) }} <span class="text-xs font-normal text-slate-500">net</span></p>
                            </div>
                            <div class="text-right text-xs">
                                <x-payment-status-pill :status="$order->payment_status" />
                                <p class="mt-2 font-semibold capitalize text-slate-600">Settlement: {{ $order->settlement_status }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @if ($ordersReceived->hasPages())
                <div class="border-t border-slate-100 px-4 py-3">{{ $ordersReceived->links() }}</div>
            @endif
        @endif
    </div>

    <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-lg ring-1 ring-slate-900/5">
        <div class="border-b border-slate-100 bg-slate-50/80 px-6 py-4">
            <h2 class="text-sm font-bold uppercase tracking-wide text-slate-600">Bank settlements</h2>
        </div>
        @if ($settlements->isEmpty())
            <p class="px-6 py-10 text-center text-sm text-slate-600">No bank settlement records yet.</p>
        @else
            <div class="divide-y divide-slate-100">
                @foreach ($settlements as $settlement)
                    <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 sm:px-6">
                        <div>
                            <p class="font-mono text-sm font-semibold text-slate-900">₹{{ number_format((float) $settlement->amount, 2) }}</p>
                            <p class="mt-1 text-xs text-slate-600">{{ $settlement->order?->order_code }} · {{ $settlement->bank?->bank_name ?? 'Wallet' }}</p>
                        </div>
                        <x-payment-status-pill :status="$settlement->status" />
                    </div>
                @endforeach
            </div>
            @if ($settlements->hasPages())
                <div class="border-t border-slate-100 px-4 py-3">{{ $settlements->links() }}</div>
            @endif
        @endif
    </div>
@endsection
