<?php

namespace App\Services\Payments;

use App\Enums\LogLevel;
use App\Gateways\Contracts\HandlesPaymentReturn;
use App\Models\Payment;
use App\Services\Logging\FlowLog;

class PaymentReturnService
{
    public function __construct(
        protected GatewayManager $gateways,
        protected FlowLog $flow,
    ) {}

    /**
     * Reconcile payment status with the gateway (return URL or pending refresh).
     */
    public function syncFromGateway(Payment $payment): Payment
    {
        $payment->loadMissing(['gateway', 'order']);

        $this->flow->order(
            'return.sync',
            'Reconciling payment from gateway',
            $this->flow->paymentContext($payment),
            $payment->order,
            LogLevel::Debug,
        );

        $this->flow->gateway(
            'return.sync',
            'Reconciling payment from gateway',
            $this->flow->gatewayContext($payment),
            $payment->order,
            LogLevel::Debug,
        );

        $driver = $this->gateways->driverForPayment($payment);
        if ($driver instanceof HandlesPaymentReturn) {
            $payment = $driver->handleReturn(request(), $payment);
        } else {
            $this->flow->gateway(
                'return.unsupported',
                'Payment gateway does not support return sync',
                $this->flow->gatewayContext($payment),
                $payment->order,
                LogLevel::Warning,
            );
        }

        $fresh = $payment->fresh(['gateway', 'order.freelancer', 'order.customer']);

        if ($fresh) {
            $outcome = $this->outcome($fresh);

            $this->flow->order(
                'return.synced',
                'Payment status after gateway sync',
                array_merge($this->flow->paymentContext($fresh), ['outcome' => $outcome]),
                $fresh->order,
            );

            $this->flow->gateway(
                'return.synced',
                'Payment status after gateway sync',
                array_merge($this->flow->gatewayContext($fresh), ['outcome' => $outcome]),
                $fresh->order,
            );
        }

        return $fresh ?? $payment;
    }

    public function outcome(Payment $payment): string
    {
        return match ($payment->status) {
            'completed' => 'success',
            'failed' => 'failed',
            default => 'pending',
        };
    }

    /**
     * @return array<string, mixed>
     */
    public function resultContext(Payment $payment): array
    {
        $payment->loadMissing([
            'gateway:id,name,code',
            'order.freelancer:id,name,company_name,user_code',
            'order.customer:id,name',
        ]);

        $order = $payment->order;
        $freelancer = $order?->freelancer;
        $refreshReturnUrl = $this->gatewayReturnUrl($payment);

        return [
            'payment' => $payment,
            'order' => $order,
            'freelancer' => $freelancer,
            'gatewayName' => $payment->gateway?->name ?? 'Payment gateway',
            'amountLabel' => '₹'.number_format((float) $payment->amount, 2),
            'netSettlementLabel' => $order
                ? '₹'.number_format((float) $order->net_settlement_amount, 2)
                : null,
            'currency' => $payment->currency,
            'platformReference' => '#'.$payment->id,
            'gatewayReference' => $payment->gateway_reference,
            'orderCode' => $order?->order_code,
            'freelancerName' => $freelancer?->name,
            'freelancerId' => $freelancer?->id,
            'remark' => $payment->remark,
            'completedAt' => $payment->updated_at,
            'refreshReturnUrl' => $refreshReturnUrl,
        ];
    }

    protected function gatewayReturnUrl(Payment $payment): ?string
    {
        $driver = $this->gateways->driverForPayment($payment);

        return $driver instanceof HandlesPaymentReturn
            ? $driver->returnUrl($payment)
            : null;
    }
}
