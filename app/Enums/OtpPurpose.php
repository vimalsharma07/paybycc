<?php

namespace App\Enums;

enum OtpPurpose: string
{
    case Registration = 'registration';

    public function configKey(): string
    {
        return $this->value;
    }
}
