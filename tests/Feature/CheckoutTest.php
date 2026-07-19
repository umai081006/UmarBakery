<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Cart;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_validation_returns_json()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $response = $this->actingAs($user)
            ->postJson(route('checkout.store'), []);

        $this->assertEquals(422, $response->getStatusCode(), "Actual status: " . $response->getStatusCode() . " Content: " . $response->getContent());
    }

    public function test_checkout_shipping_rates_uses_dynamic_address()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $address = Address::create([
            'user_id' => $user->id,
            'recipient_name' => 'Test User',
            'phone' => '08123456789',
            'address' => 'Test Address',
            'province' => 'Jawa Barat',
            'city' => 'Kota Depok',
            'district' => 'Tapos',
            'postal_code' => '16458',
            'biteship_area_id' => 'IDNP9IDNC111IDND272IDZ16458',
        ]);
        
        $product = Product::factory()->create(['price' => 10000]);
        Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($user)
            ->getJson(route('checkout.shipping_rates', ['address_id' => $address->id]));

        $response->assertStatus(200);
        $response->assertJsonStructure(['available', 'message', 'rates']);
    }

    public function test_checkout_fails_gracefully_with_json_on_invalid_address()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        
        $response = $this->actingAs($user)
            ->postJson(route('checkout.store'), [
                'address_id' => 9999,
                'courier_name' => 'JNE',
                'courier_service' => 'REG',
                'shipping_type' => 'biteship',
                'shipping_price' => 10000,
            ], [
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $this->assertNotEquals(302, $response->getStatusCode(), "Should never return 302");
        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Alamat tidak ditemukan.',
            ]);
    }

    public function test_checkout_never_returns_302_on_validation_failure_with_fetch_headers()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        // Mimicking frontend payload EXACTLY
        $response = $this->actingAs($user)
            ->post(route('checkout.store'), [], [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $this->assertNotEquals(302, $response->getStatusCode(), "Validation failure returned 302 instead of 422 JSON");
        $response->assertStatus(422);
    }
}
