<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasActiveKyc() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'payer_mode' => ['required', Rule::in(['kyc', 'login', 'guest'])],
        ];
    }
}
