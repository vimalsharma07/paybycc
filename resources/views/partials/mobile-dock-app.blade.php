{{-- App shell: bottom navigation (mobile only, non-admin). "Menu" opens icon grid sheet. --}}
@php
    $u = auth()->user();
    $isAdmin = $u->is_admin;
    $canPay = $u->canUsePlatform();
    $canPayout = $u->canReceivePayouts();
    $payHref = $canPay ? route('payments.create') : route('kyc.index');
    $dialogId = 'mobile-explore-app';

    $tilesBase = [
        ['href' => route('account.payments'), 'label' => 'Payments', 'icon' => 'card'],
        ['href' => route('account.transactions'), 'label' => 'Transactions', 'icon' => 'chart'],
        ['href' => route('marketplace.index'), 'label' => 'Marketplace', 'icon' => 'grid'],
        ['href' => route('banks.index'), 'label' => 'Banks', 'icon' => 'bank'],
        ['href' => route('profile.show'), 'label' => 'Profile', 'icon' => 'user'],
        ['href' => route('privacy'), 'label' => 'Privacy', 'icon' => 'shield'],
        ['href' => route('terms'), 'label' => 'Terms', 'icon' => 'document'],
        ['href' => route('contact'), 'label' => 'Contact', 'icon' => 'mail'],
        ['href' => route('home'), 'label' => 'Website', 'icon' => 'globe'],
    ];

    if ($u->isSeller()) {
        array_splice($tilesBase, 2, 0, [['href' => route('account.settlements'), 'label' => 'Received', 'icon' => 'inbox']]);
    }

    if ($u->canCreatePaymentLinks()) {
        array_splice($tilesBase, 2, 0, [['href' => route('payment-links.index'), 'label' => 'Payment links', 'icon' => 'link']]);
    }

    $tilesAppKyc = array_merge($tilesBase, [
        ['logout' => true, 'label' => 'Log out', 'icon' => 'logout'],
    ]);

    $tilesAppBrowse = array_merge($tilesBase, [
        ['href' => route('kyc.index'), 'label' => 'Complete KYC', 'icon' => 'user-plus'],
        ['logout' => true, 'label' => 'Log out', 'icon' => 'logout'],
    ]);

    $tilesAppNoKyc = array_merge($tilesBase, [
        ['href' => route('kyc.index'), 'label' => 'Complete KYC', 'icon' => 'user-plus'],
        ['logout' => true, 'label' => 'Log out', 'icon' => 'logout'],
    ]);
@endphp

@if (! $isAdmin)
    <nav class="mobile-dock fixed bottom-0 left-0 right-0 z-50 border-t border-slate-200/90 bg-white/95 backdrop-blur-md lg:hidden mobile-dock-safe" aria-label="App navigation">
        @if ($canPayout)
            <div class="relative mx-auto grid min-h-[4.75rem] max-w-lg grid-cols-5 items-end px-0.5 pb-1.5 pt-3">
                <a href="{{ route('dashboard') }}" class="mobile-dock-item flex flex-col items-center justify-end gap-0.5 pb-1.5 pt-1 {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-slate-600 hover:text-indigo-600' }}">
                    <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 22V12h6v10"/></svg>
                    <span class="text-[10px] font-semibold leading-none">Home</span>
                </a>
                @if ($u->canCreatePaymentLinks())
                    <a href="{{ route('payment-links.index') }}" class="mobile-dock-item flex flex-col items-center justify-end gap-0.5 pb-1.5 pt-1 {{ request()->routeIs('payment-links.*') ? 'text-indigo-600' : 'text-slate-600 hover:text-indigo-600' }}">
                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/></svg>
                        <span class="text-[10px] font-semibold leading-none">Links</span>
                    </a>
                @else
                    <a href="{{ route('account.transactions') }}" class="mobile-dock-item flex flex-col items-center justify-end gap-0.5 pb-1.5 pt-1 {{ request()->routeIs('account.transactions') ? 'text-indigo-600' : 'text-slate-600 hover:text-indigo-600' }}">
                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                        <span class="text-[10px] font-semibold leading-none">Activity</span>
                    </a>
                @endif
                <div class="relative z-10 flex min-h-[2.75rem] justify-center">
                    <a href="{{ $payHref }}" class="mobile-dock-fab absolute bottom-full left-1/2 z-[51] mb-1.5 flex h-[3.25rem] w-[3.25rem] -translate-x-1/2 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-500 via-indigo-600 to-violet-600 text-xs font-extrabold text-white shadow-lg shadow-indigo-500/40 ring-4 ring-white transition hover:brightness-110 active:scale-95 {{ request()->routeIs('payments.*') ? 'ring-indigo-200' : '' }}" aria-label="Pay now">Pay</a>
                </div>
                <a href="{{ $u->isSeller() ? route('account.settlements') : route('banks.index') }}" class="mobile-dock-item flex flex-col items-center justify-end gap-0.5 pb-1.5 pt-1 {{ request()->routeIs('account.settlements', 'banks.*') ? 'text-indigo-600' : 'text-slate-600 hover:text-indigo-600' }}">
                    @if ($u->isSeller())
                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859M12 3v8.25m0 0l-3-3m3 3l3-3"/></svg>
                        <span class="text-[10px] font-semibold leading-none">Received</span>
                    @else
                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-4h6v4"/></svg>
                        <span class="text-[10px] font-semibold leading-none">Banks</span>
                    @endif
                </a>
                <button type="button" data-open-dialog="{{ $dialogId }}" class="mobile-dock-item flex w-full flex-col items-center justify-end gap-0.5 pb-1.5 pt-1 text-slate-600 hover:text-indigo-600" aria-label="Open menu">
                    <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7"/></svg>
                    <span class="text-[10px] font-semibold leading-none">Menu</span>
                </button>
            </div>
        @elseif ($canPay)
            <div class="relative mx-auto grid min-h-[4.75rem] max-w-lg grid-cols-5 items-end px-0.5 pb-1.5 pt-3">
                <a href="{{ route('dashboard') }}" class="mobile-dock-item flex flex-col items-center justify-end gap-0.5 pb-1.5 pt-1 text-slate-600 hover:text-indigo-600 {{ request()->routeIs('dashboard') ? 'text-indigo-600' : '' }}">
                    <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 22V12h6v10"/></svg>
                    <span class="text-[10px] font-semibold leading-none">Home</span>
                </a>
                <a href="{{ route('kyc.index') }}" class="mobile-dock-item flex flex-col items-center justify-end gap-0.5 pb-1.5 pt-1 text-slate-600 hover:text-indigo-600 {{ request()->routeIs('kyc.*') ? 'text-indigo-600' : '' }}" title="Complete KYC to receive payouts">
                    <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-4h6v4"/></svg>
                    <span class="text-[10px] font-semibold leading-none">KYC</span>
                </a>
                <div class="relative z-10 flex min-h-[2.75rem] justify-center">
                    <a href="{{ $payHref }}" class="mobile-dock-fab absolute bottom-full left-1/2 z-[51] mb-1.5 flex h-[3.25rem] w-[3.25rem] -translate-x-1/2 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-500 via-indigo-600 to-violet-600 text-xs font-extrabold text-white shadow-lg shadow-indigo-500/40 ring-4 ring-white transition hover:brightness-110 active:scale-95 {{ request()->routeIs('payments.*') ? 'ring-indigo-200' : '' }}" aria-label="Pay now">
                        Pay
                    </a>
                </div>
                <a href="{{ route('account.payments') }}" class="mobile-dock-item flex flex-col items-center justify-end gap-0.5 pb-1.5 pt-1 {{ request()->routeIs('account.payments') ? 'text-indigo-600' : 'text-slate-600 hover:text-indigo-600' }}">
                    <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
                    <span class="text-[10px] font-semibold leading-none">Payments</span>
                </a>
                <button type="button" data-open-dialog="{{ $dialogId }}" class="mobile-dock-item flex w-full flex-col items-center justify-end gap-0.5 pb-1.5 pt-1 text-slate-600 hover:text-indigo-600">
                    <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7"/></svg>
                    <span class="text-[10px] font-semibold leading-none">Menu</span>
                </button>
            </div>
        @else
            <div class="relative mx-auto grid min-h-[4.75rem] max-w-lg grid-cols-5 items-end px-0.5 pb-1.5 pt-3">
                <a href="{{ route('dashboard') }}" class="mobile-dock-item flex flex-col items-center justify-end gap-0.5 pb-1.5 pt-1 text-slate-600 hover:text-indigo-600 {{ request()->routeIs('dashboard') ? 'text-indigo-600' : '' }}">
                    <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 22V12h6v10"/></svg>
                    <span class="text-[10px] font-semibold leading-none">Home</span>
                </a>
                <a href="{{ route('about') }}" class="mobile-dock-item flex flex-col items-center justify-end gap-0.5 pb-1.5 pt-1 text-slate-600 hover:text-indigo-600 {{ request()->routeIs('about') ? 'text-indigo-600' : '' }}">
                    <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                    <span class="text-[10px] font-semibold leading-none">About</span>
                </a>
                <div class="relative z-10 flex min-h-[2.75rem] justify-center">
                    <a href="{{ $payHref }}" class="mobile-dock-fab absolute bottom-full left-1/2 z-[51] mb-1.5 flex h-[3.25rem] w-[3.25rem] -translate-x-1/2 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-500 via-indigo-600 to-violet-600 text-xs font-extrabold text-white shadow-lg shadow-indigo-500/40 ring-4 ring-white transition hover:brightness-110 active:scale-95 {{ request()->routeIs('kyc.*') ? 'ring-indigo-200' : '' }}" aria-label="Complete KYC">
                        KYC
                    </a>
                </div>
                <a href="{{ route('home') }}" class="mobile-dock-item flex flex-col items-center justify-end gap-0.5 pb-1.5 pt-1 text-slate-600 hover:text-indigo-600 {{ request()->routeIs('home') ? 'text-indigo-600' : '' }}">
                    <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3"/></svg>
                    <span class="text-[10px] font-semibold leading-none">Site</span>
                </a>
                <button type="button" data-open-dialog="{{ $dialogId }}" class="mobile-dock-item flex w-full flex-col items-center justify-end gap-0.5 pb-1.5 pt-1 text-slate-600 hover:text-indigo-600">
                    <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7"/></svg>
                    <span class="text-[10px] font-semibold leading-none">Menu</span>
                </button>
            </div>
        @endif
    </nav>

    @include('partials.mobile-explore-dialog', [
        'dialogId' => $dialogId,
        'skin' => 'light',
        'tiles' => $canPayout ? $tilesAppKyc : ($canPay ? $tilesAppBrowse : $tilesAppNoKyc),
    ])
@endif
