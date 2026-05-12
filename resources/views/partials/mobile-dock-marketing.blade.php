{{-- Marketing site: bottom bar (mobile only). "Menu" opens icon grid sheet. --}}
@php
    $admin = auth()->check() && auth()->user()->is_admin;
    $kyc = auth()->check() && auth()->user()->hasActiveKyc() && ! $admin;
    $dialogId = 'mobile-explore-marketing';

    $tilesLegal = [
        ['href' => route('privacy'), 'label' => 'Privacy', 'icon' => 'shield'],
        ['href' => route('terms'), 'label' => 'Terms', 'icon' => 'document'],
        ['href' => route('contact'), 'label' => 'Contact', 'icon' => 'mail'],
        ['href' => route('about'), 'label' => 'About', 'icon' => 'info'],
        ['href' => route('home').'#bills', 'label' => 'Bill types', 'icon' => 'grid'],
    ];

    $tilesGuest = array_merge($tilesLegal, [
        ['href' => route('register'), 'label' => 'Sign up', 'icon' => 'user-plus'],
        ['href' => route('login'), 'label' => 'Log in', 'icon' => 'login'],
    ]);

    $tilesAuthKyc = array_merge($tilesLegal, [
        ['href' => route('dashboard'), 'label' => 'Dashboard', 'icon' => 'dashboard'],
        ['logout' => true, 'label' => 'Log out', 'icon' => 'logout'],
    ]);

    $tilesAuthNoKyc = array_merge($tilesLegal, [
        ['href' => route('dashboard'), 'label' => 'Dashboard', 'icon' => 'dashboard'],
        ['href' => route('kyc.index'), 'label' => 'Complete KYC', 'icon' => 'user-plus'],
        ['logout' => true, 'label' => 'Log out', 'icon' => 'logout'],
    ]);
@endphp

@if (! $admin)
    <nav class="mobile-dock fixed bottom-0 left-0 right-0 z-50 border-t border-white/10 bg-slate-950/95 backdrop-blur-md lg:hidden mobile-dock-safe" aria-label="Quick navigation">
        @guest
            <div class="relative mx-auto grid min-h-[4.75rem] max-w-lg grid-cols-5 items-end px-0.5 pb-1.5 pt-3">
                <a href="{{ route('home') }}" class="mobile-dock-item flex flex-col items-center justify-end gap-0.5 pb-1.5 pt-1 text-slate-400 hover:text-white {{ request()->routeIs('home') ? 'text-cyan-300' : '' }}">
                    <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 22V12h6v10"/></svg>
                    <span class="text-[10px] font-semibold leading-none">Home</span>
                </a>
                <a href="{{ route('about') }}" class="mobile-dock-item flex flex-col items-center justify-end gap-0.5 pb-1.5 pt-1 text-slate-400 hover:text-white {{ request()->routeIs('about') ? 'text-cyan-300' : '' }}">
                    <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                    <span class="text-[10px] font-semibold leading-none">About</span>
                </a>
                <div class="relative z-10 flex min-h-[2.75rem] justify-center">
                    <a href="{{ route('register') }}" class="mobile-dock-fab absolute bottom-full left-1/2 z-[51] mb-1.5 flex h-[3.25rem] w-[3.25rem] -translate-x-1/2 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-500 via-indigo-600 to-violet-600 text-xs font-extrabold text-white shadow-lg shadow-indigo-500/30 ring-4 ring-slate-950 transition hover:brightness-110 active:scale-95" aria-label="Pay now">
                        Pay
                    </a>
                </div>
                <a href="{{ route('login') }}" class="mobile-dock-item flex flex-col items-center justify-end gap-0.5 pb-1.5 pt-1 text-slate-400 hover:text-white {{ request()->routeIs('login') ? 'text-cyan-300' : '' }}">
                    <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span class="text-[10px] font-semibold leading-none">Log in</span>
                </a>
                <button type="button" data-open-dialog="{{ $dialogId }}" class="mobile-dock-item flex w-full flex-col items-center justify-end gap-0.5 pb-1.5 pt-1 text-slate-400 hover:text-white">
                    <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7"/></svg>
                    <span class="text-[10px] font-semibold leading-none">Menu</span>
                </button>
            </div>
        @else
            @if ($kyc)
                <div class="relative mx-auto grid min-h-[4.75rem] max-w-lg grid-cols-5 items-end px-0.5 pb-1.5 pt-3">
                    <a href="{{ route('home') }}" class="mobile-dock-item flex flex-col items-center justify-end gap-0.5 pb-1.5 pt-1 text-slate-400 hover:text-white {{ request()->routeIs('home') ? 'text-cyan-300' : '' }}">
                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 22V12h6v10"/></svg>
                        <span class="text-[10px] font-semibold leading-none">Home</span>
                    </a>
                    <a href="{{ route('wallet.index') }}" class="mobile-dock-item flex flex-col items-center justify-end gap-0.5 pb-1.5 pt-1 text-slate-400 hover:text-white {{ request()->routeIs('wallet.*') ? 'text-cyan-300' : '' }}">
                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H12a2.25 2.25 0 00-2.25 2.25v6.75A2.25 2.25 0 009.75 21.75h-1.5A2.25 2.25 0 016 19.5V12a2.25 2.25 0 00-2.25-2.25H3"/><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 3.375H9.75A2.25 2.25 0 0112 5.625v.75m0 0h3.75m-3.75 0H9m0 0H5.625A2.25 2.25 0 003 8.25v.375c0 .621.504 1.125 1.125 1.125h16.5c.621 0 1.125-.504 1.125-1.125V8.25A2.25 2.25 0 0019.875 6H16.5"/></svg>
                        <span class="text-[10px] font-semibold leading-none">Wallet</span>
                    </a>
                    <div class="relative z-10 flex min-h-[2.75rem] justify-center">
                        <a href="{{ route('payments.create') }}" class="mobile-dock-fab absolute bottom-full left-1/2 z-[51] mb-1.5 flex h-[3.25rem] w-[3.25rem] -translate-x-1/2 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-500 via-indigo-600 to-violet-600 text-xs font-extrabold text-white shadow-lg shadow-indigo-500/30 ring-4 ring-slate-950 transition hover:brightness-110 active:scale-95 {{ request()->routeIs('payments.*') ? 'ring-cyan-400/50' : '' }}" aria-label="Pay now">
                            Pay
                        </a>
                    </div>
                    <a href="{{ route('banks.index') }}" class="mobile-dock-item flex flex-col items-center justify-end gap-0.5 pb-1.5 pt-1 text-slate-400 hover:text-white {{ request()->routeIs('banks.*') ? 'text-cyan-300' : '' }}">
                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-4h6v4"/></svg>
                        <span class="text-[10px] font-semibold leading-none">Banks</span>
                    </a>
                    <button type="button" data-open-dialog="{{ $dialogId }}" class="mobile-dock-item flex w-full flex-col items-center justify-end gap-0.5 pb-1.5 pt-1 text-slate-400 hover:text-white">
                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7"/></svg>
                        <span class="text-[10px] font-semibold leading-none">Menu</span>
                    </button>
                </div>
            @else
                <div class="relative mx-auto grid min-h-[4.75rem] max-w-lg grid-cols-5 items-end px-0.5 pb-1.5 pt-3">
                    <a href="{{ route('home') }}" class="mobile-dock-item flex flex-col items-center justify-end gap-0.5 pb-1.5 pt-1 text-slate-400 hover:text-white {{ request()->routeIs('home') ? 'text-cyan-300' : '' }}">
                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 22V12h6v10"/></svg>
                        <span class="text-[10px] font-semibold leading-none">Home</span>
                    </a>
                    <a href="{{ route('about') }}" class="mobile-dock-item flex flex-col items-center justify-end gap-0.5 pb-1.5 pt-1 text-slate-400 hover:text-white {{ request()->routeIs('about') ? 'text-cyan-300' : '' }}">
                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                        <span class="text-[10px] font-semibold leading-none">About</span>
                    </a>
                    <div class="relative z-10 flex min-h-[2.75rem] justify-center">
                        <a href="{{ route('kyc.index') }}" class="mobile-dock-fab absolute bottom-full left-1/2 z-[51] mb-1.5 flex h-[3.25rem] w-[3.25rem] -translate-x-1/2 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-500 via-indigo-600 to-violet-600 text-xs font-extrabold text-white shadow-lg ring-4 ring-slate-950 transition hover:brightness-110 active:scale-95 {{ request()->routeIs('kyc.*') ? 'ring-cyan-400/50' : '' }}" aria-label="Complete KYC">
                            KYC
                        </a>
                    </div>
                    <a href="{{ route('dashboard') }}" class="mobile-dock-item flex flex-col items-center justify-end gap-0.5 pb-1.5 pt-1 text-slate-400 hover:text-white {{ request()->routeIs('dashboard') ? 'text-cyan-300' : '' }}">
                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25A2.25 2.25 0 018.25 10.5H6A2.25 2.25 0 013.75 8.25V6zM13.5 9.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25h-2.25A2.25 2.25 0 0113.5 18V9.75z"/></svg>
                        <span class="text-[10px] font-semibold leading-none">App</span>
                    </a>
                    <button type="button" data-open-dialog="{{ $dialogId }}" class="mobile-dock-item flex w-full flex-col items-center justify-end gap-0.5 pb-1.5 pt-1 text-slate-400 hover:text-white">
                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7"/></svg>
                        <span class="text-[10px] font-semibold leading-none">Menu</span>
                    </button>
                </div>
            @endif
        @endguest
    </nav>

    @guest
        @include('partials.mobile-explore-dialog', ['dialogId' => $dialogId, 'skin' => 'dark', 'tiles' => $tilesGuest])
    @else
        @include('partials.mobile-explore-dialog', ['dialogId' => $dialogId, 'skin' => 'dark', 'tiles' => $kyc ? $tilesAuthKyc : $tilesAuthNoKyc])
    @endguest
@endif
