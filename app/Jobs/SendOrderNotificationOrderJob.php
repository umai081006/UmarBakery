<?php

namespace App\Jobs;

use App\Models\Order;
use App\Notifications\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendOrderNotificationOrderJob implements ShouldQueue
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

    public function handle(NotificationService $notificationService): void
    {
        try {
            \App\Models\JobExecution::create(['job_name' => static::class, 'order_id' => $this->order->id]);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::info('Job skipped - already executed', ['job' => static::class, 'order_id' => $this->order->id]);
            return;
        }

        $notificationService->sendOrderNotification(
            $this->order->user_id,
            "Pesanan Dibuat #{$this->order->order_number}",
            "Pesanan Anda berhasil dibuat dan menunggu pembayaran.",
            'order_created',
            ['order_id' => $this->order->id]
        );
        
        $this->order->update(['processing_stage' => 'completed', 'pipeline_status' => 'completed']);
        Log::info('Order notification dispatched and pipeline completed', ['order_id' => $this->order->id]);
    }
    
    public function failed(\Throwable $exception): void
    {
        Log::error('SendOrderNotificationOrderJob failed', [
            'order_id' => $this->order->id,
            'error' => $exception->getMessage()
        ]);
        
        app(\App\Services\OrderService::class)->compensateOrder($this->order);
    }
}
