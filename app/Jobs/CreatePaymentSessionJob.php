<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\Payments\PaymentService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class CreatePaymentSessionJob implements ShouldQueue
{
    use Queueable;

    public $order;
    
    public int $tries = 5;
    public array $backoff = [5, 10, 30];
    public int $timeout = 60;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function handle(PaymentService $paymentService): void
    {
        try {
            \App\Models\JobExecution::create(['job_name' => static::class, 'order_id' => $this->order->id]);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::info('Job skipped - already executed', ['job' => static::class, 'order_id' => $this->order->id]);
            return;
        }

        // Add idempotency check: only process if status is pending
        if ($this->order->status !== 'pending') {
            Log::info('Payment session skipped - order not pending', ['order_id' => $this->order->id]);
            return;
        }
        
        $paymentService->createPayment($this->order);
        
        $this->order->update(['processing_stage' => 'payment_created']);
        Log::info('Payment session created', ['order_id' => $this->order->id]);
    }
    
    public function failed(\Throwable $exception): void
    {
        Log::error('CreatePaymentSessionJob failed', [
            'order_id' => $this->order->id,
            'error' => $exception->getMessage()
        ]);
        
        app(\App\Services\OrderService::class)->compensateOrder($this->order);
    }
}
