<?php

namespace App\Http\Requests;

use App\Models\Bank;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreBankRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user->hasActiveKyc() && ! $user->is_admin;
    }

    public function rules(): array
    {
        return [
            'bank_name' => ['required', 'string', 'max:120'],
            'account_holder_name' => ['required', 'string', 'max:120'],
            'account_no' => ['required', 'string', 'max:32', 'regex:/^[A-Za-z0-9\-]{6,32}$/'],
            'ifsc' => ['required', 'string', 'size:11', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
            'is_primary' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $ifsc = strtoupper(preg_replace('/\s+/', '', (string) $this->input('ifsc', '')));
        $accountNo = preg_replace('/\s+/', '', (string) $this->input('account_no', ''));

        $this->merge([
            'ifsc' => $ifsc,
            'account_no' => $accountNo,
            'is_primary' => $this->boolean('is_primary'),
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $exists = Bank::query()
                ->where('user_id', $this->user()->id)
                ->where('ifsc', $this->input('ifsc'))
                ->where('account_no', $this->input('account_no'))
                ->exists();

            if ($exists) {
                $validator->errors()->add('account_no', 'This bank account is already registered.');
            }
        });
    }
}
