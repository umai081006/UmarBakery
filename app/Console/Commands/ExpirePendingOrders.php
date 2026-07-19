<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ExpirePendingOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:expire-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire pending orders and payments that have passed their expiration time';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\OrderService $orderService)
    {
        $this->info('Starting to process expired orders...');

        // Find all payments that are pending and past expiration time
        $expiredPayments = \App\Models\Payment::where('status', 'pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->get();

        if ($expiredPayments->isEmpty()) {
            $this->info('No expired orders found.');
            return;
        }

        $count = 0;
        foreach ($expiredPayments as $payment) {
            \Illuminate\Support\Facades\DB::transaction(function () use ($payment, $orderService, &$count) {
                // Lock the payment row
                $lockedPayment = \App\Models\Payment::where('id', $payment->id)->lockForUpdate()->first();
                
                if ($lockedPayment && $lockedPayment->status === 'pending') {
                    $lockedPayment->update(['status' => 'expired']);
                    
                    if ($lockedPayment->order_id) {
                        $order = \App\Models\Order::find($lockedPayment->order_id);
                        if ($order && $order->status === 'pending') {
                            $orderService->updateStatus($order, 'cancelled');
                        }
                    }
                    $count++;
                    $this->info("Expired payment ID: {$lockedPayment->id} (Order: {$lockedPayment->order_id})");
                    
                    \Illuminate\Support\Facades\Log::info('payment.expired', [
                        'event' => 'payment.expired',
                        'payment_id' => $lockedPayment->id,
                        'order_id' => $lockedPayment->order_id,
                        'stock_restored' => true,
                    ]);
                }
            });
        }

        $this->info("Finished. Expired {$count} orders.");
    }
}
