@extends('layouts.guest')

@section('title', 'Reset password — '.config('app.name'))

@section('guest_hero')
    <p class="text-xs font-semibold uppercase tracking-wider text-indigo-400">Almost there</p>
    <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">Choose a new password</h1>
    <p class="max-w-md text-sm leading-relaxed text-slate-400">Pick something strong and unique — you’ll use it for all future logins.</p>
@endsection

@section('content')
    <h2 class="text-center text-xl font-semibold tracking-tight text-white">New password</h2>
    <p class="mt-1.5 text-center text-sm text-slate-400">Confirm your email and set a new password</p>

    <form method="POST" action="{{ route('password.update') }}" class="mt-8 space-y-5">
        @csrf

        <input type="hidden" name="token" value="{{ request()->route('token') }}">

        <div>
            <label for="email" class="auth-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
                class="auth-input @error('email') auth-input-error @enderror">
            @error('email')
                <p class="auth-error-text">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="auth-label">New password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                class="auth-input @error('password') auth-input-error @enderror">
            @error('password')
                <p class="auth-error-text">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="auth-label">Confirm password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                class="auth-input">
        </div>

        <button type="submit" class="auth-btn-primary">
            Update password
        </button>
    </form>

    <p class="mt-8 text-center text-sm text-slate-400">
        <a href="{{ route('login') }}" class="auth-link">Back to log in</a>
    </p>
@endsection
