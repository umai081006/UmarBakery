<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Product;
use App\Events\StockReduced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReduceStockJob implements ShouldQueue
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

    public function handle(): void
    {
        try {
            \App\Models\JobExecution::create(['job_name' => static::class, 'order_id' => $this->order->id]);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::info('Job skipped - already executed', ['job' => static::class, 'order_id' => $this->order->id]);
            return;
        }

        // Stock decrement is now handled synchronously in OrderService::createOrder
        // to prevent overselling race conditions. This job now only updates the stage
        // and dispatches the event for ledger recording.
        
        $this->order->update(['processing_stage' => 'stock_reduced']);
        event(new StockReduced($this->order));
        Log::info('Stock reduced for order', ['order_id' => $this->order->id]);
    }
    
    public function failed(\Throwable $exception): void
    {
        Log::error('ReduceStockJob failed', [
            'order_id' => $this->order->id,
            'error' => $exception->getMessage()
        ]);
        
        app(\App\Services\OrderService::class)->compensateOrder($this->order);
    }
}
