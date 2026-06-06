@extends('layouts.app')

@section('title', 'Profile — '.config('app.name'))

@section('content')
    @php
        $k = (int) $user->kyc_status;
        $kycPillHero = match ($k) {
            \App\Models\User::KYC_ACTIVE => 'bg-emerald-400/20 text-emerald-50 ring-1 ring-emerald-300/40',
            \App\Models\User::KYC_INACTIVE => 'bg-amber-400/20 text-amber-50 ring-1 ring-amber-300/40',
            \App\Models\User::KYC_INCOMPLETE => 'bg-rose-400/20 text-rose-50 ring-1 ring-rose-300/40',
            default => 'bg-white/15 text-white ring-1 ring-white/25',
        };
        $kycPillCard = match ($k) {
            \App\Models\User::KYC_ACTIVE => 'bg-emerald-100 text-emerald-900 ring-1 ring-emerald-400/40',
            \App\Models\User::KYC_INACTIVE => 'bg-amber-100 text-amber-900 ring-1 ring-amber-400/45',
            \App\Models\User::KYC_INCOMPLETE => 'bg-rose-100 text-rose-900 ring-1 ring-rose-400/45',
            default => 'bg-slate-100 text-slate-800 ring-1 ring-slate-300/60',
        };
        if ($user->hasSkippedKyc()) {
            $kycPillHero = 'bg-amber-400/20 text-amber-50 ring-1 ring-amber-300/40';
            $kycPillCard = 'bg-amber-100 text-amber-900 ring-1 ring-amber-400/45';
        }
        $kycHint = $user->hasSkippedKyc()
            ? 'Skipped for now — you can pay and explore. Complete KYC to receive bank payouts.'
            : match ($k) {
                \App\Models\User::KYC_ACTIVE => 'Verified — you can pay and receive settlements to your bank.',
                \App\Models\User::KYC_INACTIVE => 'KYC is not active. Contact support if this looks wrong.',
                \App\Models\User::KYC_INCOMPLETE => 'Complete KYC to receive payouts, or skip from the KYC page to pay only.',
                default => 'KYC status unknown.',
            };
        $maskedAadhar = is_string($user->aadhar) && strlen($user->aadhar) === 12
            ? str_repeat('•', 8).substr($user->aadhar, -4)
            : ($user->aadhar ? '••••' : '—');
    @endphp

    <div class="overflow-hidden rounded-3xl border border-indigo-200/80 bg-gradient-to-br from-slate-900 via-indigo-700 to-violet-700 shadow-xl shadow-indigo-900/25">
        <div class="relative px-6 py-8 text-white sm:px-8 sm:py-10">
            <div class="pointer-events-none absolute -left-10 bottom-0 h-48 w-48 rounded-full bg-cyan-400/15 blur-3xl" aria-hidden="true"></div>
            <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="flex items-start gap-4">
                    <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/15 ring-1 ring-white/30" aria-hidden="true">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                    </span>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-white/70">Account</p>
                        <h1 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">{{ $user->name }}</h1>
                        <p class="mt-2 text-sm text-white/80">{{ $user->email }}</p>
                        <div class="mt-4 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-bold {{ $kycPillHero }}">
                                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-current opacity-80" aria-hidden="true"></span>
                                KYC {{ $user->kyc_status_label }}
                            </span>
                            <span class="text-xs text-white/70">Code <span class="font-mono font-semibold text-white">{{ $user->user_code }}</span></span>
                        </div>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/25 bg-white/10 px-4 py-2.5 text-sm font-bold text-white backdrop-blur transition hover:bg-white/15">Dashboard</a>
                    @if (! $user->hasActiveKyc())
                        <a href="{{ route('kyc.index') }}" class="inline-flex items-center justify-center rounded-2xl bg-white px-5 py-2.5 text-sm font-bold text-indigo-700 shadow-lg shadow-black/15 transition hover:bg-violet-50">{{ $user->hasSkippedKyc() ? 'Complete KYC' : 'Start KYC' }}</a>
                    @endif
                    @if ($user->canUsePlatform())
                        <a href="{{ route('payments.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-emerald-400 px-5 py-2.5 text-sm font-bold text-emerald-950 shadow-lg shadow-black/15 transition hover:bg-emerald-300">Pay now</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="overflow-hidden rounded-3xl border border-slate-200/90 bg-white shadow-lg ring-1 ring-slate-900/5">
                <div class="border-b border-slate-100 bg-gradient-to-r from-indigo-50/80 via-white to-violet-50/60 px-6 py-4 sm:px-8">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-600">Identity &amp; KYC</h2>
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $kycPillCard }}">{{ $user->kyc_status_label }}</span>
                    </div>
                    <p class="mt-2 text-sm text-slate-600">{{ $kycHint }}</p>
                </div>
                <dl class="grid gap-px bg-slate-100 sm:grid-cols-2">
                    <div class="flex items-start gap-4 bg-white p-5 sm:p-6">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700" aria-hidden="true">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
                        </span>
                        <div class="min-w-0">
                            <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">PAN</dt>
                            <dd class="mt-1 font-mono text-sm font-bold text-slate-900">{{ $user->pan ?? '—' }}</dd>
                            @if ($user->pan_name)
                                <dd class="mt-1 text-xs text-slate-600">{{ $user->pan_name }}</dd>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-start gap-4 bg-white p-5 sm:p-6">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-100 text-violet-700" aria-hidden="true">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75A2.25 2.25 0 014.5 4.5h15a2.25 2.25 0 012.25 2.25v10.5A2.25 2.25 0 0119.5 19.5h-15a2.25 2.25 0 01-2.25-2.25V6.75z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 9h12M6 12h6"/></svg>
                        </span>
                        <div>
                            <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Aadhaar</dt>
                            <dd class="mt-1 font-mono text-sm font-bold text-slate-900">{{ $maskedAadhar }}</dd>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 bg-white p-5 sm:p-6">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600" aria-hidden="true">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-9 18.75h10.5a2.25 2.25 0 002.25-2.25V6.75m-15 12.75v-9"/></svg>
                        </span>
                        <div>
                            <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Phone</dt>
                            <dd class="mt-1 text-sm font-bold text-slate-900">{{ $user->phone ?? '—' }}</dd>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 bg-white p-5 sm:p-6 sm:col-span-2">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700" aria-hidden="true">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                        </span>
                        <div class="min-w-0">
                            <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Email</dt>
                            <dd class="mt-1 break-all text-sm font-bold text-slate-900">{{ $user->email }}</dd>
                        </div>
                    </div>
                </dl>
            </div>
        </div>

        <div class="space-y-4">
            @if ($user->hasActiveKyc())
                <a href="{{ route('settings.payment') }}" class="group relative block overflow-hidden rounded-2xl border border-emerald-200/80 bg-gradient-to-br from-emerald-50 via-white to-teal-50 p-6 shadow-md ring-1 ring-emerald-900/5 transition hover:border-emerald-300 hover:shadow-lg">
                    <div class="flex items-center gap-4">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-600 text-white shadow-md" aria-hidden="true">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="font-bold text-slate-900 group-hover:text-emerald-700">Payment settings</p>
                            <p class="mt-0.5 text-xs text-slate-600">Who can pay you · receive limits</p>
                        </div>
                        <span class="text-lg font-bold text-emerald-400 transition group-hover:text-emerald-600" aria-hidden="true">→</span>
                    </div>
                </a>
            @endif

            @if ($user->canCreatePaymentLinks())
                <a href="{{ route('payment-links.index') }}" class="group relative block overflow-hidden rounded-2xl border border-fuchsia-200/80 bg-gradient-to-br from-fuchsia-50 via-white to-indigo-50 p-6 shadow-md ring-1 ring-fuchsia-900/5 transition hover:border-fuchsia-300 hover:shadow-lg">
                    <div class="flex items-center gap-4">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-fuchsia-600 text-white shadow-md" aria-hidden="true">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/></svg>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="font-bold text-slate-900 group-hover:text-fuchsia-700">Payment links</p>
                            <p class="mt-0.5 text-xs text-slate-600">Create shareable links — clients pay via UPI, card, or net banking.</p>
                        </div>
                        <span class="text-lg font-bold text-fuchsia-400 transition group-hover:text-fuchsia-600" aria-hidden="true">→</span>
                    </div>
                </a>
            @endif

            <a href="{{ $user->canReceivePayouts() ? route('account.transactions') : route('kyc.index') }}" class="group relative block overflow-hidden rounded-2xl border border-indigo-200/80 bg-gradient-to-br from-indigo-50 via-white to-violet-50 p-6 shadow-md ring-1 ring-indigo-900/5 transition hover:border-indigo-300 hover:shadow-lg {{ $user->canReceivePayouts() ? '' : 'opacity-90' }}">
                <div class="flex items-center gap-4">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-md" aria-hidden="true">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75z"/></svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="font-bold text-slate-900 group-hover:text-indigo-700">Transactions</p>
                        <p class="mt-0.5 text-xs text-slate-600">{{ $user->canReceivePayouts() ? 'Payment and bank settlement history.' : 'Requires KYC to receive payouts.' }}</p>
                    </div>
                    <span class="text-lg font-bold text-indigo-400 transition group-hover:text-indigo-600" aria-hidden="true">→</span>
                </div>
            </a>

            <a href="{{ $user->canReceivePayouts() ? route('banks.index') : route('kyc.index') }}" class="group relative block overflow-hidden rounded-2xl border border-violet-200/80 bg-gradient-to-br from-violet-50 via-white to-fuchsia-50 p-6 shadow-md ring-1 ring-violet-900/5 transition hover:border-violet-300 hover:shadow-lg {{ $user->canReceivePayouts() ? '' : 'opacity-90' }}">
                <div class="flex items-center gap-4">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-600 text-white shadow-md" aria-hidden="true">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-4h6v4"/></svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="font-bold text-slate-900 group-hover:text-violet-700">Bank accounts</p>
                        <p class="mt-0.5 text-xs text-slate-600">{{ $user->canReceivePayouts() ? ((int) $user->banks_count).' saved · add or edit payout accounts.' : 'Complete KYC to add payout bank accounts.' }}</p>
                    </div>
                    <span class="text-lg font-bold text-violet-400 transition group-hover:text-violet-600" aria-hidden="true">→</span>
                </div>
            </a>

            <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-5 text-xs leading-relaxed text-slate-600">
                <p class="font-semibold text-slate-800">Privacy</p>
                <p class="mt-1">Sensitive values are masked here. Full numbers are stored securely for verification only.</p>
            </div>
        </div>
    </div>
@endsection
