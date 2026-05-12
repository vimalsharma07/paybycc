<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $siteSettings->displayName())</title>
    @include('partials.head-styles')
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
                @include('partials.site-brand-header', ['href' => route('home'), 'variant' => 'dark', 'wrapperClass' => 'inline-block', 'logoSize' => 'lg', 'imgClass' => 'h-10 w-auto max-w-[260px] object-left sm:h-11'])
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

        <main class="flex flex-1 flex-col justify-center px-4 pb-28 pt-2 sm:px-6 lg:px-10 lg:pb-16 lg:py-16">
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

    @php
        $guestExploreId = 'mobile-explore-guest';
        $guestExploreTiles = [
            ['href' => route('privacy'), 'label' => 'Privacy', 'icon' => 'shield'],
            ['href' => route('terms'), 'label' => 'Terms', 'icon' => 'document'],
            ['href' => route('contact'), 'label' => 'Contact', 'icon' => 'mail'],
            ['href' => route('about'), 'label' => 'About', 'icon' => 'info'],
            ['href' => route('home').'#bills', 'label' => 'Bill types', 'icon' => 'grid'],
            ['href' => route('home'), 'label' => 'Home page', 'icon' => 'home'],
            ['href' => route('register'), 'label' => 'Sign up', 'icon' => 'user-plus'],
            ['href' => route('login'), 'label' => 'Log in', 'icon' => 'login'],
        ];
    @endphp
    <nav class="mobile-dock fixed bottom-0 left-0 right-0 z-50 border-t border-white/10 bg-slate-950/95 backdrop-blur-md lg:hidden mobile-dock-safe" aria-label="Quick links">
        <div class="mx-auto grid min-h-[4.25rem] max-w-md grid-cols-2 gap-2 px-6 py-2">
            <a href="{{ route('home') }}" class="mobile-dock-item flex flex-col items-center justify-center gap-1 rounded-2xl border border-white/10 bg-white/5 py-3 text-slate-300 transition hover:border-cyan-400/30 hover:bg-white/10 hover:text-white">
                <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 22V12h6v10"/></svg>
                <span class="text-[11px] font-semibold">Marketing home</span>
            </a>
            <button type="button" data-open-dialog="{{ $guestExploreId }}" class="mobile-dock-item flex flex-col items-center justify-center gap-1 rounded-2xl border border-white/10 bg-white/5 py-3 text-slate-300 transition hover:border-cyan-400/30 hover:bg-white/10 hover:text-white">
                <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7"/></svg>
                <span class="text-[11px] font-semibold">Menu & legal</span>
            </button>
        </div>
    </nav>
    @include('partials.mobile-explore-dialog', ['dialogId' => $guestExploreId, 'skin' => 'dark', 'tiles' => $guestExploreTiles])
</body>
</html>
