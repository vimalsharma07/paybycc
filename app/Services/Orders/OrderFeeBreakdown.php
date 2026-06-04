<?php

namespace App\Services\Orders;

/**
 * Computed fee lines for one order (amounts in INR, 2 decimal places).
 */
readonly class OrderFeeBreakdown
{
    public function __construct(
        public float $orderAmount,
        public float $processingFee,
        public float $flatOrderFee,
        public float $gstOnProcessingFee,
        public float $tcsAmount,
        public float $tdsAmount,
        public float $netSettlement,
        public bool $sellerHasGst,
        public bool $tcsApplied,
        public bool $tdsApplied,
        public float $fyNetSettledBefore,
    ) {}

    public function totalPlatformCharges(): float
    {
        return round($this->processingFee + $this->flatOrderFee, 2);
    }

    public function totalDeductions(): float
    {
        return round(
            $this->processingFee
            + $this->flatOrderFee
            + $this->gstOnProcessingFee
            + $this->tcsAmount
            + $this->tdsAmount,
            2
        );
    }

    /**
     * @return array<string, string>
     */
    public function toOrderAttributes(): array
    {
        return [
            'order_amount' => $this->formatMoney($this->orderAmount),
            'platform_fee' => $this->formatMoney($this->totalPlatformCharges()),
            'gst_amount' => $this->formatMoney($this->gstOnProcessingFee),
            'tds_amount' => $this->formatMoney($this->tdsAmount),
            'tcs_amount' => $this->formatMoney($this->tcsAmount),
            'net_settlement_amount' => $this->formatMoney($this->netSettlement),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toPublicArray(): array
    {
        return [
            'order_amount' => $this->orderAmount,
            'processing_fee' => $this->processingFee,
            'flat_order_fee' => $this->flatOrderFee,
            'platform_fee' => $this->totalPlatformCharges(),
            'gst_on_processing_fee' => $this->gstOnProcessingFee,
            'tcs_amount' => $this->tcsAmount,
            'tds_amount' => $this->tdsAmount,
            'net_settlement' => $this->netSettlement,
            'seller_has_gst' => $this->sellerHasGst,
            'tcs_applied' => $this->tcsApplied,
            'tds_applied' => $this->tdsApplied,
            'fy_net_settled_before' => $this->fyNetSettledBefore,
        ];
    }

    protected function formatMoney(float $value): string
    {
        return number_format(round($value, 2), 2, '.', '');
    }
}
