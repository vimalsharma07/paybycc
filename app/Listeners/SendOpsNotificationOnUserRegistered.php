<?php

namespace App\Listeners;

use App\Mail\Ops\NewUserRegisteredMail;
use App\Support\OpsNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;

class SendOpsNotificationOnUserRegistered
{
    public function handle(Registered $event): void
    {
        $to = OpsNotification::recipient();
        if ($to === null) {
            return;
        }

        Mail::to($to)->send(new NewUserRegisteredMail($event->user));
    }
}
