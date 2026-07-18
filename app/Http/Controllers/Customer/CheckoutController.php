<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\Payments\PaymentService;
use App\Services\ShippingService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    protected $cartService;
    protected $orderService;
    protected $paymentService;
    protected $shippingService;

    public function __construct(
        CartService $cartService,
        OrderService $orderService,
        PaymentService $paymentService,
        ShippingService $shippingService
    ) {
        $this->cartService = $cartService;
        $this->orderService = $orderService;
        $this->paymentService = $paymentService;
        $this->shippingService = $shippingService;
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

        $addresses = $request->user()->addresses()
            ->with('deliveryZone')
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        $cartData['addresses'] = $addresses;

        // Pre-load shipping rates for default address if it has province/city
        $defaultAddress = $addresses->firstWhere('is_default', true) ?? $addresses->first();
        $shippingRates = [];

        if ($defaultAddress && $defaultAddress->province && $defaultAddress->city) {
            // Lazy-resolve Biteship area ID if not yet saved
            if (empty($defaultAddress->biteship_area_id)) {
                $this->shippingService->resolveAndSaveAreaId($defaultAddress);
                $defaultAddress->refresh();
            }

            $totalWeight = $cartData['items']->sum(fn($item) => ($item->product->weight ?? 500) * $item->quantity);

            $shippingRates = $this->shippingService->getRates(
                $defaultAddress->province,
                $defaultAddress->city,
                $defaultAddress->district ?? '',
                (int) $totalWeight,
                (int) $cartData['subtotal'],
                $defaultAddress->biteship_area_id,
            );
        }

        $cartData['shippingRates'] = $shippingRates;
        $cartData['defaultAddressId'] = $defaultAddress?->id;

        return view('customer.checkout', $cartData);
    }

    /**
     * Handle checkout and order creation.
     * SECURITY: All shipping cost validation is done server-side.
     * The shipping_price from the frontend is ONLY used to identify which rate was selected;
     * the actual price is always re-fetched and validated from the server.
     */
    public function store(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('[checkout_trace] ENTER_STORE', [
            'method' => $request->method(),
            'accept_header' => $request->header('Accept'),
            'x_requested_with' => $request->header('X-Requested-With'),
            'content_type' => $request->header('Content-Type'),
            'wants_json' => $request->wantsJson(),
            'expects_json' => $request->expectsJson(),
        ]);

        \Illuminate\Support\Facades\Log::info('[checkout_trace] AUTH_CHECK', [
            'user_id' => $request->user()?->id,
        ]);

        \Illuminate\Support\Facades\Log::info('[checkout_trace] VALIDATION_START', [
            'address_id' => $request->input('address_id'),
            'courier_name' => $request->input('courier_name'),
            'shipping_type' => $request->input('shipping_type'),
            'shipping_price' => $request->input('shipping_price'),
        ]);

        try {
            $request->validate([
                'address_id'        => 'required|integer',
                'courier_name'      => 'required|string|max:100',
                'courier_service'   => 'required|string|max:50',
                'shipping_type'     => 'required|in:biteship,manual',
                'shipping_price'    => 'required|integer|min:0', 
                'notes'             => 'nullable|string|max:500',
            ]);
            \Illuminate\Support\Facades\Log::info('[checkout_trace] VALIDATION_PASSED');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\Log::info('[checkout_trace] EXCEPTION', [
                'class' => get_class($e),
                'message' => $e->getMessage(),
                'expects_json' => $request->expectsJson(),
                'wants_json' => $request->wantsJson(),
                'accept' => $request->header('Accept'),
                'status' => $e->status,
            ]);
            throw $e;
        }

        try {
            // 1. Verify address belongs to authenticated user
            $address = Address::where('id', $request->address_id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            if (!$address->province || !$address->city) {
                return $this->respondError($request, 'Alamat tidak lengkap. Silakan edit alamat dan pilih provinsi/kota.');
            }

            // 2. SERVER-SIDE: Re-fetch shipping rates to validate the selection
            $cartData = $this->cartService->getCartWithTotal($request->user());
            if ($cartData['items']->isEmpty()) {
                return $this->respondError($request, 'Keranjang Anda kosong.', route('cart.index'));
            }

            // Lazy-resolve Biteship area ID if not yet saved on this address
            if (empty($address->biteship_area_id)) {
                $this->shippingService->resolveAndSaveAreaId($address);
                $address->refresh();
            }

            $totalWeight = $cartData['items']->sum(fn($item) => ($item->product->weight ?? 500) * $item->quantity);

            \Illuminate\Support\Facades\Log::info('[checkout_trace] SHIPPING_START', [
                'province' => $address->province,
                'city' => $address->city,
                'total_weight' => $totalWeight,
            ]);

            $serverRates = $this->shippingService->getRates(
                $address->province,
                $address->city,
                $address->district ?? '',
                (int) $totalWeight,
                (int) $cartData['subtotal'],
                $address->biteship_area_id,
            );

            if (empty($serverRates)) {
                return $this->respondError($request, 'Ongkos kirim tidak tersedia untuk alamat ini. Silakan pilih alamat lain.');
            }

            // 3. Match the user's selection to a valid server-computed rate
            $matchedRate = null;
            foreach ($serverRates as $rate) {
                if (
                    strtolower($rate['courier_name']) === strtolower($request->courier_name) &&
                    strtolower($rate['courier_service']) === strtolower($request->courier_service) &&
                    $rate['type'] === $request->shipping_type
                ) {
                    $matchedRate = $rate;
                    break;
                }
            }

            if (!$matchedRate) {
                foreach ($serverRates as $rate) {
                    if ($rate['type'] === $request->shipping_type && $rate['price'] === (int) $request->shipping_price) {
                        $matchedRate = $rate;
                        break;
                    }
                }
            }

            if (!$matchedRate) {
                return $this->respondError($request, 'Opsi pengiriman yang dipilih tidak valid atau sudah tidak tersedia. Silakan pilih ulang.');
            }

            \Illuminate\Support\Facades\Log::info('[checkout_trace] SHIPPING_PASSED', [
                'selected_rate' => $matchedRate['courier_name'],
                'price' => $matchedRate['price'],
            ]);

            $addressData = [
                'recipient_name'  => $address->recipient_name,
                'phone'           => $address->phone,
                'address'         => $address->address,
                'province'        => $address->province,
                'city'            => $address->city,
                'district'        => $address->district,
                'postal_code'     => $address->postal_code,
                'detail_address'  => $address->detail_address,
                'notes'           => $request->notes,
                'courier_name'        => $matchedRate['courier_name'],
                'courier_service'     => $matchedRate['courier_service'],
                'shipping_type'       => $matchedRate['type'],
                'shipping_cost'       => $matchedRate['price'], 
                'origin_area_id'      => config('services.biteship.origin_area_id'),
                'destination_area_id' => $address->biteship_area_id,
            ];

            // 5. Create order (stock validation + snapshot inside)
            $order = $this->orderService->createOrder(
                $request->user(),
                $addressData,
                'midtrans'
            );

            \Illuminate\Support\Facades\Log::info('[checkout_trace] MIDTRANS_START');

            // 6. Create Midtrans payment with final SERVER-COMPUTED total
            $payment = $this->paymentService->createPayment($order);

            \Illuminate\Support\Facades\Log::info('[checkout_trace] MIDTRANS_RESPONSE', [
                'status' => 'success',
                'has_redirect' => !empty($payment['redirect_url'])
            ]);

            // 7. Clear cart after order is safely created
            $this->cartService->clearCart($request->user());

            // 8. Redirect to Midtrans Snap payment page
            $redirectUrl = !empty($payment['redirect_url']) ? $payment['redirect_url'] : route('customer.orders.show', $order->id);

            \Illuminate\Support\Facades\Log::info('[checkout_trace] SUCCESS_RESPONSE', [
                'redirect_url' => $redirectUrl
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'redirect_url' => $redirectUrl,
                    'message' => 'Pesanan berhasil dibuat! Silakan selesaikan pembayaran Anda.'
                ]);
            }

            return redirect($redirectUrl)->with('success', 'Pesanan berhasil dibuat! Silakan selesaikan pembayaran Anda.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Illuminate\Support\Facades\Log::info('[checkout_trace] EXCEPTION', [
                'class' => get_class($e),
                'message' => $e->getMessage(),
                'expects_json' => $request->expectsJson(),
                'wants_json' => $request->wantsJson(),
                'accept' => $request->header('Accept'),
            ]);
            return $this->respondError($request, 'Alamat tidak ditemukan.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::info('[checkout_trace] EXCEPTION', [
                'class' => get_class($e),
                'message' => $e->getMessage(),
                'expects_json' => $request->expectsJson(),
                'wants_json' => $request->wantsJson(),
                'accept' => $request->header('Accept'),
            ]);
            \Illuminate\Support\Facades\Log::error('Checkout store error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return $this->respondError($request, $e->getMessage());
        }
    }

    private function respondError(Request $request, string $message, string $redirect = null)
    {
        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 400);
        }
        $r = $redirect ? redirect($redirect) : redirect()->back()->withInput();
        return $r->with('error', $message);
    }

    /**
     * AJAX: Get shipping rates for a specific address.
     */
    public function shippingRates(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
        ]);

        $address = Address::where('id', $request->address_id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if (!$address->province || !$address->city) {
            return response()->json([
                'available' => false,
                'message'   => 'Alamat ini belum memiliki informasi kota/provinsi. Silakan edit alamat terlebih dahulu.',
                'rates'     => [],
            ]);
        }

        // Lazy-resolve Biteship area ID if not already stored
        if (empty($address->biteship_area_id)) {
            $this->shippingService->resolveAndSaveAreaId($address);
            $address->refresh();
        }

        $cartData    = $this->cartService->getCartWithTotal($request->user());
        $totalWeight = $cartData['items']->sum(fn($item) => ($item->product->weight ?? 500) * $item->quantity);

        $rates = $this->shippingService->getRates(
            $address->province,
            $address->city,
            $address->district ?? '',
            (int) $totalWeight,
            (int) $cartData['subtotal'],
            $address->biteship_area_id,
        );

        if (empty($rates)) {
            return response()->json([
                'available' => false,
                'message' => 'Maaf, wilayah Anda belum dapat kami layani.',
                'rates' => [],
            ]);
        }

        return response()->json([
            'available' => true,
            'message' => '',
            'rates' => $rates,
        ]);
    }
}

