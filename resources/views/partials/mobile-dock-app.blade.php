{{-- App shell: bottom navigation (mobile only, non-admin). --}}
@php
    $u = auth()->user();
    $isAdmin = $u->is_admin;
    $kyc = $u->hasActiveKyc();
    $payHref = $kyc ? route('payments.create') : route('kyc.index');
@endphp

@if (! $isAdmin)
    <nav class="mobile-dock fixed bottom-0 left-0 right-0 z-50 border-t border-slate-200/90 bg-white/95 backdrop-blur-md lg:hidden mobile-dock-safe" aria-label="App navigation">
        @if ($kyc)
            <div class="relative mx-auto grid h-[4.25rem] max-w-lg grid-cols-5 items-end px-0.5 pb-1 pt-0.5">
                <a href="{{ route('dashboard') }}" class="mobile-dock-item flex flex-col items-center justify-end gap-0.5 pb-1.5 pt-1 text-slate-600 hover:text-indigo-600 {{ request()->routeIs('dashboard') ? 'text-indigo-600' : '' }}">
                    <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 22V12h6v10"/></svg>
                    <span class="text-[10px] font-semibold leading-none">Home</span>
                </a>
                <a href="{{ route('wallet.index') }}" class="mobile-dock-item flex flex-col items-center justify-end gap-0.5 pb-1.5 pt-1 text-slate-600 hover:text-indigo-600 {{ request()->routeIs('wallet.*') ? 'text-indigo-600' : '' }}">
                    <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H12a2.25 2.25 0 00-2.25 2.25v6.75A2.25 2.25 0 009.75 21.75h-1.5A2.25 2.25 0 016 19.5V12a2.25 2.25 0 00-2.25-2.25H3"/><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 3.375H9.75A2.25 2.25 0 0112 5.625v.75m0 0h3.75m-3.75 0H9m0 0H5.625A2.25 2.25 0 003 8.25v.375c0 .621.504 1.125 1.125 1.125h16.5c.621 0 1.125-.504 1.125-1.125V8.25A2.25 2.25 0 0019.875 6H16.5"/></svg>
                    <span class="text-[10px] font-semibold leading-none">Wallet</span>
                </a>
                <div class="relative flex justify-center">
                    <a href="{{ $payHref }}" class="mobile-dock-fab absolute -top-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-500 via-indigo-600 to-violet-600 text-xs font-extrabold text-white shadow-lg shadow-indigo-500/40 ring-4 ring-white transition hover:brightness-110 active:scale-95 {{ request()->routeIs('payments.*') ? 'ring-indigo-200' : '' }}" aria-label="Pay now">
                        Pay
                    </a>
                </div>
                <a href="{{ route('banks.index') }}" class="mobile-dock-item flex flex-col items-center justify-end gap-0.5 pb-1.5 pt-1 text-slate-600 hover:text-indigo-600 {{ request()->routeIs('banks.*') ? 'text-indigo-600' : '' }}">
                    <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-4h6v4"/></svg>
                    <span class="text-[10px] font-semibold leading-none">Banks</span>
                </a>
                <details class="relative flex flex-col items-center justify-end pb-0.5">
                    <summary class="mobile-dock-item flex cursor-pointer list-none flex-col items-center gap-0.5 pb-1.5 pt-1 text-slate-600 marker:content-none hover:text-indigo-600 [&::-webkit-details-marker]:hidden">
                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><circle cx="5" cy="12" r="1.5" fill="currentColor"/><circle cx="12" cy="12" r="1.5" fill="currentColor"/><circle cx="19" cy="12" r="1.5" fill="currentColor"/></svg>
                        <span class="text-[10px] font-semibold leading-none">More</span>
                    </summary>
                    <div class="absolute right-0 top-full z-[60] mt-2 w-56 overflow-hidden rounded-xl border border-slate-200 bg-white py-1 shadow-xl ring-1 ring-black/5">
                        <p class="truncate border-b border-slate-100 px-4 py-2.5 text-xs font-medium text-slate-500">{{ $u->name }}</p>
                        <a href="{{ route('home') }}" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">Marketing site</a>
                        <a href="{{ route('contact') }}" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">Contact</a>
                        <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2.5 text-left text-sm font-medium text-red-600 hover:bg-red-50">Log out</button>
                        </form>
                    </div>
                </details>
            </div>
        @else
            <div class="relative mx-auto flex h-[4.25rem] max-w-lg items-end justify-between px-4 pb-1 pt-0.5">
                <a href="{{ route('dashboard') }}" class="mobile-dock-item flex flex-col items-center gap-0.5 pb-1.5 text-slate-600 hover:text-indigo-600">
                    <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 22V12h6v10"/></svg>
                    <span class="text-[10px] font-semibold leading-none">Home</span>
                </a>
                <a href="{{ $payHref }}" class="mobile-dock-fab absolute left-1/2 top-1/2 flex h-14 w-14 -translate-x-1/2 -translate-y-[70%] items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-500 via-indigo-600 to-violet-600 text-xs font-extrabold text-white shadow-lg shadow-indigo-500/40 ring-4 ring-white transition hover:brightness-110 active:scale-95" aria-label="Complete KYC">
                    KYC
                </a>
                <details class="relative flex flex-col items-center pb-0.5">
                    <summary class="mobile-dock-item flex cursor-pointer list-none flex-col items-center gap-0.5 pb-1.5 marker:content-none hover:text-indigo-600 [&::-webkit-details-marker]:hidden">
                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><circle cx="5" cy="12" r="1.5" fill="currentColor"/><circle cx="12" cy="12" r="1.5" fill="currentColor"/><circle cx="19" cy="12" r="1.5" fill="currentColor"/></svg>
                        <span class="text-[10px] font-semibold leading-none">More</span>
                    </summary>
                    <div class="absolute right-0 top-full z-[60] mt-2 w-56 overflow-hidden rounded-xl border border-slate-200 bg-white py-1 shadow-xl ring-1 ring-black/5">
                        <p class="truncate border-b border-slate-100 px-4 py-2.5 text-xs font-medium text-slate-500">{{ $u->name }}</p>
                        <a href="{{ route('home') }}" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">Marketing site</a>
                        <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2.5 text-left text-sm font-medium text-red-600 hover:bg-red-50">Log out</button>
                        </form>
                    </div>
                </details>
            </div>
        @endif
    </nav>
@endif
