<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin — '.config('app.name'))</title>
    @include('partials.head-styles')
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-900 antialiased">
    <div class="flex min-h-screen">
        <aside class="hidden w-56 shrink-0 flex-col border-r border-slate-200 bg-slate-900 text-slate-100 md:flex">
            <div class="border-b border-slate-700 px-4 py-5">
                <span class="text-xs font-medium uppercase tracking-wider text-slate-400">Admin</span>
                <p class="mt-2">
                    <x-brand-wordmark variant="dark" size="sm" class="leading-none" />
                </p>
            </div>
            <nav class="flex flex-1 flex-col gap-1 p-3">
                <a href="{{ route('admin.dashboard') }}" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    Dashboard
                </a>
                <a href="{{ route('admin.users.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.users.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    Users
                </a>
                <a href="{{ route('admin.banks.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.banks.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    Bank accounts
                </a>
                <a href="{{ route('admin.gateways.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.gateways.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    Gateways
                </a>
                <a href="{{ route('admin.transactions.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.transactions.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    Transactions
                </a>
            </nav>
            <div class="border-t border-slate-700 p-3">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-sm text-slate-300 hover:bg-slate-800 hover:text-white">Log out</button>
                </form>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="border-b border-slate-200 bg-white px-4 py-3 md:hidden">
                <div class="flex items-center justify-between gap-2">
                    <span class="font-semibold text-slate-900">Admin</span>
                    <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-indigo-600">Log out</button>
                    </form>
                </div>
                <nav class="mt-3 flex flex-wrap gap-2 text-sm">
                    <a href="{{ route('admin.dashboard') }}" class="rounded-md px-2 py-1 font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-slate-900 text-white' : 'text-slate-700' }}">Dashboard</a>
                    <a href="{{ route('admin.users.index') }}" class="rounded-md px-2 py-1 font-medium {{ request()->routeIs('admin.users.*') ? 'bg-slate-900 text-white' : 'text-slate-700' }}">Users</a>
                    <a href="{{ route('admin.banks.index') }}" class="rounded-md px-2 py-1 font-medium {{ request()->routeIs('admin.banks.*') ? 'bg-slate-900 text-white' : 'text-slate-700' }}">Banks</a>
                    <a href="{{ route('admin.gateways.index') }}" class="rounded-md px-2 py-1 font-medium {{ request()->routeIs('admin.gateways.*') ? 'bg-slate-900 text-white' : 'text-slate-700' }}">Gateways</a>
                    <a href="{{ route('admin.transactions.index') }}" class="rounded-md px-2 py-1 font-medium {{ request()->routeIs('admin.transactions.*') ? 'bg-slate-900 text-white' : 'text-slate-700' }}">Transactions</a>
                </nav>
            </header>

            <main class="min-w-0 w-full flex-1 p-4 sm:p-8">
                @if (session('status'))
                    <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                        {{ session('status') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
