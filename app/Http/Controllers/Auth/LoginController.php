<?php

namespace App\Http\Controllers\Auth;

use App\Enums\LogLevel;
use App\Http\Controllers\Controller;
use App\Services\Logging\FlowLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request, FlowLog $flow): RedirectResponse
    {
        try {
            $credentials = $request->validate([
                'email' => ['required', 'string', 'email'],
                'password' => ['required', 'string'],
                'remember' => ['nullable', 'boolean'],
            ]);
        } catch (ValidationException $e) {
            $flow->auth('login.validation_failed', 'Login validation failed', $flow->validationErrors($e), level: LogLevel::Notice);

            throw $e;
        }

        $remember = $request->boolean('remember');

        $flow->auth('login.attempt', 'Login attempt', [
            'email' => $credentials['email'],
            'remember' => $remember,
        ]);

        if (! Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $remember)) {
            $flow->auth('login.failed', 'Login failed — invalid credentials', [
                'email' => $credentials['email'],
            ], level: LogLevel::Warning);

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'These credentials do not match our records.']);
        }

        $user = Auth::user();
        $request->session()->regenerate();

        $redirect = $this->redirectPath($user);

        $flow->auth('login.success', 'Login successful', $flow->userContext($user, [
            'remember' => $remember,
            'redirect' => $redirect,
        ]), $user);

        return redirect()->intended($redirect);
    }

    public function destroy(Request $request, FlowLog $flow): RedirectResponse
    {
        $user = Auth::user();

        if ($user !== null) {
            $flow->auth('logout.success', 'User logged out', $flow->userContext($user), $user);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function redirectPath(\App\Models\User $user): string
    {
        if ($user->is_admin) {
            return route('admin.dashboard');
        }

        if (! $user->hasActiveKyc()) {
            return route('kyc.index');
        }

        return route('dashboard');
    }
}
