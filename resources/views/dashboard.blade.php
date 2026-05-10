@extends('layouts.app')

@section('title', 'Dashboard — '.config('app.name'))

@section('content')
    <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
        <h1 class="text-2xl font-semibold text-slate-900">Dashboard</h1>
        <p class="mt-2 text-slate-600">You are signed in as <span class="font-medium text-slate-900">{{ $user->name }}</span>.</p>

        @if ($user->hasActiveKyc())
            <div class="mt-6 flex flex-wrap items-center gap-4">
                <a href="{{ route('payments.create') }}" class="inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-emerald-500">
                    Make a payment
                </a>
                <a href="{{ route('wallet.index') }}" class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-slate-50">
                    Wallet
                </a>
                <a href="{{ route('banks.index') }}" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500">
                    Manage bank accounts
                </a>
                <span class="text-sm text-slate-600">{{ $user->banks_count }} saved</span>
            </div>
        @endif

        <dl class="mt-8 grid gap-4 sm:grid-cols-2">
            <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">User code</dt>
                <dd class="mt-1 font-mono text-sm font-semibold text-slate-900">{{ $user->user_code }}</dd>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">KYC status</dt>
                <dd class="mt-1 text-sm font-semibold text-emerald-700">{{ $user->kyc_status_label }}</dd>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Phone</dt>
                <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $user->phone }}</dd>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Email</dt>
                <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $user->email }}</dd>
            </div>
        </dl>
    </div>
@endsection
