@extends('layouts.app')

@section('title', 'Dashboard — '.config('app.name'))

@section('content')
    @php
        $k = (int) $user->kyc_status;
        $kycPill = match ($k) {
            \App\Models\User::KYC_ACTIVE => 'bg-emerald-400/20 text-emerald-50 ring-1 ring-emerald-300/40',
            \App\Models\User::KYC_INACTIVE => 'bg-amber-400/20 text-amber-50 ring-1 ring-amber-300/40',
            \App\Models\User::KYC_INCOMPLETE => 'bg-rose-400/20 text-rose-50 ring-1 ring-rose-300/40',
            default => 'bg-white/15 text-white ring-1 ring-white/25',
        };
        $kycSnap = match ($k) {
            \App\Models\User::KYC_ACTIVE => 'bg-emerald-100 text-emerald-900 ring-1 ring-emerald-400/40',
            \App\Models\User::KYC_INACTIVE => 'bg-amber-100 text-amber-900 ring-1 ring-amber-400/45',
            \App\Models\User::KYC_INCOMPLETE => 'bg-rose-100 text-rose-900 ring-1 ring-rose-400/45',
            default => 'bg-slate-100 text-slate-800 ring-1 ring-slate-300/60',
        };
        $displayName = \Illuminate\Support\Str::limit((string) $user->name, 28);
    @endphp

    <div class="overflow-hidden rounded-3xl border border-indigo-200/80 bg-gradient-to-br from-indigo-600 via-violet-600 to-slate-900 shadow-xl shadow-indigo-900/25">
        <div class="relative px-6 py-8 text-white sm:px-8 sm:py-10">
            <div class="pointer-events-none absolute -right-16 -top-20 h-56 w-56 rounded-full bg-cyan-400/20 blur-3xl" aria-hidden="true"></div>
            <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="flex items-start gap-4">
                    <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/15 ring-1 ring-white/30" aria-hidden="true">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25A2.25 2.25 0 018.25 10.5H6A2.25 2.25 0 013.75 8.25V6zM13.5 9.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25h-2.25A2.25 2.25 0 0113.5 18V9.75z"/></svg>
                    </span>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-white/70">Home</p>
                        <h1 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">Hi, {{ $displayName }}</h1>
                        <p class="mt-2 max-w-xl text-sm leading-relaxed text-white/85">Pay bills, track your wallet, and manage payout banks — all from here.</p>
                        <div class="mt-4 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-bold {{ $kycPill }}">
                                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-current opacity-80" aria-hidden="true"></span>
                                KYC {{ $user->kyc_status_label }}
                            </span>
                            <span class="text-xs text-white/70">Code <span class="font-mono font-semibold text-white">{{ $user->user_code }}</span></span>
                        </div>
                    </div>
                </div>
                <a href="{{ route('profile.show') }}" class="inline-flex items-center justify-center gap-2 self-start rounded-2xl bg-white px-5 py-3 text-sm font-bold text-indigo-700 shadow-lg shadow-black/15 transition hover:bg-violet-50">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0"/></svg>
                    Your profile
                </a>
            </div>
        </div>
    </div>

    @if ($user->hasActiveKyc())
        <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <a href="{{ route('payments.create') }}" class="group relative overflow-hidden rounded-2xl border border-emerald-200/80 bg-gradient-to-br from-emerald-50 to-teal-50 p-5 shadow-md ring-1 ring-emerald-900/5 transition hover:border-emerald-300 hover:shadow-lg">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-600 text-white shadow-sm" aria-hidden="true">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                </span>
                <p class="mt-4 font-bold text-slate-900">Pay</p>
                <p class="mt-1 text-xs text-slate-600">Card checkout · add a note</p>
                <span class="mt-3 inline-flex text-xs font-bold text-emerald-700 group-hover:underline">Open →</span>
            </a>
            <a href="{{ route('wallet.index') }}" class="group relative overflow-hidden rounded-2xl border border-indigo-200/80 bg-gradient-to-br from-indigo-50 to-violet-50 p-5 shadow-md ring-1 ring-indigo-900/5 transition hover:border-indigo-300 hover:shadow-lg">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-sm" aria-hidden="true">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H12a2.25 2.25 0 00-2.25 2.25v6.75A2.25 2.25 0 009.75 21.75h-1.5A2.25 2.25 0 016 19.5V12a2.25 2.25 0 00-2.25-2.25H3"/><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 3.375H9.75A2.25 2.25 0 0112 5.625v.75m0 0h3.75m-3.75 0H9m0 0H5.625A2.25 2.25 0 003 8.25v.375c0 .621.504 1.125 1.125 1.125h16.5c.621 0 1.125-.504 1.125-1.125V8.25A2.25 2.25 0 0019.875 6H16.5"/></svg>
                </span>
                <p class="mt-4 font-bold text-slate-900">Wallet</p>
                <p class="mt-1 text-xs text-slate-600">Balance &amp; history</p>
                <span class="mt-3 inline-flex text-xs font-bold text-indigo-700 group-hover:underline">Open →</span>
            </a>
            <a href="{{ route('banks.index') }}" class="group relative overflow-hidden rounded-2xl border border-violet-200/80 bg-gradient-to-br from-violet-50 to-fuchsia-50 p-5 shadow-md ring-1 ring-violet-900/5 transition hover:border-violet-300 hover:shadow-lg">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-violet-600 text-white shadow-sm" aria-hidden="true">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-4h6v4"/></svg>
                </span>
                <p class="mt-4 font-bold text-slate-900">Banks</p>
                <p class="mt-1 text-xs text-slate-600">{{ (int) $user->banks_count }} account{{ (int) $user->banks_count === 1 ? '' : 's' }}</p>
                <span class="mt-3 inline-flex text-xs font-bold text-violet-700 group-hover:underline">Manage →</span>
            </a>
            <a href="{{ route('profile.show') }}" class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-50 to-white p-5 shadow-md ring-1 ring-slate-900/5 transition hover:border-slate-300 hover:shadow-lg">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-800 text-white shadow-sm" aria-hidden="true">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0"/></svg>
                </span>
                <p class="mt-4 font-bold text-slate-900">Profile</p>
                <p class="mt-1 text-xs text-slate-600">Details &amp; KYC</p>
                <span class="mt-3 inline-flex text-xs font-bold text-slate-700 group-hover:underline">View →</span>
            </a>
        </div>
    @else
        <div class="mt-8 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-950">
            <p class="font-semibold">Finish KYC to unlock Pay, Wallet, and Banks.</p>
            <a href="{{ route('kyc.index') }}" class="mt-2 inline-flex font-bold text-amber-900 underline decoration-amber-800/40 hover:text-amber-950">Go to KYC →</a>
        </div>
    @endif

    <div class="mt-8 overflow-hidden rounded-3xl border border-slate-200/90 bg-white shadow-lg ring-1 ring-slate-900/5">
        <div class="border-b border-slate-100 bg-gradient-to-r from-slate-50 via-indigo-50/40 to-violet-50/30 px-6 py-4 sm:px-8">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-600">Account snapshot</h2>
        </div>
        <dl class="grid gap-px bg-slate-100 sm:grid-cols-2">
            <div class="flex items-start gap-4 bg-white p-5 sm:p-6">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600" aria-hidden="true">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 00.365-.83V4.305"/></svg>
                </span>
                <div>
                    <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Email</dt>
                    <dd class="mt-1 break-all text-sm font-semibold text-slate-900">{{ $user->email }}</dd>
                </div>
            </div>
            <div class="flex items-start gap-4 bg-white p-5 sm:p-6">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600" aria-hidden="true">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-9 18.75h10.5a2.25 2.25 0 002.25-2.25V6.75m-15 12.75v-9"/></svg>
                </span>
                <div>
                    <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Phone</dt>
                    <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $user->phone ?? '—' }}</dd>
                </div>
            </div>
            <div class="flex items-start gap-4 bg-white p-5 sm:p-6">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600" aria-hidden="true">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 8.25h15m-16.5 7.5h15m-13.5-6v6m10.5-6v6"/></svg>
                </span>
                <div>
                    <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">User code</dt>
                    <dd class="mt-1 font-mono text-sm font-bold text-slate-900">{{ $user->user_code }}</dd>
                </div>
            </div>
            <div class="flex items-start gap-4 bg-white p-5 sm:p-6">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600" aria-hidden="true">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                </span>
                <div>
                    <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">KYC</dt>
                    <dd class="mt-1">
                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $kycSnap }}">{{ $user->kyc_status_label }}</span>
                    </dd>
                </div>
            </div>
        </dl>
    </div>
@endsection
