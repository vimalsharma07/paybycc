@extends('layouts.app')

@section('title', 'Dashboard — '.config('app.name'))
@section('page_heading', 'Overview')
@section('page_subheading', 'Accept UPI, cards & more — track payouts to your bank')

@section('content')
    @php
        $kycSnap = match ((int) $user->kyc_status) {
            \App\Models\User::KYC_ACTIVE => 'bg-emerald-100 text-emerald-900 ring-emerald-400/40',
            \App\Models\User::KYC_INACTIVE => 'bg-amber-100 text-amber-900 ring-amber-400/45',
            default => 'bg-rose-100 text-rose-900 ring-rose-400/45',
        };
    @endphp

    <div class="overflow-hidden rounded-3xl border border-indigo-200/80 bg-gradient-to-br from-indigo-600 via-violet-600 to-slate-900 p-6 text-white shadow-xl shadow-indigo-900/25 sm:p-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-white/70">Welcome back</p>
                <h2 class="mt-1 text-2xl font-extrabold tracking-tight sm:text-3xl">{{ \Illuminate\Support\Str::limit($user->name, 32) }}</h2>
                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1 {{ $kycSnap }}">{{ $user->kyc_status_label }}</span>
                    <span class="font-mono text-xs text-white/80">{{ $user->user_code }}</span>
                </div>
            </div>
            @if ($user->canReceivePayouts())
                <div class="rounded-2xl bg-white/15 px-5 py-4 ring-1 ring-white/25 backdrop-blur-sm">
                    <p class="text-xs font-bold uppercase text-white/70">You accept</p>
                    <p class="mt-1 text-sm text-white/90">UPI · Cards · Net banking · Wallets</p>
                    <p class="mt-2 text-xs text-white/75">Settlements go straight to your bank.</p>
                    <a href="{{ route('banks.index') }}" class="mt-2 inline-flex text-xs font-bold text-cyan-200 hover:underline">Manage banks →</a>
                </div>
            @endif
        </div>
    </div>

    @if ($user->hasSkippedKyc())
        <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-950">
            <p class="font-semibold">KYC skipped — you can pay and explore; bank payouts need verification.</p>
            <a href="{{ route('kyc.index') }}" class="mt-2 inline-flex font-bold text-amber-900 underline">Complete KYC →</a>
        </div>
    @endif

    @if ($user->canUsePlatform())
        <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <a href="{{ route('account.payments') }}" class="app-stat-card group rounded-2xl border border-slate-200/90 bg-white p-5 shadow-sm ring-1 ring-slate-900/5 transition hover:border-indigo-200 hover:shadow-md">
                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Payments sent</p>
                <p class="mt-2 text-3xl font-bold tabular-nums text-slate-900">{{ $stats['payments_sent'] }}</p>
                <p class="mt-1 text-xs text-emerald-700">{{ $stats['payments_completed'] }} completed</p>
            </a>
            @if ($user->isSeller())
                <a href="{{ route('account.settlements') }}" class="app-stat-card group rounded-2xl border border-slate-200/90 bg-white p-5 shadow-sm ring-1 ring-slate-900/5 transition hover:border-emerald-200 hover:shadow-md">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Received (net)</p>
                    <p class="mt-2 font-mono text-2xl font-bold tabular-nums text-emerald-800">₹{{ number_format($stats['received_paid'], 0) }}</p>
                    <p class="mt-1 text-xs text-slate-600">{{ $stats['orders_received'] }} orders</p>
                </a>
            @endif
            @if ($user->canReceivePayouts())
                <a href="{{ route('account.transactions') }}" class="app-stat-card group rounded-2xl border border-slate-200/90 bg-white p-5 shadow-sm ring-1 ring-slate-900/5 transition hover:border-violet-200 hover:shadow-md">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Transactions</p>
                    <p class="mt-2 text-3xl font-bold text-indigo-800">{{ $recentTransactions->count() > 0 ? 'Active' : '—' }}</p>
                    <p class="mt-1 text-xs font-bold text-indigo-600 group-hover:underline">Payment history →</p>
                </a>
                <a href="{{ route('banks.index') }}" class="app-stat-card group rounded-2xl border border-slate-200/90 bg-white p-5 shadow-sm ring-1 ring-slate-900/5 transition hover:border-violet-200 hover:shadow-md">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Bank accounts</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ (int) $user->banks_count }}</p>
                    <p class="mt-1 text-xs font-bold text-violet-600 group-hover:underline">Auto payout enabled</p>
                </a>
            @else
                <a href="{{ route('marketplace.index') }}" class="app-stat-card rounded-2xl border border-indigo-200/80 bg-gradient-to-br from-indigo-50 to-violet-50 p-5 shadow-sm sm:col-span-2">
                    <p class="font-bold text-slate-900">Find freelancers</p>
                    <p class="mt-1 text-sm text-slate-600">Pay with UPI, card, or net banking</p>
                </a>
                <a href="{{ route('kyc.index') }}" class="app-stat-card rounded-2xl border border-amber-200/80 bg-amber-50 p-5 shadow-sm">
                    <p class="font-bold text-amber-950">Complete KYC</p>
                    <p class="mt-1 text-sm text-amber-900/90">Unlock bank payouts</p>
                </a>
            @endif
        </div>
    @endif

    <div class="mt-8 grid gap-6 xl:grid-cols-2">
        @if ($user->canUsePlatform() && $recentPayments->isNotEmpty())
            <section class="overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-lg ring-1 ring-slate-900/5">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 sm:px-6">
                    <h3 class="text-sm font-bold text-slate-900">Recent payments</h3>
                    <a href="{{ route('account.payments') }}" class="text-xs font-bold text-indigo-600 hover:underline">View all</a>
                </div>
                <ul class="divide-y divide-slate-100">
                    @foreach ($recentPayments as $payment)
                        <li class="flex items-center justify-between gap-3 px-5 py-3.5 sm:px-6">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-900">{{ $payment->order?->freelancer?->name ?? 'Payment' }}</p>
                                <p class="text-xs text-slate-500">{{ $payment->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="font-mono text-sm font-bold">₹{{ number_format((float) $payment->amount, 2) }}</p>
                                <x-payment-status-pill :status="$payment->status" class="mt-1" />
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if ($user->isSeller() && $recentOrdersReceived->isNotEmpty())
            <section class="overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-lg ring-1 ring-slate-900/5">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 sm:px-6">
                    <h3 class="text-sm font-bold text-slate-900">Recently received</h3>
                    <a href="{{ route('account.settlements') }}" class="text-xs font-bold text-indigo-600 hover:underline">View all</a>
                </div>
                <ul class="divide-y divide-slate-100">
                    @foreach ($recentOrdersReceived as $order)
                        <li class="flex items-center justify-between gap-3 px-5 py-3.5 sm:px-6">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $order->customer?->name }}</p>
                                <p class="font-mono text-xs text-indigo-700">{{ $order->order_code }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-mono text-sm font-bold text-emerald-800">₹{{ number_format((float) $order->net_settlement_amount, 2) }}</p>
                                <x-payment-status-pill :status="$order->payment_status" class="mt-1" />
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if ($user->canReceivePayouts() && $recentTransactions->isNotEmpty())
            <section class="overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-lg ring-1 ring-slate-900/5 xl:col-span-2">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 sm:px-6">
                    <h3 class="text-sm font-bold text-slate-900">Recent transactions</h3>
                    <a href="{{ route('account.transactions') }}" class="text-xs font-bold text-indigo-600 hover:underline">View all</a>
                </div>
                <ul class="divide-y divide-slate-100 sm:grid sm:grid-cols-2 sm:divide-x sm:divide-y-0">
                    @foreach ($recentTransactions as $tx)
                        <li class="flex items-center justify-between gap-3 px-5 py-3.5 sm:px-6">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $tx->type_label }}</p>
                                <p class="text-xs text-slate-500">{{ $tx->created_at->format('M j, H:i') }}</p>
                            </div>
                            <p class="font-mono text-sm font-bold">₹{{ number_format((float) $tx->amount, 2) }}</p>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif
    </div>

    @if ($user->canUsePlatform() && $recentPayments->isEmpty() && (! $user->isSeller() || $recentOrdersReceived->isEmpty()))
        <div class="mt-8 rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center">
            <p class="text-lg font-bold text-slate-900">Start on the marketplace</p>
            <p class="mt-2 text-sm text-slate-600">Pay a freelancer any way you like — or complete KYC to start accepting payments to your bank.</p>
            <div class="mt-6 flex flex-wrap justify-center gap-3">
                <a href="{{ route('marketplace.index') }}" class="rounded-2xl border border-slate-200 px-5 py-2.5 text-sm font-bold text-slate-800 hover:bg-slate-50">Explore</a>
                <a href="{{ route('payments.create') }}" class="rounded-2xl bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white shadow-md hover:bg-indigo-500">Pay now</a>
            </div>
        </div>
    @endif
@endsection
