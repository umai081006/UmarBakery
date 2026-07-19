<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Events\OrderCreated;

class OrderService
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Create an order from user's cart.
     */
    public function createOrder(User $user, array $addressData, string $paymentMethod = 'bank_transfer'): Order
    {
        $order = DB::transaction(function () use ($user, $addressData, $paymentMethod) {
            // 1. Get cart items and validate
            $cartData = $this->cartService->getCartWithTotal($user);
            $cartItems = $cartData['items'];

            if ($cartItems->isEmpty()) {
                throw new Exception('Keranjang belanja Anda kosong.');
            }

            // 2. Validate stock and decrement atomically
            foreach ($cartItems as $item) {
                // Lock the product row for update to ensure stock is valid during checkout
                $product = Product::where('id', $item->product_id)
                    ->lockForUpdate()
                    ->first();

                if (!$product || !$product->is_active) {
                    throw new Exception("Produk '{$item->product->name}' tidak lagi aktif atau tersedia.");
                }

                if ($product->stock < $item->quantity) {
                    throw new Exception("Stok untuk '{$product->name}' tidak mencukupi. Tersedia: {$product->stock}");
                }

                // Decrement stock synchronously to prevent overselling race condition
                $product->decrement('stock', $item->quantity);
            }

            // 3. Generate Order Number
            $today = now()->format('Ymd');
            $count = Order::whereDate('created_at', now()->toDateString())->count();
            $orderNumber = 'UB-' . $today . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);

            // Double check uniqueness
            while (Order::where('order_number', $orderNumber)->exists()) {
                $count++;
                $orderNumber = 'UB-' . $today . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
            }

            // 4. Resolve shipping cost
            // shipping_cost is now provided by checkout (from ShippingService), not CartService
            $shippingCost = $addressData['shipping_cost'] ?? $cartData['shipping_cost'];
            $subtotal = $cartData['subtotal'];
            $discountAmount = $cartData['discount_amount'] ?? 0;
            $total = ($subtotal - $discountAmount) + $shippingCost;

            // 5. Create Order
            $order = Order::create([
                'user_id'          => $user->id,
                'order_number'     => $orderNumber,
                'status'           => 'pending',
                'recipient_name'   => $addressData['recipient_name'],
                'phone'            => $addressData['phone'],
                'address'          => $addressData['address'],
                'city'             => $addressData['city'],
                'postal_code'      => $addressData['postal_code'],
                'notes'            => $addressData['notes'] ?? null,
                'subtotal'         => $subtotal,
                'shipping_cost'    => $shippingCost,
                'total'            => $total > 0 ? $total : 0,
                'payment_method'   => $paymentMethod,
                // Shipping snapshot
                'province'         => $addressData['province'] ?? null,
                'district'         => $addressData['district'] ?? null,
                'courier_name'     => $addressData['courier_name'] ?? null,
                'courier_service'  => $addressData['courier_service'] ?? null,
                'shipping_type'    => $addressData['shipping_type'] ?? null,
            ]);

            // 6. Create Order Items (snapshot)
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'product_sku' => $item->product->sku,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->product->price * $item->quantity,
                ]);
            }

            return $order;
        });

        // 6. Dispatch OrderCreated Event AFTER transaction commits
        // This ensures the order is persisted and visible to queue jobs
        event(new OrderCreated($order));

        return $order;
    }

    /**
     * Update order status.
     */
    public function updateStatus(Order $order, string $newStatus): Order
    {
        $allowedStatus = ['pending', 'paid', 'processing', 'shipped', 'delivered', 'completed', 'cancelled'];
        if (!in_array($newStatus, $allowedStatus)) {
            throw new Exception("Status '{$newStatus}' tidak valid.");
        }

        // Define valid transitions
        $transitions = [
            'pending' => ['paid', 'cancelled'],
            'paid' => ['processing', 'cancelled'],
            'processing' => ['shipped'],
            'shipped' => ['delivered'],
            'delivered' => ['completed'],
            'completed' => [],
            'cancelled' => [],
        ];

        return DB::transaction(function () use ($order, $newStatus, $transitions) {
            // Lock the order for atomic status update
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->first();
            
            $currentStatus = $lockedOrder->status;
            if (!in_array($newStatus, $transitions[$currentStatus])) {
                throw new Exception("Tidak dapat mengubah status dari '{$currentStatus}' ke '{$newStatus}'.");
            }

            $updateData = ['status' => $newStatus];
            
            if ($newStatus === 'paid') {
                $updateData['paid_at'] = now();
            }

            $lockedOrder->update($updateData);

            // If transitioned to cancelled, restore stock and cancel payment
            if ($newStatus === 'cancelled') {
                $this->restoreStock($lockedOrder);
                
                // Cancel pending payment if exists
                $payment = \App\Models\Payment::where('order_id', $lockedOrder->id)->where('status', 'pending')->first();
                if ($payment) {
                    $payment->update(['status' => 'cancelled']);
                }
            }

            return $lockedOrder;
        });
    }

    /**
     * Cancel an order (by Customer or Admin).
     */
    public function cancelOrder(Order $order): Order
    {
        // State checking is now safely and atomically handled inside updateStatus()
        return $this->updateStatus($order, 'cancelled');
    }

    /**
     * Restore stock for a cancelled order.
     */
    protected function restoreStock(Order $order): void
    {
        foreach ($order->items as $item) {
            if ($item->product_id) {
                $product = Product::where('id', $item->product_id)->lockForUpdate()->first();
                if ($product) {
                    $product->increment('stock', $item->quantity);
                    
                    \App\Models\StockMovement::create([
                        'product_id' => $product->id,
                        'type' => 'RETURN',
                        'quantity' => $item->quantity,
                        'reference_type' => get_class($order),
                        'reference_id' => $order->id,
                        'notes' => 'Stock restored due to order cancellation'
                    ]);
                }
            }
        }
    }

    /**
     * Compensate an order if pipeline fails.
     */
    public function compensateOrder(Order $order): void
    {
        Log::warning('Triggering compensation for order', ['order_id' => $order->id]);

        DB::transaction(function () use ($order) {
            $order->update(['pipeline_status' => 'failed']);

            if (in_array($order->processing_stage, ['stock_reduced', 'payment_created', 'cart_cleared', 'completed'])) {
                $this->restoreStock($order);
                Log::info('Compensation: Stock restored', ['order_id' => $order->id]);
            }

            if (in_array($order->processing_stage, ['payment_created', 'cart_cleared', 'completed'])) {
                $payment = \App\Models\Payment::where('order_id', $order->id)->first();
                if ($payment && $payment->status === 'pending') {
                    $payment->update(['status' => 'failed']);
                }
                Log::info('Compensation: Payment session marked as failed locally', ['order_id' => $order->id]);
            }

            if ($order->status !== 'cancelled') {
                $order->update(['status' => 'cancelled']);
            }
        });
    }
}
