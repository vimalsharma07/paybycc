<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateWalletRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'auto_settle_to_bank' => ['boolean'],
            'default_bank_id' => ['nullable', 'integer', 'exists:banks,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'auto_settle_to_bank' => $this->boolean('auto_settle_to_bank'),
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $bankId = $this->input('default_bank_id');
            if ($bankId === null || $bankId === '') {
                return;
            }

            $owns = $this->user()->banks()->whereKey($bankId)->exists();
            if (! $owns) {
                $validator->errors()->add('default_bank_id', 'Choose one of your bank accounts.');
            }
        });
    }
}
