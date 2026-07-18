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
     * Helper to fetch and cache from Emsifa API
     */
    protected function fetchEmsifa(string $path)
    {
        $cacheKey = 'emsifa_' . md5($path);
        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 86400 * 30, function () use ($path) {
            try {
                $url = "https://www.emsifa.com/api-wilayah-indonesia/api/{$path}";
                $response = \Illuminate\Support\Facades\Http::timeout(10)->get($url);
                if ($response->successful()) {
                    return $response->json();
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("Emsifa API error: " . $e->getMessage());
            }
            return [];
        });
    }

    /**
     * GET /shipping/provinces
     * Returns all active provinces (Now fetching all of Indonesia).
     */
    public function provinces(): JsonResponse
    {
        $provinces = $this->fetchEmsifa('provinces.json');
        
        $names = collect($provinces)
            ->pluck('name')
            ->map(fn($name) => ucwords(strtolower($name)))
            ->sort()
            ->values()
            ->toArray();
            
        return response()->json($names);
    }

    /**
     * GET /shipping/cities?province=Jawa+Tengah
     * Returns cities for a given province.
     */
    public function cities(Request $request): JsonResponse
    {
        $provinceName = strtoupper($request->query('province', ''));
        if (!$provinceName) return response()->json([]);

        $provinces = $this->fetchEmsifa('provinces.json');
        $province = collect($provinces)->firstWhere('name', $provinceName);
        
        if (!$province) return response()->json([]);

        $cities = $this->fetchEmsifa("regencies/{$province['id']}.json");
        
        $names = collect($cities)
            ->pluck('name')
            ->map(fn($name) => ucwords(strtolower($name)))
            ->sort()
            ->values()
            ->toArray();

        return response()->json($names);
    }

    /**
     * GET /shipping/districts?province=Jawa+Tengah&city=Wonogiri
     * Returns districts (kecamatan).
     */
    public function districts(Request $request): JsonResponse
    {
        $provinceName = strtoupper($request->query('province', ''));
        $cityName = strtoupper($request->query('city', ''));
        
        if (!$provinceName || !$cityName) return response()->json([]);

        $provinces = $this->fetchEmsifa('provinces.json');
        $province = collect($provinces)->firstWhere('name', $provinceName);
        if (!$province) return response()->json([]);

        $cities = $this->fetchEmsifa("regencies/{$province['id']}.json");
        $city = collect($cities)->firstWhere('name', $cityName);
        if (!$city) return response()->json([]);

        $districts = $this->fetchEmsifa("districts/{$city['id']}.json");
        
        // Match frontend expectation: array of objects with 'district' key
        $formatted = collect($districts)
            ->map(function ($d) {
                return [
                    'district' => ucwords(strtolower($d['name'])),
                    'postal_code' => '', // Emsifa doesn't provide postal codes reliably here, so leave empty for manual input
                ];
            })
            ->sortBy('district')
            ->values()
            ->toArray();

        return response()->json($formatted);
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
