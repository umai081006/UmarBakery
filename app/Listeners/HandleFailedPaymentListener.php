<?php

namespace App\Listeners;

use App\Events\PaymentFailed;
use App\Services\OrderService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class HandleFailedPaymentListener implements ShouldQueue
{
    use InteractsWithQueue;
    
    protected $orderService;
    
    public int $tries = 5;
    public array $backoff = [5, 10, 30];
    public int $timeout = 60;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function handle(PaymentFailed $event): void
    {
        try {
            \App\Models\JobExecution::create(['job_name' => static::class, 'order_id' => $event->order->id]);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::info('Listener skipped - already executed', ['listener' => static::class, 'order_id' => $event->order->id]);
            return;
        }

        if ($event->order->status === 'cancelled') {
            Log::info('Failed payment handling skipped - order already cancelled', ['order_id' => $event->order->id]);
            return;
        }

        $this->orderService->cancelOrder($event->order);
        Log::info('Order cancelled due to failed payment', ['order_id' => $event->order->id]);
    }
    
    public function failed(\Throwable $exception): void
    {
        Log::error('HandleFailedPaymentListener failed', [
            'error' => $exception->getMessage()
        ]);
    }
}
