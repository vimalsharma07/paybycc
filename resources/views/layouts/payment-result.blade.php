<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $siteSettings->displayName())</title>
    @include('partials.head-styles')
    <link rel="stylesheet" href="{{ asset('css/payment-result.css') }}">
</head>
@php
    $appDock = ! auth()->user()->is_admin;
@endphp
<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">
    <header class="border-b border-slate-200/80 bg-white/90 backdrop-blur-sm">
        <div class="mx-auto flex max-w-3xl items-center justify-center px-4 py-4 sm:px-6">
            @include('partials.site-brand-header', ['href' => route('dashboard'), 'variant' => 'light', 'wrapperClass' => ''])
        </div>
    </header>

    <main class="mx-auto max-w-3xl px-4 py-8 sm:px-6 sm:py-12 {{ $appDock ? 'pb-28 lg:pb-12' : '' }}">
        @yield('content')
    </main>

    @include('partials.mobile-dock-app')
</body>
</html>
