<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBankRequest;
use App\Http\Requests\UpdateBankRequest;
use App\Models\Bank;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BankController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if ($r = $this->redirectAdmin()) {
            return $r;
        }

        $banks = auth()->user()
            ->banks()
            ->orderByDesc('is_primary')
            ->orderByDesc('updated_at')
            ->get();

        return view('banks.index', compact('banks'));
    }

    public function store(StoreBankRequest $request): RedirectResponse
    {
        if ($r = $this->redirectAdmin()) {
            return $r;
        }

        $user = $request->user();

        DB::transaction(function () use ($request, $user) {
            $data = $request->validated();

            $bank = $user->banks()->create([
                'bank_name' => $data['bank_name'],
                'account_holder_name' => $data['account_holder_name'],
                'account_no' => $data['account_no'],
                'ifsc' => $data['ifsc'],
                'status' => 'active',
                'is_primary' => $data['is_primary'],
            ]);

            if ($bank->is_primary) {
                $user->banks()->whereKeyNot($bank->id)->update(['is_primary' => false]);
            }
        });

        return redirect()->route('banks.index')->with('status', 'Bank account added.');
    }

    public function update(UpdateBankRequest $request, Bank $bank): RedirectResponse
    {
        if ($r = $this->redirectAdmin()) {
            return $r;
        }

        $this->ensureOwnsBank($bank);

        DB::transaction(function () use ($request, $bank) {
            $data = $request->validated();

            $bank->fill([
                'bank_name' => $data['bank_name'],
                'account_holder_name' => $data['account_holder_name'],
                'account_no' => $data['account_no'],
                'ifsc' => $data['ifsc'],
                'status' => $data['status'],
                'is_primary' => $data['is_primary'],
            ]);
            $bank->save();

            if ($bank->is_primary) {
                $request->user()->banks()->whereKeyNot($bank->id)->update(['is_primary' => false]);
            }
        });

        return redirect()->route('banks.index')->with('status', 'Bank account updated.');
    }

    public function destroy(Bank $bank): RedirectResponse
    {
        if ($r = $this->redirectAdmin()) {
            return $r;
        }

        $this->ensureOwnsBank($bank);
        $bank->delete();

        return redirect()->route('banks.index')->with('status', 'Bank account removed.');
    }

    protected function ensureOwnsBank(Bank $bank): void
    {
        abort_unless($bank->user_id === auth()->id(), 403);
    }

    protected function redirectAdmin(): ?RedirectResponse
    {
        if (auth()->user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        return null;
    }
}
