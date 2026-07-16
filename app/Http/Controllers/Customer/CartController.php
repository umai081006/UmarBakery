<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * View cart page.
     */
    public function index(Request $request): View
    {
        $cartData = $this->cartService->getCartWithTotal($request->user());
        return view('customer.cart', $cartData);
    }

    /**
     * Add product to cart.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            $this->cartService->addItem(
                $request->user(),
                $request->input('product_id'),
                $request->input('quantity', 1)
            );
            return redirect()->back()->with('success', 'Roti berhasil ditambahkan ke keranjang!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update quantity of a cart item.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        try {
            $this->cartService->updateQuantity(
                $request->user(),
                $id,
                $request->input('quantity')
            );
            return redirect()->back()->with('success', 'Keranjang berhasil diperbarui!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove item from cart.
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        try {
            $this->cartService->removeItem($request->user(), $id);
            return redirect()->back()->with('success', 'Item berhasil dihapus dari keranjang.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Apply Promo Code.
     */
    public function applyPromo(Request $request): RedirectResponse
    {
        $request->validate([
            'promo_code' => 'required|string|max:50',
        ]);

        session()->put('applied_promo_code', strtoupper($request->promo_code));

        return redirect()->back()->with('success', 'Promo sedang diproses.');
    }

    /**
     * Remove Promo Code.
     */
    public function removePromo(): RedirectResponse
    {
        session()->forget('applied_promo_code');
        return redirect()->back()->with('success', 'Promo berhasil dihapus.');
    }
}
