<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('meta_description', 'Pay tuition, utilities, rent & more with '.config('app.name').'. Secure card payments & wallet settlements.')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name').' — Pay bills & tuition securely')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800|instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="marketing-body min-h-screen bg-slate-950 font-sans text-slate-100 antialiased">
    <div class="pointer-events-none fixed inset-0 overflow-hidden">
        <div class="marketing-gradient absolute -left-1/4 top-0 h-[500px] w-[800px] rounded-full bg-indigo-600/30 blur-[120px]"></div>
        <div class="marketing-gradient absolute -right-1/4 top-32 h-[400px] w-[600px] rounded-full bg-violet-600/25 blur-[100px]" style="animation-delay: 2s"></div>
        <div class="absolute bottom-0 left-1/3 h-[300px] w-[500px] rounded-full bg-cyan-500/15 blur-[90px]"></div>
    </div>

    <header class="relative z-50 border-b border-white/10 bg-slate-950/80 backdrop-blur-xl">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4 sm:px-6">
            <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2 text-lg font-bold tracking-tight text-white">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 text-sm font-extrabold shadow-lg shadow-indigo-500/30">P</span>
                {{ config('app.name') }}
            </a>
            <nav class="hidden flex-1 items-center justify-center gap-1 lg:flex">
                <a href="{{ route('home') }}#bills" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-300 transition hover:bg-white/5 hover:text-white">Bill types</a>
                <a href="{{ route('about') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-300 transition hover:bg-white/5 hover:text-white">About</a>
                <a href="{{ route('contact') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-300 transition hover:bg-white/5 hover:text-white">Contact</a>
                <a href="{{ route('privacy') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-400 transition hover:bg-white/5 hover:text-white">Privacy</a>
                <a href="{{ route('terms') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-400 transition hover:bg-white/5 hover:text-white">Terms</a>
            </nav>
            <div class="flex shrink-0 items-center gap-2">
                @auth
                    @php
                        $dash = auth()->user()->is_admin
                            ? route('admin.dashboard')
                            : (auth()->user()->hasActiveKyc() ? route('dashboard') : route('kyc.index'));
                    @endphp
                    <a href="{{ $dash }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-white transition hover:bg-white/10">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="hidden rounded-lg px-3 py-2 text-sm font-medium text-slate-300 transition hover:text-white sm:inline">Log in</a>
                    <a href="{{ route('register') }}" class="rounded-xl bg-gradient-to-r from-indigo-500 to-violet-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition hover:brightness-110">Get started</a>
                @endauth
            </div>
        </div>
        <nav class="flex flex-wrap items-center justify-center gap-x-3 gap-y-2 border-t border-white/5 px-4 py-3 text-xs lg:hidden">
            <a href="{{ route('home') }}#bills" class="text-slate-400 transition hover:text-white">Bills</a>
            <a href="{{ route('about') }}" class="text-slate-400 transition hover:text-white">About</a>
            <a href="{{ route('contact') }}" class="text-slate-400 transition hover:text-white">Contact</a>
            <a href="{{ route('privacy') }}" class="text-slate-400 transition hover:text-white">Privacy</a>
            <a href="{{ route('terms') }}" class="text-slate-400 transition hover:text-white">Terms</a>
            @guest
                <a href="{{ route('login') }}" class="text-indigo-400 transition hover:text-white">Log in</a>
            @endguest
        </nav>
    </header>

    <main class="relative z-10">
        @if (session('status'))
            <div class="mx-auto max-w-6xl px-4 pt-6 sm:px-6">
                <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">{{ session('status') }}</div>
            </div>
        @endif
        @yield('content')
    </main>

    <footer class="relative z-10 mt-20 border-t border-white/10 bg-slate-950/90 py-14">
        <div class="mx-auto grid max-w-6xl gap-10 px-4 sm:grid-cols-2 sm:px-6 lg:grid-cols-4">
            <div class="lg:col-span-2">
                <p class="flex items-center gap-2 text-lg font-bold text-white">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 text-sm font-extrabold">P</span>
                    {{ config('app.name') }}
                </p>
                <p class="mt-3 max-w-md text-sm leading-relaxed text-slate-400">Pay tuition, household bills, rent & more — with secure card payments, clear settlement timing, and wallet tools built for real life.</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Product</p>
                <ul class="mt-4 space-y-2 text-sm">
                    <li><a href="{{ route('home') }}#bills" class="text-slate-400 transition hover:text-white">Bill types</a></li>
                    <li><a href="{{ route('about') }}" class="text-slate-400 transition hover:text-white">About us</a></li>
                    @auth
                        <li><a href="{{ route('payments.create') }}" class="text-slate-400 transition hover:text-white">Pay now</a></li>
                    @else
                        <li><a href="{{ route('register') }}" class="text-slate-400 transition hover:text-white">Create account</a></li>
                    @endauth
                </ul>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Legal &amp; help</p>
                <ul class="mt-4 space-y-2 text-sm">
                    <li><a href="{{ route('privacy') }}" class="text-slate-400 transition hover:text-white">Privacy policy</a></li>
                    <li><a href="{{ route('terms') }}" class="text-slate-400 transition hover:text-white">Terms &amp; conditions</a></li>
                    <li><a href="{{ route('contact') }}" class="text-slate-400 transition hover:text-white">Contact us</a></li>
                </ul>
            </div>
        </div>
        <p class="mx-auto mt-12 max-w-6xl border-t border-white/5 px-4 pt-8 text-center text-xs text-slate-600 sm:px-6">
            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </p>
    </footer>
</body>
</html>
