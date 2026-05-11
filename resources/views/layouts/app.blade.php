<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    @include('partials.head-styles')
</head>
@php
    $appDock = ! auth()->user()->is_admin;
@endphp
<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-5xl items-center justify-center px-4 py-3 sm:px-6 lg:justify-between lg:py-4">
            <a href="{{ route('dashboard') }}" class="flex items-center">
                <x-brand-wordmark variant="light" class="leading-none" />
            </a>
            <nav class="hidden flex-wrap items-center gap-3 text-sm lg:flex lg:gap-4">
                @if (! auth()->user()->is_admin && auth()->user()->hasActiveKyc())
                    <a href="{{ route('payments.create') }}" class="pay-now-btn inline-flex items-center rounded-lg bg-gradient-to-r from-cyan-600 via-indigo-600 to-violet-600 px-4 py-2 text-sm font-bold text-white shadow-md transition hover:brightness-110">Pay now</a>
                    <a href="{{ route('wallet.index') }}" class="font-medium text-slate-700 hover:text-indigo-600">Wallet</a>
                    <a href="{{ route('banks.index') }}" class="font-medium text-slate-700 hover:text-indigo-600">Banks</a>
                @endif
                <span class="hidden text-slate-600 xl:inline">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="font-medium text-slate-700 underline decoration-slate-300 underline-offset-4 hover:text-indigo-600">Log out</button>
                </form>
            </nav>
        </div>
    </header>

    <main class="app-main-shell mx-auto max-w-5xl px-4 py-6 sm:px-6 lg:py-10 {{ $appDock ? 'pb-24 lg:pb-10' : '' }}">
        @if (session('status'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        @yield('content')
    </main>

    @include('partials.mobile-dock-app')
</body>
</html>
