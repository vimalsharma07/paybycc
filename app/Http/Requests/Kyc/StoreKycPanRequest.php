<?php

namespace App\Http\Requests\Kyc;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreKycPanRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null
            && ! $user->is_admin
            && ! $user->hasActiveKyc();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'pan' => ['required', 'string', 'size:10', 'regex:/^[A-Za-z]{5}[0-9]{4}[A-Za-z]{1}$/'],
            'pan_name' => ['required', 'string', 'max:255'],
            'dob' => ['required', 'string', 'regex:/^\d{2}\/\d{2}\/\d{4}$/'],
            'has_gst' => ['sometimes', 'boolean'],
            'gstin' => [
                Rule::requiredIf(fn () => $this->boolean('has_gst')),
                'nullable',
                'string',
                'size:15',
                'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/i',
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $dob = $this->input('dob');
            if (! is_string($dob) || $dob === '') {
                return;
            }

            try {
                $date = Carbon::createFromFormat('d/m/Y', $dob);
            } catch (\Throwable) {
                $validator->errors()->add('dob', 'Enter a valid date of birth in DD/MM/YYYY format.');

                return;
            }

            if (! $date || $date->format('d/m/Y') !== $dob) {
                $validator->errors()->add('dob', 'Enter a valid date of birth in DD/MM/YYYY format.');

                return;
            }

            if ($date->isFuture()) {
                $validator->errors()->add('dob', 'Date of birth cannot be in the future.');
            }

            if ($date->age > 120) {
                $validator->errors()->add('dob', 'Please enter a valid date of birth.');
            }
        });
    }

    public function normalizedPan(): string
    {
        return strtoupper((string) $this->validated('pan'));
    }

    public function panName(): string
    {
        return trim((string) $this->validated('pan_name'));
    }

    public function dob(): string
    {
        return (string) $this->validated('dob');
    }

    public function hasGst(): bool
    {
        return $this->boolean('has_gst');
    }

    public function gstin(): ?string
    {
        if (! $this->hasGst()) {
            return null;
        }

        $value = $this->validated('gstin');
        if (! is_string($value) || $value === '') {
            return null;
        }

        return strtoupper(preg_replace('/\s+/', '', $value) ?? $value);
    }
}
