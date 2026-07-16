<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\CartService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ClearCartJob implements ShouldQueue
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

    public function handle(CartService $cartService): void
    {
        try {
            \App\Models\JobExecution::create(['job_name' => static::class, 'order_id' => $this->order->id]);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::info('Job skipped - already executed', ['job' => static::class, 'order_id' => $this->order->id]);
            return;
        }

        $cartService->clearCart($this->order->user);
        
        $this->order->update(['processing_stage' => 'cart_cleared']);
        Log::info('Cart cleared', ['order_id' => $this->order->id]);
    }
    
    public function failed(\Throwable $exception): void
    {
        Log::error('ClearCartJob failed', [
            'order_id' => $this->order->id,
            'error' => $exception->getMessage()
        ]);
        
        app(\App\Services\OrderService::class)->compensateOrder($this->order);
    }
}
