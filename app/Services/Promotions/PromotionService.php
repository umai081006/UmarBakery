<?php

namespace App\Services\Promotions;

use App\Models\Promotion;

class PromotionService
{
    /**
     * Calculate discount for a given promo code.
     */
    public function calculateDiscount(?string $code, float $subtotal): array
    {
        if (!$code) {
            return [
                'valid' => false,
                'discount_amount' => 0,
                'final_total' => $subtotal,
                'promotion_id' => null,
                'message' => 'Tidak ada promo.',
            ];
        }

        $promotion = Promotion::where('code', $code)
            ->where('is_active', true)
            ->first();

        if (!$promotion) {
            return [
                'valid' => false,
                'discount_amount' => 0,
                'final_total' => $subtotal,
                'promotion_id' => null,
                'message' => 'Kode promo tidak ditemukan atau tidak aktif.',
            ];
        }

        $now = now();
        if ($promotion->start_date && $promotion->start_date > $now) {
            return [
                'valid' => false,
                'discount_amount' => 0,
                'final_total' => $subtotal,
                'promotion_id' => null,
                'message' => 'Promo belum dimulai.',
            ];
        }

        if ($promotion->end_date && $promotion->end_date < $now) {
            return [
                'valid' => false,
                'discount_amount' => 0,
                'final_total' => $subtotal,
                'promotion_id' => null,
                'message' => 'Promo sudah kedaluwarsa.',
            ];
        }

        // Calculate discount
        $discountAmount = 0;
        if ($promotion->type === 'percent') {
            $discountAmount = ($promotion->value / 100) * $subtotal;
        } else {
            $discountAmount = $promotion->value;
        }

        // Cap discount to subtotal
        if ($discountAmount > $subtotal) {
            $discountAmount = $subtotal;
        }

        return [
            'valid' => true,
            'discount_amount' => $discountAmount,
            'final_total' => $subtotal - $discountAmount,
            'promotion_id' => $promotion->id,
            'message' => 'Promo berhasil digunakan!',
        ];
    }
}
