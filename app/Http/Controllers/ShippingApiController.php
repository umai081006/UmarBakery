<?php

namespace App\Http\Controllers;

use App\Models\DeliveryZone;
use App\Services\ShippingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Shipping API Controller
 *
 * Provides JSON endpoints for the cascading address dropdowns
 * and realtime shipping rate fetching (used via AJAX/fetch).
 */
class ShippingApiController extends Controller
{
    protected ShippingService $shippingService;

    public function __construct(ShippingService $shippingService)
    {
        $this->shippingService = $shippingService;
    }

    /**
     * GET /shipping/provinces
     * Returns all active provinces from admin delivery zones.
     */
    public function provinces(): JsonResponse
    {
        $provinces = DeliveryZone::provinces();
        return response()->json($provinces);
    }

    /**
     * GET /shipping/cities?province=Jawa+Tengah
     * Returns cities for a given province.
     */
    public function cities(Request $request): JsonResponse
    {
        $province = $request->query('province', '');
        if (!$province) {
            return response()->json([]);
        }
        $cities = DeliveryZone::citiesFor($province);
        return response()->json($cities);
    }

    /**
     * GET /shipping/districts?province=Jawa+Tengah&city=Wonogiri
     * Returns districts (kecamatan) with their zone IDs and postal codes.
     */
    public function districts(Request $request): JsonResponse
    {
        $province = $request->query('province', '');
        $city = $request->query('city', '');

        if (!$province || !$city) {
            return response()->json([]);
        }

        $districts = DeliveryZone::districtsFor($province, $city);
        return response()->json($districts);
    }

    /**
     * POST /shipping/rates
     * Returns available shipping rates for a destination.
     * Called via AJAX on checkout page.
     */
    public function rates(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'province'   => 'required|string|max:100',
            'city'       => 'required|string|max:100',
            'district'   => 'nullable|string|max:100',
            'weight'     => 'nullable|integer|min:1',
            'order_value' => 'nullable|integer|min:0',
        ]);

        $rates = $this->shippingService->getRates(
            $validated['province'],
            $validated['city'],
            $validated['district'] ?? '',
            $validated['weight'] ?? 500,
            $validated['order_value'] ?? 0,
        );

        if (empty($rates)) {
            return response()->json([
                'available' => false,
                'message' => 'Maaf, wilayah Anda belum dapat kami layani. Silakan hubungi kami untuk informasi lebih lanjut.',
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
