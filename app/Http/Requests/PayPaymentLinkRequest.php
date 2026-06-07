<?php

namespace App\Http\Requests;

use App\Services\PaymentLinks\PaymentLinkService;
use Illuminate\Foundation\Http\FormRequest;

class PayPaymentLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $min = (float) config('platform.marketplace.min_order_amount', 1);
        $max = (float) config('platform.marketplace.max_order_amount', 500000);
        $linkToken = $this->route('linkToken');
        $requiresAmount = false;

        if (is_string($linkToken) && $linkToken !== '') {
            $link = app(PaymentLinkService::class)->findByToken($linkToken);
            $requiresAmount = $link?->isOpenAmount() ?? false;
        }

        return [
            'checkout_meta' => ['nullable', 'string', 'max:2000'],
            'amount' => [
                $requiresAmount ? 'required' : 'nullable',
                'numeric',
                'min:'.$min,
                'max:'.$max,
            ],
        ];
    }
}
