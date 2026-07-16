<?php

namespace App\Listeners;

use App\Events\PaymentPaid;
use App\Services\OrderService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UpdateOrderStatusListener implements ShouldQueue
{
    use InteractsWithQueue;
    
    protected $orderService;
    
    public int $tries = 5;
    public array $backoff = [5, 10, 30];
    public int $timeout = 60;

    /**
     * Create the event listener.
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Handle the event.
     */
    public function handle(PaymentPaid $event): void
    {
        try {
            \App\Models\JobExecution::create(['job_name' => static::class, 'order_id' => $event->order->id]);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::info('Listener skipped - already executed', ['listener' => static::class, 'order_id' => $event->order->id]);
            return;
        }

        if ($event->order->status !== 'pending') {
            Log::info('UpdateOrderStatus skipped - order already paid or cancelled', ['order_id' => $event->order->id]);
            return;
        }

        $this->orderService->updateStatus($event->order, 'paid');
        Log::info('Order status updated to paid', ['order_id' => $event->order->id]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('UpdateOrderStatusListener failed', [
            'error' => $exception->getMessage()
        ]);
    }
}
