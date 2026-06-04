<?php

namespace App\Services\Orders;

use App\Models\Order;
use App\Models\User;
use App\Support\Commerce\FinancialYear;
use Carbon\CarbonInterface;
use InvalidArgumentException;

class OrderFeeCalculator
{
    public function calculate(User $freelancer, float $orderAmount, ?CarbonInterface $on = null): OrderFeeBreakdown
    {
        $amount = round(max(0, $orderAmount), 2);

        if ($amount <= 0) {
            throw new InvalidArgumentException('Order amount must be greater than zero.');
        }

        $processingPercent = (float) config('commerce.processing_fee.percent', 3);
        $processingThreshold = (float) config('commerce.processing_fee.threshold_inr', 1000);
        $gstPercent = (float) config('commerce.gst_on_processing_fee_percent', 18);
        $flatPercent = (float) config('commerce.flat_order_fee_percent', 0.5);
        $tcsPercent = (float) config('commerce.tcs_when_gst_registered_percent', 0.5);
        $tdsThreshold = (float) config('commerce.tds.cumulative_net_threshold_inr', 500000);
        $tdsPercent = (float) config('commerce.tds.percent_after_threshold', 0.1);
        $noGstCap = (float) config('commerce.seller_no_gst_max_net_payout_per_fy_inr', 2000000);

        $processingFee = $amount > $processingThreshold
            ? $this->percentOf($amount, $processingPercent)
            : 0.0;

        $flatOrderFee = $this->percentOf($amount, $flatPercent);
        $gstOnProcessing = $this->percentOf($processingFee, $gstPercent);

        $sellerHasGst = $freelancer->hasGstRegistered();
        $tcsAmount = $sellerHasGst ? $this->percentOf($amount, $tcsPercent) : 0.0;

        $fyNetBefore = $this->freelancerFyNetSettled($freelancer->id, $on);
        $tdsAmount = $fyNetBefore >= $tdsThreshold
            ? $this->percentOf($amount, $tdsPercent)
            : 0.0;

        $net = round(
            $amount - $processingFee - $flatOrderFee - $gstOnProcessing - $tcsAmount - $tdsAmount,
            2
        );

        if ($net < 0) {
            throw new InvalidArgumentException('Fees exceed order amount. Lower the amount or adjust fee configuration.');
        }

        if (! $sellerHasGst) {
            $projectedFyNet = $fyNetBefore + $net;
            if ($projectedFyNet > $noGstCap + 0.0001) {
                $remaining = max(0, $noGstCap - $fyNetBefore);
                throw new InvalidArgumentException(
                    'This seller has no GSTIN on file and may receive at most ₹'
                    .number_format($noGstCap, 0)
                    .' net per financial year (₹'
                    .number_format($remaining, 2)
                    .' remaining). Ask them to add GSTIN or reduce the amount.'
                );
            }
        }

        return new OrderFeeBreakdown(
            orderAmount: $amount,
            processingFee: $processingFee,
            flatOrderFee: $flatOrderFee,
            gstOnProcessingFee: $gstOnProcessing,
            tcsAmount: $tcsAmount,
            tdsAmount: $tdsAmount,
            netSettlement: $net,
            sellerHasGst: $sellerHasGst,
            tcsApplied: $tcsAmount > 0,
            tdsApplied: $tdsAmount > 0,
            fyNetSettledBefore: $fyNetBefore,
        );
    }

    public function freelancerFyNetSettled(int $freelancerId, ?CarbonInterface $on = null): float
    {
        $fyStart = FinancialYear::start($on);

        return (float) Order::query()
            ->where('freelancer_id', $freelancerId)
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', $fyStart)
            ->sum('net_settlement_amount');
    }

    protected function percentOf(float $base, float $percent): float
    {
        if ($percent <= 0 || $base <= 0) {
            return 0.0;
        }

        return round($base * ($percent / 100), 2);
    }
}
