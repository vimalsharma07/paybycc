<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-4 sm:px-6">
            <a href="{{ route('dashboard') }}" class="text-lg font-semibold text-indigo-600">{{ config('app.name') }}</a>
            <nav class="flex flex-wrap items-center gap-4 text-sm">
                @if (! auth()->user()->is_admin && auth()->user()->hasActiveKyc())
                    <a href="{{ route('payments.create') }}" class="font-medium text-slate-700 hover:text-indigo-600">Pay</a>
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
