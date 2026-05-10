<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateBankRequest;
use App\Models\Bank;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BankController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $banks = Bank::query()
            ->with(['user:id,user_code,name,email'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($query) use ($q) {
                    $query
                        ->where('bank_name', 'like', '%'.$q.'%')
                        ->orWhere('ifsc', 'like', '%'.$q.'%')
                        ->orWhere('account_holder_name', 'like', '%'.$q.'%')
                        ->orWhereHas('user', function ($query) use ($q) {
                            $query
                                ->where('name', 'like', '%'.$q.'%')
                                ->orWhere('email', 'like', '%'.$q.'%')
                                ->orWhere('user_code', 'like', '%'.$q.'%');
                        });
                });
            })
            ->orderByDesc('updated_at')
            ->paginate(25)
            ->withQueryString();

        return view('admin.banks.index', [
            'banks' => $banks,
            'q' => $q,
        ]);
    }

    public function edit(Bank $bank): View
    {
        $bank->load('user:id,user_code,name,email');

        return view('admin.banks.edit', ['bank' => $bank]);
    }

    public function update(UpdateBankRequest $request, Bank $bank): RedirectResponse
    {
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
                Bank::query()
                    ->where('user_id', $bank->user_id)
                    ->whereKeyNot($bank->id)
                    ->update(['is_primary' => false]);
            }
        });

        return redirect()
            ->route('admin.banks.edit', $bank)
            ->with('status', 'Bank account updated successfully.');
    }
}
