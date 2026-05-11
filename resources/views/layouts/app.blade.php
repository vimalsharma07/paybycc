<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    @include('partials.head-styles')
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-5xl items-center justify-between gap-4 px-4 py-4 sm:px-6">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-cyan-500 via-violet-500 to-amber-400 text-[10px] font-black text-white shadow-md">PB</span>
                <x-brand-wordmark variant="light" class="leading-none" />
            </a>
            <nav class="flex flex-wrap items-center gap-3 text-sm sm:gap-4">
                @if (! auth()->user()->is_admin && auth()->user()->hasActiveKyc())
                    <a href="{{ route('payments.create') }}" class="pay-now-btn inline-flex items-center rounded-lg bg-gradient-to-r from-cyan-600 via-indigo-600 to-violet-600 px-4 py-2 text-sm font-bold text-white shadow-md transition hover:brightness-110">Pay now</a>
                    <a href="{{ route('wallet.index') }}" class="font-medium text-slate-700 hover:text-indigo-600">Wallet</a>
                    <a href="{{ route('banks.index') }}" class="font-medium text-slate-700 hover:text-indigo-600">Banks</a>
                @endif
                <span class="hidden text-slate-600 sm:inline">{{ auth()->user()->name }}</span>
                <span class="rounded-md bg-slate-100 px-2 py-1 font-mono text-xs text-slate-700">{{ auth()->user()->user_code }}</span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="font-medium text-slate-700 underline decoration-slate-300 underline-offset-4 hover:text-indigo-600">Log out</button>
                </form>
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-4 py-10 sm:px-6">
        @if (session('status'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
