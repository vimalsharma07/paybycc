<?php

namespace App\Mail\Ops;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewUserRegisteredMail extends Mailable implements ShouldQueue, ShouldQueueAfterCommit
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        $site = config('app.name', 'App');

        return new Envelope(
            subject: "[{$site}] New user registered — {$this->user->email}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.ops.new-user',
        );
    }
}
