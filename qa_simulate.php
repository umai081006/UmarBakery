<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use App\Services\CartService;
use App\Services\OrderService;
use App\Events\PaymentPaid;
use App\Events\PaymentFailed;
use App\Models\Payment;
use App\Models\Order;
use App\Models\StockMovement;
use App\Models\OrderEventLog;
use App\Models\JobExecution;
use Illuminate\Support\Facades\Event;

echo "--- STARTING QA END-TO-END SIMULATION ---\n\n";

// 0. Clean DB
echo "Refreshing Database...\n";
\Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--seed' => true]);
config(['queue.default' => 'sync']);

// Mock Midtrans for QA Simulation
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

echo "Database Refreshed and Queue set to SYNC.\n\n";

// 1. Setup User & Product
$user = User::firstOrCreate(['email' => 'qa_test@example.com'], [
    'name' => 'QA Tester', 'password' => bcrypt('password'), 'phone' => '081234567890'
]);

$category = \App\Models\Category::firstOrCreate(['slug' => 'qa-category'], ['name' => 'QA Category']);
$product = Product::firstOrCreate(['sku' => 'QA-BREAD-01'], [
    'name' => 'QA Roti Sobek',
    'slug' => 'qa-roti-sobek',
    'description' => 'Test bread',
    'price' => 25000,
    'stock' => 10,
    'weight' => 500,
    'is_active' => true,
    'category_id' => $category->id
]);

// Reset stock for clean test
$product->update(['stock' => 10]);
echo "Initial Stock: {$product->stock}\n";

// 2. Add to Cart
$cartService = app(CartService::class);
$cartService->addItem($user, $product->id, 2);
echo "Added 2 qty to cart.\n";

// 3. Checkout
$orderService = app(OrderService::class);
echo "Executing Checkout...\n";
$order = $orderService->createOrder($user, [
    'recipient_name' => 'QA Tester',
    'phone' => '081234567890',
    'address' => 'Test Address',
    'city' => 'Jakarta',
    'postal_code' => '12345'
], 'bank_transfer');

echo "Order Created: {$order->order_number} (Stage: {$order->processing_stage})\n";

// Wait for queue to process
sleep(3);
$order->refresh();
$product->refresh();

echo "Post-Checkout Stock: {$product->stock} (Expected: 8)\n";
echo "Post-Checkout Order Stage: {$order->processing_stage} (Expected: completed)\n";

// Check DB records
$movements = StockMovement::where('reference_id', $order->id)->get();
echo "Stock Movements OUT: " . $movements->count() . "\n";

$jobExecutions = JobExecution::where('order_id', $order->id)->count();
echo "Job Executions Created: {$jobExecutions}\n";

$payment = Payment::where('order_id', $order->id)->first();
echo "Payment Session Status: " . ($payment ? $payment->status : 'NOT FOUND') . "\n";

// 4. Payment Success
echo "\n--- SIMULATING PAYMENT SUCCESS ---\n";
event(new PaymentPaid($order, $payment));
sleep(2);
$order->refresh();
echo "Order Status after Payment: {$order->status} (Expected: paid)\n";

// 5. Payment Failed Scenario (New Order)
echo "\n--- SIMULATING FAILURE SCENARIO ---\n";
$product->update(['stock' => 10]);
$cartService->addItem($user, $product->id, 3);
$order2 = $orderService->createOrder($user, [
    'recipient_name' => 'QA Fail', 'phone' => '111', 'address' => 'X', 'city' => 'X', 'postal_code' => 'X'
], 'bank_transfer');

sleep(3);
$order2->refresh();
$product->refresh();
echo "Fail Scenario Order Stage: {$order2->processing_stage}\n";
echo "Fail Scenario Stock before fail: {$product->stock} (Expected 7)\n";

event(new PaymentFailed($order2));
sleep(2);
$order2->refresh();
$product->refresh();

echo "Order Status after Failed Payment: {$order2->status} (Expected: cancelled)\n";
echo "Stock after Failed Payment: {$product->stock} (Expected: 10)\n";
$returnMovements = StockMovement::where('reference_id', $order2->id)->where('type', 'RETURN')->count();
echo "Stock RETURN ledgers: {$returnMovements}\n";

// Check Event Traceability
$logs = OrderEventLog::where('order_id', $order->id)->count();
echo "\nEvent Traceability Logs for Order 1: {$logs}\n";

echo "\n--- QA SIMULATION COMPLETE ---\n";
