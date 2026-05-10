<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
    <div class="flex min-h-screen flex-col items-center justify-center px-4 py-10">
        <div class="w-full max-w-md">
            <a href="{{ route('home') }}" class="mb-8 block text-center text-xl font-semibold tracking-tight text-indigo-600">{{ config('app.name') }}</a>

            @if (session('status'))
                <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                @yield('content')
            </div>

            @yield('guest-footer')

            <nav class="mt-10 flex flex-wrap justify-center gap-x-4 gap-y-2 text-center text-xs text-slate-500">
                <a href="{{ route('home') }}" class="hover:text-indigo-600">Home</a>
                <a href="{{ route('about') }}" class="hover:text-indigo-600">About</a>
                <a href="{{ route('contact') }}" class="hover:text-indigo-600">Contact</a>
                <a href="{{ route('privacy') }}" class="hover:text-indigo-600">Privacy</a>
                <a href="{{ route('terms') }}" class="hover:text-indigo-600">Terms</a>
            </nav>
        </div>
    </div>
</body>
</html>
