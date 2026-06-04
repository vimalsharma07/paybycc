{{-- Marketing site only: public bottom bar (mobile). App wallet/bank dock lives in layouts.app — not here. --}}
@php
    $admin = auth()->check() && auth()->user()->is_admin;
    $dialogId = 'mobile-explore-marketing';
    $dashHref = auth()->check() && ! $admin
        ? (auth()->user()->canUsePlatform() ? route('dashboard') : route('kyc.index'))
        : route('login');
    $payHref = auth()->check() && ! $admin
        ? (auth()->user()->canUsePlatform() ? route('payments.create') : route('kyc.index'))
        : route('register');
    $payFabLabel = auth()->check() && ! $admin && ! auth()->user()->canUsePlatform() ? 'KYC' : 'Pay';

    $tilesLegal = [
        ['href' => route('privacy'), 'label' => 'Privacy', 'icon' => 'shield'],
        ['href' => route('terms'), 'label' => 'Terms', 'icon' => 'document'],
        ['href' => route('contact'), 'label' => 'Contact', 'icon' => 'mail'],
        ['href' => route('about'), 'label' => 'About', 'icon' => 'info'],
        ['href' => route('home').'#marketplace', 'label' => 'Marketplace', 'icon' => 'grid'],
    ];

    $tilesGuest = array_merge($tilesLegal, [
        ['href' => route('register'), 'label' => 'Sign up', 'icon' => 'user-plus'],
        ['href' => route('login'), 'label' => 'Log in', 'icon' => 'login'],
    ]);

    $tilesAuth = array_merge($tilesLegal, [
        ['href' => route('dashboard'), 'label' => 'My dashboard', 'icon' => 'dashboard'],
        ['href' => route('marketplace.index'), 'label' => 'Find freelancers', 'icon' => 'grid'],
        ['href' => route('payments.create'), 'label' => 'Pay now', 'icon' => 'card'],
        ['logout' => true, 'label' => 'Log out', 'icon' => 'logout'],
    ]);
@endphp

@if (! $admin)
    <nav class="mobile-dock fixed bottom-0 left-0 right-0 z-50 border-t border-white/10 bg-slate-950/95 backdrop-blur-md lg:hidden mobile-dock-safe" aria-label="Website navigation">
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
                <a href="{{ $payHref }}" class="mobile-dock-fab absolute bottom-full left-1/2 z-[51] mb-1.5 flex h-[3.25rem] w-[3.25rem] -translate-x-1/2 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-500 via-indigo-600 to-violet-600 text-xs font-extrabold text-white shadow-lg shadow-indigo-500/30 ring-4 ring-slate-950 transition hover:brightness-110 active:scale-95" aria-label="{{ $payFabLabel }}">
                    {{ $payFabLabel }}
                </a>
            </div>
            @guest
                <a href="{{ route('login') }}" class="mobile-dock-item flex flex-col items-center justify-end gap-0.5 pb-1.5 pt-1 text-slate-400 hover:text-white {{ request()->routeIs('login') ? 'text-cyan-300' : '' }}">
                    <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span class="text-[10px] font-semibold leading-none">Log in</span>
                </a>
            @else
                <a href="{{ $dashHref }}" class="mobile-dock-item flex flex-col items-center justify-end gap-0.5 pb-1.5 pt-1 text-slate-400 hover:text-white {{ request()->routeIs('dashboard', 'kyc.*') ? 'text-cyan-300' : '' }}">
                    <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25A2.25 2.25 0 018.25 10.5H6A2.25 2.25 0 013.75 8.25V6zM13.5 9.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25h-2.25A2.25 2.25 0 0113.5 18V9.75z"/></svg>
                    <span class="text-[10px] font-semibold leading-none">Dashboard</span>
                </a>
            @endguest
            <button type="button" data-open-dialog="{{ $dialogId }}" class="mobile-dock-item flex w-full flex-col items-center justify-end gap-0.5 pb-1.5 pt-1 text-slate-400 hover:text-white">
                <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7"/></svg>
                <span class="text-[10px] font-semibold leading-none">Menu</span>
            </button>
        </div>
    </nav>

    @include('partials.mobile-explore-dialog', [
        'dialogId' => $dialogId,
        'skin' => 'dark',
        'tiles' => auth()->check() ? $tilesAuth : $tilesGuest,
    ])
@endif
