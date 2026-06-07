<?php

namespace App\Observers;

use App\Mail\Ops\NewTransactionMail;
use App\Models\Transaction;
use App\Services\Logging\FlowLog;
use App\Support\OpsNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class TransactionObserver
{
    public function __construct(
        protected FlowLog $flow,
    ) {}

    public function created(Transaction $transaction): void
    {
        $this->flow->transaction(
            'transaction.created',
            'Ledger transaction recorded',
            $this->flow->transactionContext($transaction),
            $transaction,
        );

        $to = OpsNotification::recipient();
        if ($to === null) {
            return;
        }

        $id = (int) $transaction->id;

        DB::afterCommit(function () use ($to, $id): void {
            $fresh = Transaction::query()->find($id);
            if ($fresh === null) {
                return;
            }

            Mail::to($to)->send(new NewTransactionMail($fresh));
        });
    }
}
