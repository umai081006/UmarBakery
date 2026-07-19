<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\Payment;
use Exception;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\Log;

class MidtransService implements PaymentService
{
    /**
     * Create a payment request to the provider.
     *
     * @param Order $order
     * @return array
     */
    public function createPayment(Order $order): array
    {
        $this->initMidtransConfig();

        $payload = $this->buildPayload($order);

        try {
            $transaction = Snap::createTransaction($payload);
            
            $response = [
                'token' => $transaction->token,
                'redirect_url' => $transaction->redirect_url,
            ];

            $this->storePayment($response, $order);

            return $response;

        } catch (Exception $e) {
            Log::error('Midtrans Snap Error: ' . $e->getMessage(), ['order_id' => $order->id]);
            throw new Exception('Gagal membuat transaksi pembayaran.');
        }
    }

    /**
     * Initialize Midtrans Configuration.
     */
    protected function initMidtransConfig(): void
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.sanitization');
        Config::$is3ds = config('midtrans.3ds');
    }

    /**
     * Map Order model to Midtrans payload format.
     */
    protected function buildPayload(Order $order): array
    {
        $items = [];
        
        // Make sure to eager load items or assume they are available
        foreach ($order->items as $item) {
            $items[] = [
                'id' => (string) ($item->product_sku ?? $item->product_id),
                'price' => (int) $item->price,
                'quantity' => $item->quantity,
                'name' => mb_substr($item->product_name, 0, 50),
            ];
        }

        // Add shipping cost if exists
        if ($order->shipping_cost > 0) {
            $items[] = [
                'id' => 'SHIPPING',
                'price' => (int) $order->shipping_cost,
                'quantity' => 1,
                'name' => 'Ongkos Kirim',
            ];
        }

        return [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => (int) $order->total,
            ],
            'customer_details' => [
                'first_name' => mb_substr($order->recipient_name, 0, 255),
                'email' => mb_substr($order->user->email ?? '', 0, 255),
                'phone' => mb_substr($order->phone ?? '', 0, 255),
            ],
            'item_details' => $items,
            'custom_expiry' => [
                'expiry_duration' => 60,
                'unit' => 'minute'
            ]
        ];
    }

    /**
     * Store payment record into DB.
     */
    protected function storePayment(array $response, Order $order): Payment
    {
        return Payment::create([
            'order_id' => $order->id,
            'provider' => 'midtrans',
            'status' => 'pending',
            'amount' => $order->total,
            'currency' => 'IDR',
            'transaction_id' => $order->order_number,
            'snap_token' => $response['token'],
            'snap_redirect_url' => $response['redirect_url'],
            'expires_at' => now()->addMinutes(60),
            'raw_response' => $response,
        ]);
    }

    /**
     * Handle webhook callback from the provider.
     */
    public function handleCallback(array $payload): void
    {
        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $serverKey = config('midtrans.server_key');
        $signatureKey = $payload['signature_key'] ?? '';

        if (!$orderId || !$statusCode || !$grossAmount) {
            throw new Exception("Invalid payload data");
        }

        // Validate Signature
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        
        if ($expectedSignature !== $signatureKey) {
            throw new Exception("Invalid Signature");
        }

        $transactionStatus = $payload['transaction_status'];
        $fraudStatus = $payload['fraud_status'] ?? null;

        // Status mapping
        $status = 'pending';
        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'accept') {
                $status = 'paid';
            }
        } else if ($transactionStatus == 'settlement') {
            $status = 'paid';
        } else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
            $status = 'failed';
        } else if ($transactionStatus == 'pending') {
            $status = 'pending';
        }

        // Update Payment Record with Row Lock for Idempotency
        \Illuminate\Support\Facades\DB::transaction(function () use ($orderId, $status, $payload, $grossAmount) {
            $payment = Payment::with('order')->where('transaction_id', $orderId)->lockForUpdate()->first();
            
            if (!$payment) {
                throw new Exception("Payment not found for Order ID: " . $orderId);
            }

            // Verify Gross Amount to prevent manipulation
            if ((float)$payment->amount !== (float)$grossAmount) {
                throw new Exception("Gross amount mismatch.");
            }

            // Webhook Hardening: Prevent overriding final states
            if ($payment->order && in_array($payment->order->status, ['completed', 'cancelled'])) {
                \Illuminate\Support\Facades\Log::info('Webhook ignored - order in final state', ['order_id' => $payment->order->id]);
                return;
            }

            // Webhook Idempotency: Ignore if status is already achieved
            if ($payment->status === $status) {
                \Illuminate\Support\Facades\Log::info('Webhook ignored - payment already in target status', ['order_id' => $payment->order->id, 'status' => $status]);
                return;
            }

            $payment->status = $status;
            $payment->raw_response = $payload;
            if ($status === 'paid' && !$payment->paid_at) {
                $payment->paid_at = now();
            }
            $payment->save();

            // Dispatch Event for Decoupling
            if ($status === 'paid' && $payment->order) {
                event(new \App\Events\PaymentPaid($payment->order, $payment));
            } elseif ($status === 'failed' && $payment->order) {
                event(new \App\Events\PaymentFailed($payment->order));
            }
        });
    }

    /**
     * Get real-time payment status from provider.
     */
    public function getPaymentStatus(string $transactionId): array
    {
        // To be implemented on Day 4
        return [];
    }
}
