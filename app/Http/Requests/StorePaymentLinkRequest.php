<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'amount_type' => ['required', Rule::in(['fixed', 'open'])],
            'amount' => ['required_if:amount_type,fixed', 'nullable', 'numeric', 'min:'.$min, 'max:'.$max],
            'description' => ['nullable', 'string', 'max:500'],
            'usage_limit' => ['required', Rule::in(['once', 'ten', 'unlimited'])],
            'expires_on' => ['nullable', 'date', 'after_or_equal:today'],
            'expires_in_days' => ['nullable', 'integer', 'min:1', 'max:'.$maxExpiry],
        ];
    }

    public function isOpenAmount(): bool
    {
        return $this->input('amount_type') === 'open';
    }

    public function maxUses(): ?int
    {
        return match ($this->input('usage_limit')) {
            'once' => 1,
            'ten' => 10,
            default => null,
        };
    }
}
