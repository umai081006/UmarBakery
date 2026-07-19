<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrderSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_access_other_users_order()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $orderData = [
            'user_id' => $userA->id,
            'order_number' => 'ORD-12345-' . rand(1000, 9999),
            'status' => 'pending',
            'recipient_name' => 'John',
            'phone' => '123',
            'address' => 'abc',
            'city' => 'def',
            'postal_code' => '123',
            'subtotal' => 10000,
            'shipping_cost' => 5000,
            'total' => 15000,
            'payment_method' => 'midtrans',
            'courier_name' => 'JNE',
            'courier_service' => 'REG',
            'shipping_type' => 'biteship',
        ];
        $orderA = Order::create($orderData);

        // User B tries to view Order A
        $response = $this->actingAs($userB)->get(route('customer.orders.show', $orderA->id));
        $response->assertStatus(404); // Using firstOrFail() will throw 404
    }

    public function test_user_cannot_cancel_other_users_order()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $orderData = [
            'user_id' => $userA->id,
            'order_number' => 'ORD-12345-' . rand(1000, 9999),
            'status' => 'pending',
            'recipient_name' => 'John',
            'phone' => '123',
            'address' => 'abc',
            'city' => 'def',
            'postal_code' => '123',
            'subtotal' => 10000,
            'shipping_cost' => 5000,
            'total' => 15000,
            'payment_method' => 'midtrans',
            'courier_name' => 'JNE',
            'courier_service' => 'REG',
            'shipping_type' => 'biteship',
        ];
        $orderA = Order::create($orderData);

        // User B tries to cancel Order A
        $response = $this->actingAs($userB)->post(route('customer.orders.cancel', $orderA->id));
        $response->assertStatus(404);
    }

    public function test_user_cannot_upload_proof_for_other_users_order()
    {
        Storage::fake('cloudinary');

        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $orderData = [
            'user_id' => $userA->id,
            'order_number' => 'ORD-12345-' . rand(1000, 9999),
            'status' => 'pending',
            'recipient_name' => 'John',
            'phone' => '123',
            'address' => 'abc',
            'city' => 'def',
            'postal_code' => '123',
            'subtotal' => 10000,
            'shipping_cost' => 5000,
            'total' => 15000,
            'payment_method' => 'midtrans',
            'courier_name' => 'JNE',
            'courier_service' => 'REG',
            'shipping_type' => 'biteship',
        ];
        $orderA = Order::create($orderData);

        $file = UploadedFile::fake()->image('proof.jpg');

        // User B tries to upload proof to Order A
        $response = $this->actingAs($userB)->post(route('customer.orders.upload_proof', $orderA->id), [
            'payment_proof' => $file
        ]);

        $response->assertStatus(404);
    }
}
