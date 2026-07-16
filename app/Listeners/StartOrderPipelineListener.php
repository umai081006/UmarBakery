<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Jobs\ReduceStockJob;
use App\Jobs\CreatePaymentSessionJob;
use App\Jobs\ClearCartJob;
use App\Jobs\SendOrderNotificationOrderJob;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class StartOrderPipelineListener
{
    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
    {
        Log::info('Dispatching Order Pipeline', ['order_id' => $event->order->id]);

        Bus::chain([
            new ReduceStockJob($event->order),
            new CreatePaymentSessionJob($event->order),
            new ClearCartJob($event->order),
            new SendOrderNotificationOrderJob($event->order),
        ])->dispatch();
    }
}
