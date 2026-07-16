<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Store a new review for a product.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $user = $request->user();

        try {
            // Verify order belongs to user and is completed
            $order = Order::where('id', $request->order_id)
                ->where('user_id', $user->id)
                ->where('status', 'completed')
                ->firstOrFail();

            // Verify the product was actually in this order
            $orderItem = $order->items()
                ->where('product_id', $request->product_id)
                ->firstOrFail();

            // Check if already reviewed
            $exists = Review::where('order_id', $order->id)
                ->where('product_id', $request->product_id)
                ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Anda sudah memberikan review untuk produk ini pada pesanan ini.');
            }

            Review::create([
                'user_id' => $user->id,
                'product_id' => $request->product_id,
                'order_id' => $order->id,
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);

            return redirect()->back()->with('success', 'Review berhasil ditambahkan! Terima kasih atas ulasan Anda.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan review. Pastikan pesanan sudah selesai.');
        }
    }

    /**
     * Update an existing review.
     */
    public function update(Request $request, Review $review): RedirectResponse
    {
        if ($review->user_id !== $request->user()->id) {
            return redirect()->back()->with('error', 'Unauthorized.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->back()->with('success', 'Review berhasil diperbarui.');
    }
}
