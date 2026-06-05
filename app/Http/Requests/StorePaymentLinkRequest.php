<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canCreatePaymentLinks() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $min = (float) config('platform.marketplace.min_order_amount', 1);
        $max = (float) config('platform.marketplace.max_order_amount', 500000);
        $maxExpiry = (int) config('platform.payment_links.max_expiry_days', 90);

        return [
            'amount' => ['required', 'numeric', 'min:'.$min, 'max:'.$max],
            'description' => ['nullable', 'string', 'max:500'],
            'expires_in_days' => ['nullable', 'integer', 'min:1', 'max:'.$maxExpiry],
        ];
    }
}
