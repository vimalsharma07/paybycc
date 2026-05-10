<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KycController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = auth()->user();

        if ($user->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasActiveKyc()) {
            return redirect()->route('dashboard');
        }

        return view('kyc.index', ['user' => $user]);
    }

    public function storePan(Request $request): RedirectResponse
    {
        $user = auth()->user();

        if ($user->is_admin || $user->hasActiveKyc()) {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'pan' => ['required', 'string', 'size:10', 'regex:/^[A-Za-z]{5}[0-9]{4}[A-Za-z]{1}$/'],
            'pan_name' => ['required', 'string', 'max:255'],
            'aadhar' => ['nullable', 'string', 'size:12', 'regex:/^\d{12}$/'],
        ]);

        $user->pan = strtoupper($validated['pan']);
        $user->pan_name = $validated['pan_name'];
        if (! empty($validated['aadhar'])) {
            $user->aadhar = $validated['aadhar'];
        }
        $user->kyc_status = User::KYC_ACTIVE;
        $user->save();

        return redirect()->route('dashboard')->with('status', 'KYC completed successfully.');
    }
}
