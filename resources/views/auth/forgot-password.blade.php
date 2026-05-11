@extends('layouts.guest')

@section('title', 'Forgot password — '.config('app.name'))

@section('guest_hero')
    <p class="text-xs font-semibold uppercase tracking-wider text-indigo-400">Account recovery</p>
    <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">Reset your password</h1>
    <p class="max-w-md text-sm leading-relaxed text-slate-400">We’ll send a secure link to your email so you can choose a new password.</p>
@endsection

@section('content')
    <h2 class="text-center text-xl font-semibold tracking-tight text-white">Forgot password</h2>
    <p class="mt-1.5 text-center text-sm text-slate-400">Enter the email on your account</p>

    <form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <label for="email" class="auth-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                class="auth-input @error('email') auth-input-error @enderror">
            @error('email')
                <p class="auth-error-text">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="auth-btn-primary">
            Send reset link
        </button>
    </form>

    <p class="mt-8 text-center text-sm text-slate-400">
        <a href="{{ route('login') }}" class="auth-link">Back to log in</a>
    </p>
@endsection
