<?php

namespace App\Listeners;

use App\Events\StockReduced;
use App\Models\StockMovement;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class RecordStockMovementListener implements ShouldQueue
{
    use InteractsWithQueue;
    
    public int $tries = 5;
    public array $backoff = [5, 10, 30];
    public int $timeout = 60;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(StockReduced $event): void
    {
        try {
            \App\Models\JobExecution::create(['job_name' => static::class, 'order_id' => $event->order->id]);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::info('Listener skipped - already executed', ['listener' => static::class, 'order_id' => $event->order->id]);
            return;
        }

        $order = $event->order;

        foreach ($order->items as $item) {
            StockMovement::create([
                'product_id' => $item->product_id,
                'type' => 'OUT',
                'quantity' => $item->quantity,
                'reference_type' => get_class($order),
                'reference_id' => $order->id,
                'notes' => 'Order created'
            ]);
        }
        
        Log::info('Stock movements OUT recorded for order', ['order_id' => $order->id]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('RecordStockMovementListener failed', [
            'error' => $exception->getMessage()
        ]);
    }
}
