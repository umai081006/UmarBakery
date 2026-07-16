<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\CloudinaryService;
use App\Services\OrderService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    protected $orderService;
    protected $cloudinaryService;

    public function __construct(OrderService $orderService, CloudinaryService $cloudinaryService)
    {
        $this->orderService = $orderService;
        $this->cloudinaryService = $cloudinaryService;
    }

    /**
     * Display customer order history.
     */
    public function index(Request $request): View
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    /**
     * Display a specific order detail.
     */
    public function show(Request $request, int $id): View|RedirectResponse
    {
        $order = Order::with(['items.product', 'reviews'])
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return view('customer.orders.show', compact('order'));
    }

    /**
     * Upload payment proof for an order.
     */
    public function uploadPaymentProof(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'payment_proof' => 'required|image|max:2048', // 2MB max
        ]);

        $order = Order::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Bukti pembayaran hanya dapat diupload untuk pesanan berstatus pending.');
        }

        try {
            if ($request->hasFile('payment_proof')) {
                // Delete old proof if exists
                if ($order->payment_proof) {
                    $this->cloudinaryService->delete($order->payment_proof);
                }

                $url = $this->cloudinaryService->upload($request->file('payment_proof'), 'payment-proofs');
                
                $order->update([
                    'payment_proof' => $url
                ]);

                return redirect()->back()->with('success', 'Bukti pembayaran berhasil diupload! Menunggu konfirmasi admin.');
            }
            
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengupload bukti: ' . $e->getMessage());
        }
    }

    /**
     * Cancel order.
     */
    public function cancel(Request $request, int $id): RedirectResponse
    {
        $order = Order::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        try {
            $this->orderService->cancelOrder($order);
            return redirect()->back()->with('success', 'Pesanan berhasil dibatalkan.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
