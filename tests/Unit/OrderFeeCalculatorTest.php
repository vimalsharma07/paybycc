<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\User;
use App\Services\Orders\OrderFeeCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class OrderFeeCalculatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_processing_fee_applies_above_threshold(): void
    {
        $seller = $this->makeSeller();

        $fees = app(OrderFeeCalculator::class)->calculate($seller, 2000.0);

        $this->assertSame(60.0, $fees->processingFee);
        $this->assertSame(10.0, $fees->flatOrderFee);
        $this->assertSame(10.8, $fees->gstOnProcessingFee);
        $this->assertSame(0.0, $fees->tcsAmount);
        $this->assertSame(1919.2, $fees->netSettlement);
    }

    public function test_no_processing_fee_at_or_below_threshold(): void
    {
        $seller = $this->makeSeller();

        $fees = app(OrderFeeCalculator::class)->calculate($seller, 1000.0);

        $this->assertSame(0.0, $fees->processingFee);
        $this->assertSame(5.0, $fees->flatOrderFee);
    }

    public function test_tcs_when_seller_has_gstin(): void
    {
        $seller = $this->makeSeller(['gstin' => '22AAAAA0000A1Z5']);

        $fees = app(OrderFeeCalculator::class)->calculate($seller, 2000.0);

        $this->assertTrue($fees->tcsApplied);
        $this->assertSame(10.0, $fees->tcsAmount);
    }

    public function test_tds_after_fy_net_threshold(): void
    {
        $seller = $this->makeSeller();
        $customer = User::factory()->create(['role' => 'customer']);

        $this->createPaidOrder($seller, $customer, '500000.00', '500000.00');

        $fees = app(OrderFeeCalculator::class)->calculate($seller, 10000.0);

        $this->assertTrue($fees->tdsApplied);
        $this->assertSame(10.0, $fees->tdsAmount);
    }

    public function test_no_gst_seller_fy_cap_blocks_excess(): void
    {
        $seller = $this->makeSeller(['gstin' => null]);
        $customer = User::factory()->create(['role' => 'customer']);

        $this->createPaidOrder($seller, $customer, '1990000.00', '1990000.00');

        $this->expectException(InvalidArgumentException::class);
        app(OrderFeeCalculator::class)->calculate($seller, 50000.0);
    }

    protected function makeSeller(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'seller',
            'gstin' => null,
        ], $overrides));
    }

    protected function createPaidOrder(User $seller, User $customer, string $orderAmount, string $netAmount): Order
    {
        return Order::create([
            'order_code' => 'ORD-TEST'.uniqid(),
            'customer_id' => $customer->id,
            'freelancer_id' => $seller->id,
            'order_amount' => $orderAmount,
            'platform_fee' => '0.00',
            'gst_amount' => '0.00',
            'tds_amount' => '0.00',
            'tcs_amount' => '0.00',
            'net_settlement_amount' => $netAmount,
            'currency' => 'INR',
            'order_status' => 'accepted',
            'payment_status' => 'paid',
            'settlement_status' => 'eligible',
            'safe_status' => 'safe',
        ]);
    }
}
