<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->category = Category::create(['name' => 'Test', 'slug' => 'test']);
        $this->product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Test Product',
            'slug' => 'test-product',
            'sku' => 'UB-PRD-000001',
            'description' => 'Test',
            'price' => 10000,
            'stock' => 100,
            'weight' => 500,
            'is_active' => true
        ]);
    }

    public function test_payment_pending()
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'order_number' => 'ORD-1',
            'status' => 'pending',
            'recipient_name' => 'Test',
            'phone' => '123',
            'address' => 'Test',
            'city' => 'Test',
            'postal_code' => '123',
            'subtotal' => 10000,
            'shipping_cost' => 0,
            'total' => 10000,
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'status' => 'pending',
            'amount' => 10000,
            'expires_at' => now()->addMinutes(60)
        ]);

        $this->assertEquals('pending', $payment->status);
        $this->assertTrue($payment->expires_at->gt(now()));
    }

    public function test_payment_expiry_after_60_minutes()
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'order_number' => 'ORD-2',
            'status' => 'pending',
            'recipient_name' => 'Test',
            'phone' => '123',
            'address' => 'Test',
            'city' => 'Test',
            'postal_code' => '123',
            'subtotal' => 10000,
            'shipping_cost' => 0,
            'total' => 10000,
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'status' => 'pending',
            'amount' => 10000,
            'expires_at' => now()->subMinutes(5) // Expired
        ]);

        \App\Models\OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_sku' => $this->product->sku,
            'price' => 10000,
            'quantity' => 1,
            'subtotal' => 10000,
        ]);
        
        // Simulate stock deduction at creation
        $this->product->decrement('stock', 1);
        $this->assertEquals(99, $this->product->stock);

        $this->artisan('orders:expire-pending')->assertSuccessful();

        $payment->refresh();
        $order->refresh();

        $this->assertEquals('expired', $payment->status);
        $this->assertEquals('cancelled', $order->status);
        $this->assertEquals(100, $this->product->fresh()->stock);

        // Re-run command to test idempotency (no duplicate stock changes)
        $this->artisan('orders:expire-pending')->assertSuccessful();
        $this->assertEquals(100, $this->product->fresh()->stock);
    }

    public function test_paid_payment_cannot_expire()
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'order_number' => 'ORD-3',
            'status' => 'paid',
            'recipient_name' => 'Test',
            'phone' => '123',
            'address' => 'Test',
            'city' => 'Test',
            'postal_code' => '123',
            'subtotal' => 10000,
            'shipping_cost' => 0,
            'total' => 10000,
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'status' => 'paid',
            'amount' => 10000,
            'expires_at' => now()->subMinutes(5) // Time passed
        ]);

        $this->artisan('orders:expire-pending')->assertSuccessful();

        $payment->refresh();
        $order->refresh();

        $this->assertEquals('paid', $payment->status);
        $this->assertEquals('paid', $order->status);
    }
    
    public function test_sku_auto_generation()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->post(route('admin.products.store'), [
            'category_id' => $this->category->id,
            'name' => 'Auto SKU Product',
            'description' => 'Test',
            'price' => 10000,
            'stock' => 10,
            'weight' => 500,
            // 'sku' is intentionally omitted
        ]);

        $response->assertSessionHasNoErrors();
        $product = Product::where('name', 'Auto SKU Product')->first();
        
        $this->assertNotNull($product);
        $this->assertStringStartsWith('UB-PRD-', $product->sku);
    }
}
