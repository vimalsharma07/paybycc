<?php

namespace App\Http\Requests\Concerns;

use App\Enums\LogLevel;
use App\Services\Logging\FlowLog;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

trait LogsBankValidationFailures
{
    protected function failedAuthorization(): void
    {
        $user = $this->user();

        if ($user !== null) {
            app(FlowLog::class)->bank(
                'bank.unauthorized',
                'Bank action blocked — not authorized',
                array_merge(
                    app(FlowLog::class)->userContext($user),
                    ['kyc_active' => $user->hasActiveKyc()]
                ),
                null,
                LogLevel::Warning
            );
        }

        parent::failedAuthorization();
    }

    protected function failedValidation(Validator $validator): void
    {
        $user = $this->user();

        if ($user !== null) {
            $action = $this->isMethod('POST') && ! $this->route('bank') ? 'store' : 'update';

            app(FlowLog::class)->bank(
                'bank.'.$action.'.validation_failed',
                'Bank form validation failed',
                array_merge(
                    app(FlowLog::class)->userContext($user),
                    ['fields' => array_keys($validator->errors()->toArray())]
                ),
                null,
                LogLevel::Notice
            );
        }

        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }
}
