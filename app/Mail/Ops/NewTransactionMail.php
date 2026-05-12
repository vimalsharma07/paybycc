<?php

namespace App\Mail\Ops;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewTransactionMail extends Mailable implements ShouldQueue, ShouldQueueAfterCommit
{
    use Queueable, SerializesModels;

    public function __construct(public Transaction $transaction) {}

    public function envelope(): Envelope
    {
        $site = config('app.name', 'App');
        $type = $this->transaction->type_label ?? $this->transaction->type;
        $amount = (string) $this->transaction->amount;

        return new Envelope(
            subject: "[{$site}] New transaction — {$type} · {$amount} {$this->transaction->currency}",
        );
    }

    public function content(): Content
    {
        $this->transaction->loadMissing('user', 'payment.gateway', 'bank');

        return new Content(
            view: 'mail.ops.new-transaction',
        );
    }
}
