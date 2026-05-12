<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $transactions = Transaction::query()
            ->with([
                'user:id,name,email',
                'payment:id,amount,status,gateway_reference,remark',
                'bank:id,account_holder_name',
            ])
            ->when($q !== '', function ($query) use ($q) {
                $query->whereHas('user', function ($query) use ($q) {
                    $query
                        ->where('name', 'like', '%'.$q.'%')
                        ->orWhere('email', 'like', '%'.$q.'%');
                });
            })
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.transactions.index', [
            'transactions' => $transactions,
            'q' => $q,
        ]);
    }
}
