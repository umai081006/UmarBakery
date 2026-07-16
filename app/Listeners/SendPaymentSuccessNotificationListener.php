<?php

namespace App\Listeners;

use App\Events\PaymentPaid;
use App\Notifications\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendPaymentSuccessNotificationListener implements ShouldQueue
{
    use InteractsWithQueue;
    
    protected $notificationService;
    
    public int $tries = 5;
    public array $backoff = [5, 10, 30];
    public int $timeout = 60;

    /**
     * Create the event listener.
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
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

        // Actually, order status should be paid by the time this runs.
        // It's safe to just send it, or we could check a 'notification_sent' flag.
        // But for idempotent, we'll assume sending multiple notifications is slightly annoying but not corrupting data,
        // although we can prevent it by checking if it's already sent? No DB flag for that.
        
        $this->notificationService->sendOrderNotification(
            $event->order->user_id,
            "Pembayaran Berhasil #{$event->order->order_number}",
            "Pembayaran untuk pesanan Anda telah berhasil diverifikasi.",
            'payment_success',
            ['order_id' => $event->order->id]
        );
        Log::info('Payment success notification dispatched', ['order_id' => $event->order->id]);
    }
    
    public function failed(\Throwable $exception): void
    {
        Log::error('SendPaymentSuccessNotificationListener failed', [
            'error' => $exception->getMessage()
        ]);
    }
}
