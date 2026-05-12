<?php

namespace App\Support;

class OpsNotification
{
    public static function recipient(): ?string
    {
        $email = trim((string) config('paybycc.ops_notification_email', ''));

        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return null;
        }

        return $email;
    }
}
