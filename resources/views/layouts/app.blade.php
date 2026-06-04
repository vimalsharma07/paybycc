<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#4f46e5">
    <title>@yield('title', $siteSettings->displayName())</title>
    @include('partials.head-styles')
    @stack('styles')
</head>
@php
    $appDock = ! auth()->user()->is_admin;
    $pageTitle = trim($__env->yieldContent('page_heading'));
@endphp
<body class="min-h-screen bg-slate-100 font-sans text-slate-900 antialiased">
    <div class="flex min-h-screen min-h-[100dvh]">
        @include('partials.app-sidebar')

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="app-topbar sticky top-0 z-40 border-b border-slate-200/90 bg-white/90 backdrop-blur-md lg:hidden">
                <div class="flex items-center justify-between gap-3 px-4 py-3">
                    @include('partials.site-brand-header', ['href' => route('dashboard'), 'variant' => 'light', 'wrapperClass' => ''])
                    <a href="{{ route('profile.show') }}" class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-800 ring-2 ring-indigo-200" aria-label="Profile">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </a>
                </div>
                @hasSection('page_heading')
                    <div class="border-t border-slate-100 px-4 py-2">
                        <h1 class="text-base font-bold text-slate-900">@yield('page_heading')</h1>
                    </div>
                @endif
            </header>

            <header class="app-topbar hidden border-b border-slate-200/80 bg-white/80 px-8 py-5 backdrop-blur-sm lg:block">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h1 class="text-xl font-bold tracking-tight text-slate-900">
                            @hasSection('page_heading')
                                @yield('page_heading')
                            @else
                                {{ $pageTitle !== '' ? $pageTitle : 'Overview' }}
                            @endif
                        </h1>
                        @hasSection('page_subheading')
                            <p class="mt-1 text-sm text-slate-600">@yield('page_subheading')</p>
                        @endif
                    </div>
                    @if (auth()->user()->canUsePlatform())
                        <a href="{{ route('payments.create') }}" class="pay-now-btn inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-cyan-500 via-indigo-600 to-violet-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg transition hover:brightness-110">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                            Pay now
                        </a>
                    @endif
                </div>
            </header>

            <main class="app-main-shell min-w-0 flex-1 px-4 py-5 sm:px-6 lg:px-8 lg:py-8 {{ $appDock ? 'pb-28 lg:pb-8' : '' }}">
                @if (session('status'))
                    <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900">
                        {{ session('status') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @include('partials.mobile-dock-app')
    @stack('scripts')
</body>
</html>
