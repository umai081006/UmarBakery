<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Product;
use App\Services\CartService;
use App\Services\OrderService;

class StressCheckoutWorkerCommand extends Command
{
    protected $signature = 'stress:checkout-worker {user_id} {product_id}';
    protected $description = 'Worker to execute checkout concurrently';

    public function handle(CartService $cartService, OrderService $orderService)
    {
        // Setup specific test environment config
        config(['queue.default' => 'sync']);
        
        // Mock Midtrans for Testing Environment
        app()->bind(\App\Services\Payments\PaymentService::class, function () {
            return new class implements \App\Services\Payments\PaymentService {
                public function createPayment(\App\Models\Order $order): array {
                    $response = ['token' => 'mock-token-' . $order->id, 'redirect_url' => 'http://mock-url'];
                    \App\Models\Payment::create([
                        'order_id' => $order->id,
                        'provider' => 'midtrans',
                        'status' => 'pending',
                        'amount' => $order->total,
                        'currency' => 'IDR',
                        'transaction_id' => $order->order_number,
                        'snap_token' => $response['token'],
                        'snap_redirect_url' => $response['redirect_url'],
                    ]);
                    return $response;
                }
                public function handleWebhook(array $payload): void {}
                public function handleCallback(array $payload): void {}
                public function getPaymentStatus(string $orderId): array { return []; }
            };
        });

        try {
            $user = User::findOrFail($this->argument('user_id'));
            $productId = $this->argument('product_id');

            // Random delay to create real-world jitter (0-300ms)
            usleep(rand(0, 300000));

            // Add item to cart
            $cartService->addItem($user, $productId, 1);

            // Execute Checkout
            $order = $orderService->createOrder($user, [
                'recipient_name' => $user->name,
                'phone' => $user->phone,
                'address' => 'Chaos St.',
                'city' => 'Chaos City',
                'postal_code' => '00000'
            ], 'bank_transfer');

            // Random failure injection (20% chance) after order is created to test SAGA compensation
            if (rand(1, 100) <= 20) {
                // We simulate a failure in the pipeline by manually triggering compensation
                $orderService->compensateOrder($order);
                $this->error("Worker {$user->id}: Injected Failure (Compensated)");
                return 1;
            }

            $this->info("Worker {$user->id}: Success (Order {$order->order_number})");
            return 0;

        } catch (\Exception $e) {
            $this->error("Worker {$user->id}: Failed - " . $e->getMessage());
            return 1; // Exit code 1 indicates failure
        }
    }
}
