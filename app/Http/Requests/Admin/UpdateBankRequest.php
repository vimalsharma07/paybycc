<?php

namespace App\Http\Requests\Admin;

use App\Models\Bank;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateBankRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->is_admin;
    }

    public function rules(): array
    {
        return [
            'bank_name' => ['required', 'string', 'max:120'],
            'account_holder_name' => ['required', 'string', 'max:120'],
            'account_no' => ['required', 'string', 'max:32', 'regex:/^[A-Za-z0-9\-]{6,32}$/'],
            'ifsc' => ['required', 'string', 'size:11', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
            'status' => ['required', 'in:active,inactive'],
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

            /** @var Bank $bank */
            $bank = $this->route('bank');

            $exists = Bank::query()
                ->where('user_id', $bank->user_id)
                ->where('ifsc', $this->input('ifsc'))
                ->where('account_no', $this->input('account_no'))
                ->whereKeyNot($bank->id)
                ->exists();

            if ($exists) {
                $validator->errors()->add('account_no', 'Another entry for this user already uses this IFSC and account number.');
            }
        });
    }
}
