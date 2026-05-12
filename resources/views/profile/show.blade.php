@extends('layouts.app')

@section('title', 'Profile — '.config('app.name'))

@section('content')
    @php
        $k = (int) $user->kyc_status;
        $kycPill = match ($k) {
            \App\Models\User::KYC_ACTIVE => 'bg-emerald-100 text-emerald-900 ring-1 ring-emerald-400/40',
            \App\Models\User::KYC_INACTIVE => 'bg-amber-100 text-amber-900 ring-1 ring-amber-400/45',
            \App\Models\User::KYC_INCOMPLETE => 'bg-rose-100 text-rose-900 ring-1 ring-rose-400/45',
            default => 'bg-slate-100 text-slate-800 ring-1 ring-slate-300/60',
        };
        $kycHint = match ($k) {
            \App\Models\User::KYC_ACTIVE => 'You can pay and manage banks/wallet.',
            \App\Models\User::KYC_INACTIVE => 'KYC is not active yet.',
            \App\Models\User::KYC_INCOMPLETE => 'Complete KYC to enable payments.',
            default => 'KYC status unknown.',
        };
        $maskedAadhar = is_string($user->aadhar) && strlen($user->aadhar) === 12
            ? str_repeat('•', 8).substr($user->aadhar, -4)
            : ($user->aadhar ? '••••' : '—');
    @endphp

    <div class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div class="flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-sm" aria-hidden="true">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                </span>
                <div>
                    <h1 class="text-2xl font-semibold text-slate-900">Profile</h1>
                    <p class="mt-0.5 text-sm text-slate-600">Your details and KYC status.</p>
                </div>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-slate-50">
                Dashboard
            </a>
            @if (! $user->hasActiveKyc())
                <a href="{{ route('kyc.index') }}" class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500">
                    Complete KYC
                </a>
            @else
                <a href="{{ route('payments.create') }}" class="inline-flex items-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-emerald-500">
                    Pay
                </a>
            @endif
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Account</p>
                    <p class="mt-2 text-lg font-semibold text-slate-900">{{ $user->name }}</p>
                    <p class="text-sm text-slate-600">{{ $user->email }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">KYC</p>
                    <span class="mt-2 inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $kycPill }}">{{ $user->kyc_status_label }}</span>
                    <p class="mt-2 text-xs text-slate-500">{{ $kycHint }}</p>
                </div>
            </div>

            <dl class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">User code</dt>
                    <dd class="mt-1 font-mono text-sm font-semibold text-slate-900">{{ $user->user_code }}</dd>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Phone</dt>
                    <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $user->phone ?? '—' }}</dd>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">PAN</dt>
                    <dd class="mt-1 font-mono text-sm font-semibold text-slate-900">{{ $user->pan ?? '—' }}</dd>
                    @if ($user->pan_name)
                        <dd class="mt-1 text-xs text-slate-600">{{ $user->pan_name }}</dd>
                    @endif
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Aadhaar</dt>
                    <dd class="mt-1 font-mono text-sm font-semibold text-slate-900">{{ $maskedAadhar }}</dd>
                </div>
            </dl>
        </div>

        <div class="space-y-4">
            <a href="{{ route('wallet.index') }}" class="group block rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-indigo-200 hover:shadow-md">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-700" aria-hidden="true">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H12a2.25 2.25 0 00-2.25 2.25v6.75A2.25 2.25 0 009.75 21.75h-1.5A2.25 2.25 0 016 19.5V12a2.25 2.25 0 00-2.25-2.25H3"/><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 3.375H9.75A2.25 2.25 0 0112 5.625v.75m0 0h3.75m-3.75 0H9m0 0H5.625A2.25 2.25 0 003 8.25v.375c0 .621.504 1.125 1.125 1.125h16.5c.621 0 1.125-.504 1.125-1.125V8.25A2.25 2.25 0 0019.875 6H16.5"/></svg>
                    </span>
                    <div class="min-w-0">
                        <p class="font-semibold text-slate-900 group-hover:text-indigo-700">Wallet</p>
                        <p class="mt-0.5 text-xs text-slate-600">Balance, settlement settings, transactions.</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('banks.index') }}" class="group block rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-indigo-200 hover:shadow-md">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-violet-100 text-violet-700" aria-hidden="true">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-4h6v4"/></svg>
                    </span>
                    <div class="min-w-0">
                        <p class="font-semibold text-slate-900 group-hover:text-violet-700">Bank accounts</p>
                        <p class="mt-0.5 text-xs text-slate-600">{{ (int) $user->banks_count }} saved · payouts setup</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
@endsection

