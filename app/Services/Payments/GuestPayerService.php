<?php

namespace App\Services\Payments;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GuestPayerService
{
    public function resolve(string $name, string $email, string $phone): User
    {
        $email = strtolower(trim($email));
        $phone = preg_replace('/\D/', '', $phone) ?? '';
        $name = trim($name);

        $existing = User::query()->where('email', $email)->first();
        if ($existing) {
            return $existing;
        }

        return User::create([
            'user_code' => $this->uniqueUserCode(),
            'name' => $name,
            'email' => $email,
            'phone' => $phone !== '' ? $phone : null,
            'password' => Hash::make(Str::random(32)),
            'is_admin' => false,
            'role' => 'customer',
            'kyc_status' => User::KYC_INCOMPLETE,
            'status' => 'active',
        ]);
    }

    protected function uniqueUserCode(): string
    {
        do {
            $code = 'GST-'.strtoupper(Str::random(8));
        } while (User::query()->where('user_code', $code)->exists());

        return $code;
    }
}
