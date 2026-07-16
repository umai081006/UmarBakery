<?php

namespace App\Services\Payments;

use App\Models\Order;

interface PaymentService
{
    /**
     * Create a payment request to the provider.
     *
     * @param Order $order
     * @return array
     */
    public function createPayment(Order $order): array;

    /**
     * Handle webhook callback from the provider.
     *
     * @param array $payload
     * @return void
     */
    public function handleCallback(array $payload): void;

    /**
     * Get real-time payment status from provider.
     *
     * @param string $transactionId
     * @return array
     */
    public function getPaymentStatus(string $transactionId): array;
}
