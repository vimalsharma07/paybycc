<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->is_admin;
    }

    public function rules(): array
    {
        /** @var User $target */
        $target = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($target->id)],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($target->id)],
            'status' => ['required', 'string', Rule::in(['active', 'inactive'])],
            'kyc_status' => ['required', 'integer', Rule::in([User::KYC_INCOMPLETE, User::KYC_INACTIVE, User::KYC_ACTIVE])],
            'daily_limit' => ['required', 'numeric', 'min:0', 'max:9999999999999.99'],
            'monthly_limit' => ['required', 'numeric', 'min:0', 'max:9999999999999.99'],
            'yearly_limit' => ['required', 'numeric', 'min:0', 'max:9999999999999.99'],
            'pan' => ['nullable', 'string', 'size:10', 'regex:/^[A-Za-z]{5}[0-9]{4}[A-Za-z]{1}$/'],
            'pan_name' => ['nullable', 'string', 'max:255'],
            'aadhar' => ['nullable', 'string', 'size:12', 'regex:/^\d{12}$/'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'is_admin' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_admin' => $this->boolean('is_admin'),
        ]);

        foreach (['pan', 'pan_name', 'aadhar', 'phone'] as $field) {
            if ($this->input($field) === '') {
                $this->merge([$field => null]);
            }
        }

        if ($this->filled('pan')) {
            $this->merge(['pan' => strtoupper((string) $this->input('pan'))]);
        }
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            /** @var User $target */
            $target = $this->route('user');

            if (! $this->boolean('is_admin') && $target->is_admin && $target->id === $this->user()->id) {
                $validator->errors()->add('is_admin', 'You cannot remove admin access from your own account.');
            }
        });
    }
}
