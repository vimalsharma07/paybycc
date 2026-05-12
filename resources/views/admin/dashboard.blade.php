@extends('layouts.admin')

@section('title', 'Admin dashboard — '.$siteSettings->displayName())

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-semibold text-slate-900">Dashboard</h1>
        <p class="mt-1 text-sm text-slate-600">Overview for administrators.</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <a href="{{ route('admin.users.index') }}" class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-indigo-200 hover:shadow-md">
            <p class="text-sm font-medium text-slate-500">Total users</p>
            <p class="mt-2 text-3xl font-semibold tabular-nums text-slate-900">{{ $userCount }}</p>
            <p class="mt-3 text-sm font-medium text-indigo-600 group-hover:text-indigo-500">Manage users →</p>
        </a>
        <a href="{{ route('admin.banks.index') }}" class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-indigo-200 hover:shadow-md">
            <p class="text-sm font-medium text-slate-500">Bank accounts</p>
            <p class="mt-2 text-3xl font-semibold tabular-nums text-slate-900">{{ $bankCount }}</p>
            <p class="mt-3 text-sm font-medium text-indigo-600 group-hover:text-indigo-500">Review &amp; edit banks →</p>
        </a>
        <a href="{{ route('admin.gateways.index') }}" class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-indigo-200 hover:shadow-md">
            <p class="text-sm font-medium text-slate-500">Payment gateways</p>
            <p class="mt-2 text-3xl font-semibold tabular-nums text-slate-900">{{ $gatewayCount }}</p>
            <p class="mt-3 text-sm font-medium text-indigo-600 group-hover:text-indigo-500">Configure drivers →</p>
        </a>
        <a href="{{ route('admin.website-settings.edit') }}" class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-indigo-200 hover:shadow-md">
            <p class="text-sm font-medium text-slate-500">Website</p>
            <p class="mt-2 text-lg font-semibold text-slate-900">{{ $siteSettings->displayName() }}</p>
            <p class="mt-3 text-sm font-medium text-indigo-600 group-hover:text-indigo-500">Branding, contact &amp; social →</p>
        </a>
        <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-6">
            <p class="text-sm font-medium text-slate-500">Tips</p>
            <p class="mt-2 text-sm text-slate-600">Set one gateway as primary so customer payments use your active driver and credentials.</p>
        </div>
    </div>

    <div class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-900">Signed in</h2>
        <p class="mt-2 text-sm text-slate-600">{{ auth()->user()->name }} · {{ auth()->user()->email }}</p>
        <p class="mt-1 font-mono text-xs text-slate-500">{{ auth()->user()->user_code }}</p>
    </div>
@endsection
