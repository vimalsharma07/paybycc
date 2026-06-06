<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use InvalidArgumentException;

class SellerReceiveLimitService
{
    public function assertCanReceiveAmount(User $seller, float $amount): void
    {
        $this->assertPeriodLimit($seller, 'daily_limit', $amount, now()->startOfDay(), 'daily');
        $this->assertPeriodLimit($seller, 'monthly_limit', $amount, now()->startOfMonth(), 'monthly');
        $this->assertPeriodLimit($seller, 'yearly_limit', $amount, $this->financialYearStart(), 'yearly');
    }

    /**
     * @return array{daily: array{limit: float, used: float, remaining: float|null}, monthly: array{limit: float, used: float, remaining: float|null}, yearly: array{limit: float, used: float, remaining: float|null}}
     */
    public function snapshot(User $seller): array
    {
        return [
            'daily' => $this->periodSnapshot($seller, 'daily_limit', now()->startOfDay()),
            'monthly' => $this->periodSnapshot($seller, 'monthly_limit', now()->startOfMonth()),
            'yearly' => $this->periodSnapshot($seller, 'yearly_limit', $this->financialYearStart()),
        ];
    }

    protected function assertPeriodLimit(User $seller, string $column, float $amount, Carbon $since, string $label): void
    {
        $limit = (float) $seller->{$column};
        if ($limit <= 0) {
            return;
        }

        $used = $this->receivedSince($seller, $since);
        if ($used + $amount > $limit + 0.00001) {
            throw new InvalidArgumentException(
                'This seller has reached their '.$label.' receive limit (₹'.number_format($limit, 0).'). Try a smaller amount or later.'
            );
        }
    }

    /**
     * @return array{limit: float, used: float, remaining: float|null}
     */
    protected function periodSnapshot(User $seller, string $column, Carbon $since): array
    {
        $limit = (float) $seller->{$column};
        $used = $this->receivedSince($seller, $since);

        return [
            'limit' => $limit,
            'used' => $used,
            'remaining' => $limit > 0 ? max(0, $limit - $used) : null,
        ];
    }

    protected function receivedSince(User $seller, Carbon $since): float
    {
        return (float) Order::query()
            ->where('freelancer_id', $seller->id)
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', $since)
            ->whereHas('payments', fn ($query) => $query->whereNotNull('payment_link_id'))
            ->sum('order_amount');
    }

    protected function financialYearStart(): Carbon
    {
        $now = now();
        $year = $now->month >= 4 ? $now->year : $now->year - 1;

        return Carbon::create($year, 4, 1)->startOfDay();
    }
}
