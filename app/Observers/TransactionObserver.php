<?php

namespace App\Observers;

use App\Mail\Ops\NewTransactionMail;
use App\Models\Transaction;
use App\Support\OpsNotification;
use Illuminate\Support\Facades\Mail;

class TransactionObserver
{
    public function created(Transaction $transaction): void
    {
        $to = OpsNotification::recipient();
        if ($to === null) {
            return;
        }

        Mail::to($to)->send(new NewTransactionMail($transaction));
    }
}
