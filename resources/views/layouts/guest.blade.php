<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800|instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="auth-body min-h-screen font-sans">
    <div class="pointer-events-none fixed inset-0 overflow-hidden">
        <div class="marketing-gradient absolute -left-1/4 top-0 h-[420px] w-[720px] rounded-full bg-indigo-600/25 blur-[110px]"></div>
        <div class="marketing-gradient absolute -right-1/4 top-24 h-[360px] w-[560px] rounded-full bg-violet-600/20 blur-[100px]" style="animation-delay: 2s"></div>
        <div class="absolute bottom-0 left-1/4 h-[280px] w-[480px] rounded-full bg-cyan-500/10 blur-[90px]"></div>
    </div>

    <div class="relative z-10 flex min-h-screen flex-col lg:flex-row">
        <aside class="relative flex flex-col justify-center px-6 pb-6 pt-10 sm:px-10 lg:w-[46%] lg:max-w-xl lg:px-12 lg:pb-16 lg:pt-16">
            <div class="animate-fade-up">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 text-lg font-bold tracking-tight text-white transition hover:opacity-90">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 text-sm font-extrabold shadow-lg shadow-indigo-500/30">P</span>
                    {{ config('app.name') }}
                </a>
            </div>

            <div class="mt-8 animate-fade-up animate-delay-100 space-y-4 lg:mt-14">
                @yield('guest_hero')
            </div>

            <div class="mt-10 hidden animate-fade-up animate-delay-200 lg:block">
                <div class="animate-float-soft relative rounded-2xl border border-white/10 bg-gradient-to-br from-white/[0.07] to-white/[0.02] p-6 backdrop-blur-sm">
                    <div class="absolute -right-3 -top-3 h-16 w-16 rounded-full bg-indigo-500/20 blur-2xl"></div>
                    <p class="relative text-sm font-medium text-slate-300">Secure payments · Clear settlement · Built for everyday bills</p>
                    <div class="relative mt-4 flex flex-wrap gap-2">
                        <span class="rounded-lg bg-white/5 px-2.5 py-1 text-xs font-medium text-slate-400 ring-1 ring-white/10">Tuition</span>
                        <span class="rounded-lg bg-white/5 px-2.5 py-1 text-xs font-medium text-slate-400 ring-1 ring-white/10">Utilities</span>
                        <span class="rounded-lg bg-white/5 px-2.5 py-1 text-xs font-medium text-slate-400 ring-1 ring-white/10">Wallet</span>
                    </div>
                </div>
            </div>
        </aside>

        <main class="flex flex-1 flex-col justify-center px-4 pb-12 pt-2 sm:px-6 lg:px-10 lg:py-16">
            <div class="mx-auto w-full max-w-md animate-fade-up animate-delay-200">
                @if (session('status'))
                    <div class="mb-6 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="auth-card">
                    @yield('content')
                </div>

                @yield('guest-footer')

                <nav class="mt-8 flex flex-wrap justify-center gap-x-4 gap-y-2 text-center text-xs text-slate-500">
                    <a href="{{ route('home') }}" class="transition hover:text-indigo-400">Home</a>
                    <a href="{{ route('about') }}" class="transition hover:text-indigo-400">About</a>
                    <a href="{{ route('contact') }}" class="transition hover:text-indigo-400">Contact</a>
                    <a href="{{ route('privacy') }}" class="transition hover:text-indigo-400">Privacy</a>
                    <a href="{{ route('terms') }}" class="transition hover:text-indigo-400">Terms</a>
                </nav>
            </div>
        </main>
    </div>
</body>
</html>
