<?php

namespace App\Http\Controllers;

use App\Enums\LogLevel;
use App\Http\Requests\StoreBankRequest;
use App\Http\Requests\UpdateBankRequest;
use App\Models\Bank;
use App\Services\Logging\FlowLog;
use App\Services\Wallet\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BankController extends Controller
{
    public function __construct(
        protected WalletService $wallets,
    ) {}

    public function index(FlowLog $flow): View|RedirectResponse
    {
        if ($r = $this->redirectAdmin()) {
            return $r;
        }

        $user = auth()->user();
        $this->wallets->ensureForUser($user);
        $this->wallets->syncDefaultBankFromPrimary($user);

        $banks = $user->banks()
            ->orderByDesc('is_primary')
            ->orderByDesc('updated_at')
            ->get();

        $flow->bank('bank.index.view', 'Bank accounts page opened', array_merge(
            $flow->userContext($user),
            ['bank_count' => $banks->count()]
        ), $user);

        return view('banks.index', compact('banks'));
    }

    public function store(StoreBankRequest $request, FlowLog $flow): RedirectResponse
    {
        if ($r = $this->redirectAdmin()) {
            return $r;
        }

        $user = $request->user();
        $data = $request->validated();

        $flow->bank('bank.store.attempt', 'Add bank attempt', array_merge(
            $flow->userContext($user),
            $flow->bankContext($data['ifsc'], $data['bank_name']),
            $flow->maskedBankAccount($data['account_no']),
            ['is_primary' => $data['is_primary']]
        ), $user);

        $bank = null;

        try {
            DB::transaction(function () use ($request, $user, $data, &$bank) {
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
        } catch (\Throwable $e) {
            $flow->bank('bank.store.exception', 'Add bank failed with exception', array_merge(
                $flow->userContext($user),
                $flow->bankContext($data['ifsc'], $data['bank_name']),
                ['error' => $e->getMessage()]
            ), $user, LogLevel::Error);

            throw $e;
        }

        $flow->bank('bank.store.success', 'Bank account added', array_merge(
            $flow->userContext($user),
            $flow->bankContext($bank->ifsc, $bank->bank_name),
            $flow->maskedBankAccount($bank->account_no),
            ['bank_id' => $bank->id, 'is_primary' => $bank->is_primary]
        ), $bank);

        if ($bank->is_primary) {
            $this->wallets->syncDefaultBankFromPrimary($user);
        }

        return redirect()->route('banks.index')->with('status', 'Bank account added.');
    }

    public function update(UpdateBankRequest $request, Bank $bank, FlowLog $flow): RedirectResponse
    {
        if ($r = $this->redirectAdmin()) {
            return $r;
        }

        $this->ensureOwnsBank($bank);
        $user = $request->user();

        $data = $request->validated();

        $flow->bank('bank.update.attempt', 'Update bank attempt', array_merge(
            $flow->userContext($user),
            ['bank_id' => $bank->id],
            $flow->bankContext($data['ifsc'], $data['bank_name']),
            ['status' => $data['status'], 'is_primary' => $data['is_primary']]
        ), $bank);

        try {
            DB::transaction(function () use ($request, $bank, $data) {
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
        } catch (\Throwable $e) {
            $flow->bank('bank.update.exception', 'Update bank failed', array_merge(
                $flow->userContext($user),
                ['bank_id' => $bank->id, 'error' => $e->getMessage()]
            ), $bank, LogLevel::Error);

            throw $e;
        }

        $flow->bank('bank.update.success', 'Bank account updated', array_merge(
            $flow->userContext($user),
            ['bank_id' => $bank->id, 'is_primary' => $bank->is_primary, 'status' => $bank->status]
        ), $bank);

        if ($bank->is_primary) {
            $this->wallets->syncDefaultBankFromPrimary($user);
        }

        return redirect()->route('banks.index')->with('status', 'Bank account updated.');
    }

    public function destroy(Bank $bank, FlowLog $flow): RedirectResponse
    {
        if ($r = $this->redirectAdmin()) {
            return $r;
        }

        $this->ensureOwnsBank($bank);
        $user = auth()->user();

        $flow->bank('bank.destroy.attempt', 'Remove bank attempt', array_merge(
            $flow->userContext($user),
            $flow->bankContext($bank->ifsc, $bank->bank_name),
            ['bank_id' => $bank->id]
        ), $bank);

        try {
            $bank->delete();
        } catch (\Throwable $e) {
            $flow->bank('bank.destroy.exception', 'Remove bank failed', array_merge(
                $flow->userContext($user),
                ['bank_id' => $bank->id, 'error' => $e->getMessage()]
            ), $bank, LogLevel::Error);

            throw $e;
        }

        $flow->bank('bank.destroy.success', 'Bank account removed', array_merge(
            $flow->userContext($user),
            ['bank_id' => $bank->id]
        ), $user);

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
