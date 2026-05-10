<?php

namespace App\Http\Requests\Admin;

use App\Models\Gateway;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateGatewayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->is_admin;
    }

    public function rules(): array
    {
        /** @var Gateway $gateway */
        $gateway = $this->route('gateway');

        return [
            'name' => ['required', 'string', 'max:120'],
            'code' => ['required', 'string', 'max:64', 'regex:/^[a-z0-9][a-z0-9_-]*$/', Rule::unique('gateways', 'code')->ignore($gateway->id)],
            'filename' => ['required', 'string', 'max:120', 'regex:/^[A-Za-z][A-Za-z0-9_-]*(\.php)?$/'],
            'credentials_json' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'is_primary' => ['boolean'],
            'min_txn' => ['required', 'numeric', 'min:0'],
            'max_txn' => ['required', 'numeric', 'gte:min_txn'],
            'daily_limit' => ['required', 'numeric', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_primary' => $this->boolean('is_primary'),
            'filename' => Gateway::normalizeFilename((string) $this->input('filename', '')),
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $raw = $this->input('credentials_json');
            if ($raw !== null && $raw !== '') {
                json_decode((string) $raw);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $validator->errors()->add('credentials_json', 'Credentials must be valid JSON (object or array).');

                    return;
                }
            }

            $filename = Gateway::normalizeFilename((string) $this->input('filename'));
            $class = 'App\\Gateways\\'.Str::studly($filename);

            if ($this->input('status') === 'active' && ! class_exists($class)) {
                $validator->errors()->add(
                    'filename',
                    "No class found for {$class}. Add app/Gateways/".Str::studly($filename).'.php implementing the gateway contract.'
                );
            }
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function validatedPayload(): array
    {
        $data = $this->validated();
        unset($data['credentials_json'], $data['is_primary']);

        $raw = $this->input('credentials_json');
        if ($raw === null || $raw === '') {
            $data['credentials'] = [];
        } else {
            $decoded = json_decode((string) $raw, true);
            $data['credentials'] = is_array($decoded) ? $decoded : [];
        }

        return $data;
    }
}
