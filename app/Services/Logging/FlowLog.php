<?php

namespace App\Services\Logging;

use App\Enums\LogLevel;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

/**
 * Structured logs for user-facing flows (auth, kyc, etc.).
 */
class FlowLog
{
    public function __construct(
        protected AppLogger $logger,
    ) {}

    public function auth(
        string $event,
        string $message,
        array $context = [],
        ?Model $subject = null,
        LogLevel $level = LogLevel::Info,
    ): void {
        $this->logger->log($level, 'auth', $event, $message, $context, $subject);
    }

    public function kyc(
        string $event,
        string $message,
        array $context = [],
        ?Model $subject = null,
        LogLevel $level = LogLevel::Info,
    ): void {
        $this->logger->log($level, 'kyc', $event, $message, $context, $subject);
    }

    public function bank(
        string $event,
        string $message,
        array $context = [],
        ?Model $subject = null,
        LogLevel $level = LogLevel::Info,
    ): void {
        $this->logger->log($level, 'bank', $event, $message, $context, $subject);
    }

    public function order(
        string $event,
        string $message,
        array $context = [],
        ?Model $subject = null,
        LogLevel $level = LogLevel::Info,
    ): void {
        $this->logger->log($level, 'order', $event, $message, $context, $subject);
    }

    public function transaction(
        string $event,
        string $message,
        array $context = [],
        ?Model $subject = null,
        LogLevel $level = LogLevel::Info,
    ): void {
        $this->logger->log($level, 'transaction', $event, $message, $context, $subject);
    }

    /**
     * @return array<string, mixed>
     */
    public function orderContext(Order $order, array $extra = []): array
    {
        return array_merge([
            'order_id' => $order->id,
            'order_code' => $order->order_code,
            'customer_id' => $order->customer_id,
            'freelancer_id' => $order->freelancer_id,
            'order_amount' => (float) $order->order_amount,
            'net_settlement_amount' => (float) $order->net_settlement_amount,
            'order_status' => (int) $order->order_status,
            'payment_status' => (int) $order->payment_status,
            'settlement_status' => (int) $order->settlement_status,
            'safe_status' => (int) $order->safe_status,
            'ip' => is_array($order->ip_json) ? ($order->ip_json['ip'] ?? null) : null,
        ], $extra);
    }

    /**
     * @return array<string, mixed>
     */
    public function paymentContext(Payment $payment, array $extra = []): array
    {
        return array_merge([
            'payment_id' => $payment->id,
            'order_id' => $payment->order_id,
            'payment_link_id' => $payment->payment_link_id,
            'user_id' => $payment->user_id,
            'gateway_id' => $payment->gateway_id,
            'amount' => (float) $payment->amount,
            'status' => $payment->status,
        ], $extra);
    }

    /**
     * @return array<string, mixed>
     */
    public function transactionContext(Transaction $transaction, array $extra = []): array
    {
        return array_merge([
            'transaction_id' => $transaction->id,
            'user_id' => $transaction->user_id,
            'payment_id' => $transaction->payment_id,
            'type' => $transaction->type,
            'amount' => (float) $transaction->amount,
            'status' => (int) $transaction->status,
        ], $extra);
    }

    /**
     * @return array<string, mixed>
     */
    public function maskedBankAccount(?string $accountNo): array
    {
        if ($accountNo === null || $accountNo === '') {
            return [];
        }

        $len = strlen($accountNo);

        return [
            'account_masked' => $len <= 4
                ? '****'
                : str_repeat('*', max(0, $len - 4)).substr($accountNo, -4),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function bankContext(?string $ifsc, ?string $bankName = null, array $extra = []): array
    {
        $ctx = $extra;
        if ($ifsc !== null && $ifsc !== '') {
            $ctx['ifsc'] = strtoupper($ifsc);
        }
        if ($bankName !== null && $bankName !== '') {
            $ctx['bank_name'] = $bankName;
        }

        return $ctx;
    }

    /**
     * @return array<string, mixed>
     */
    public function userContext(User $user, array $extra = []): array
    {
        return array_merge([
            'user_id' => $user->id,
            'user_code' => $user->user_code,
            'email' => $user->email,
            'kyc_status' => $user->kyc_status,
        ], $extra);
    }

    /**
     * @return array<string, mixed>
     */
    public function validationErrors(ValidationException $e): array
    {
        return ['fields' => array_keys($e->errors())];
    }

    /**
     * @return array<string, mixed>
     */
    public function maskedPan(?string $pan): array
    {
        if ($pan === null || $pan === '') {
            return [];
        }

        $pan = strtoupper($pan);

        return ['pan_masked' => substr($pan, 0, 2).'****'.substr($pan, -2)];
    }

    /**
     * @return array<string, mixed>
     */
    public function maskedAadhar(?string $aadhar): array
    {
        if ($aadhar === null || $aadhar === '') {
            return [];
        }

        return ['aadhar_masked' => '****'.substr($aadhar, -4)];
    }
}
