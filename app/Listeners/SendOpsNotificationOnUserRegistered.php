<?php

namespace App\Listeners;

use App\Mail\Ops\NewUserRegisteredMail;
use App\Support\OpsNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendOpsNotificationOnUserRegistered implements ShouldQueue, ShouldQueueAfterCommit
{
    use InteractsWithQueue;

    public function handle(Registered $event): void
    {
        $to = OpsNotification::recipient();
        if ($to === null) {
            return;
        }

        Mail::to($to)->send(new NewUserRegisteredMail($event->user));
    }
}
