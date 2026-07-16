<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\Payments\PaymentService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    protected $cartService;
    protected $orderService;
    protected $paymentService;

    public function __construct(CartService $cartService, OrderService $orderService, PaymentService $paymentService)
    {
        $this->cartService = $cartService;
        $this->orderService = $orderService;
        $this->paymentService = $paymentService;
    }

    /**
     * Display checkout page.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $cartData = $this->cartService->getCartWithTotal($request->user());
        
        if ($cartData['items']->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        $cartData['addresses'] = $request->user()->addresses()->orderByDesc('is_default')->latest()->get();

        return view('customer.checkout', $cartData);
    }

    /**
     * Handle checkout and order creation.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $address = \App\Models\Address::where('id', $request->address_id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            $addressData = [
                'recipient_name' => $address->recipient_name,
                'phone' => $address->phone,
                'address' => $address->address,
                'city' => $address->city,
                'postal_code' => $address->postal_code,
                'notes' => $request->notes,
            ];

            $order = $this->orderService->createOrder(
                $request->user(),
                $addressData,
                'midtrans' // Using midtrans as payment method identifier
            );

            // Note: Snap Token generation and other side-effects 
            // are now handled via OrderCreated event listeners!

            return redirect()->route('customer.orders.show', $order->id)
                ->with('success', 'Pesanan berhasil dibuat! Silakan selesaikan pembayaran Anda.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
}
