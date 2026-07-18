<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use App\Services\Promotions\PromotionService;
use Exception;

class CartService
{
    protected $promotionService;

    public function __construct(PromotionService $promotionService)
    {
        $this->promotionService = $promotionService;
    }

    /**
     * Add item to user's cart.
     */
    public function addItem(User $user, int $productId, int $quantity = 1): Cart
    {
        $product = Product::where('id', $productId)->where('is_active', true)->first();

        if (!$product) {
            throw new Exception('Produk tidak ditemukan atau tidak aktif.');
        }

        if ($product->stock < $quantity) {
            throw new Exception("Stok tidak mencukupi. Tersedia: {$product->stock}");
        }

        $cart = Cart::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($cart) {
            $newQuantity = $cart->quantity + $quantity;
            if ($product->stock < $newQuantity) {
                throw new Exception("Stok tidak mencukupi untuk jumlah total. Tersedia: {$product->stock}");
            }
            $cart->update(['quantity' => $newQuantity]);
        } else {
            $cart = Cart::create([
                'user_id' => $user->id,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }

        return $cart;
    }

    /**
     * Update quantity of a cart item.
     */
    public function updateQuantity(User $user, int $cartId, int $quantity): Cart
    {
        $cart = Cart::where('id', $cartId)->where('user_id', $user->id)->first();

        if (!$cart) {
            throw new Exception('Item keranjang tidak ditemukan.');
        }

        if ($quantity <= 0) {
            $cart->delete();
            return $cart;
        }

        $product = $cart->product;
        if ($product->stock < $quantity) {
            throw new Exception("Stok tidak mencukupi. Tersedia: {$product->stock}");
        }

        $cart->update(['quantity' => $quantity]);
        return $cart;
    }

    /**
     * Remove item from cart.
     */
    public function removeItem(User $user, int $cartId): void
    {
        Cart::where('id', $cartId)->where('user_id', $user->id)->delete();
    }

    /**
     * Get user's cart items with pricing and totals.
     */
    public function getCartWithTotal(User $user): array
    {
        $items = Cart::with('product')
            ->where('user_id', $user->id)
            ->get()
            ->filter(fn ($item) => $item->product && $item->product->is_active);

        $subtotal = 0;
        foreach ($items as $item) {
            $item->item_total = $item->product->price * $item->quantity;
            $subtotal += $item->item_total;
        }

        $shipping_cost = 0; // Shipping is calculated at checkout based on address + courier selection
        $discount_amount = 0;
        $promoCode = session('applied_promo_code');
        $promoMessage = null;
        $isPromoValid = false;

        if ($promoCode) {
            $promoResult = $this->promotionService->calculateDiscount($promoCode, $subtotal);
            
            if ($promoResult['valid']) {
                $discount_amount = $promoResult['discount_amount'];
                $isPromoValid = true;
                $promoMessage = $promoResult['message'];
            } else {
                // If invalid, clear session
                session()->forget('applied_promo_code');
                $promoMessage = $promoResult['message'];
            }
        }

        $total = ($subtotal - $discount_amount) + $shipping_cost;

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'shipping_cost' => $shipping_cost,
            'discount_amount' => $discount_amount,
            'promo_code' => $isPromoValid ? $promoCode : null,
            'promo_message' => $promoMessage,
            'total' => $total > 0 ? $total : 0,
        ];
    }

    /**
     * Clear all items in user's cart.
     */
    public function clearCart(User $user): void
    {
        Cart::where('user_id', $user->id)->delete();
        session()->forget('applied_promo_code');
    }
}
