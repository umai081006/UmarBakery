<?php

namespace App\Services;

use App\Models\DeliveryZone;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Hybrid Shipping Service
 *
 * Priority order:
 * 1. BiteShip realtime API (if configured and returns rates)
 * 2. Admin Delivery Zone manual rate (if zone exists for the address)
 * 3. No shipping available → returns empty array
 */
class ShippingService
{
    protected ?string $apiKey;
    protected ?string $originAreaId;
    protected bool $apiEnabled;

    public function __construct()
    {
        $this->apiKey = config('services.biteship.api_key') ?: null;
        $this->originAreaId = config('services.biteship.origin_area_id') ?: null;
        $this->apiEnabled = !empty($this->apiKey) && !empty($this->originAreaId);
    }

    /**
     * Get available shipping rates for a destination.
     * Returns array of shipping options, or empty array if none available.
     *
     * @param string $province
     * @param string $city
     * @param string $district
     * @param int $totalWeightGrams
     * @param int $orderValueIdr
     * @return array
     */
    public function getRates(string $province, string $city, string $district, int $totalWeightGrams, int $orderValueIdr): array
    {
        $rates = [];

        // Level 1: Try BiteShip API
        if ($this->apiEnabled) {
            try {
                $rates = $this->getBiteshipeRates($province, $city, $district, $totalWeightGrams, $orderValueIdr);
            } catch (\Throwable $e) {
                Log::error('ShippingService::getRates BiteShip exception', ['error' => $e->getMessage()]);
                $rates = [];
            }
        }

        // Level 2: Fallback to Admin Delivery Zone
        if (empty($rates)) {
            $rates = $this->getZoneRates($province, $city, $district);
        }

        if (empty($rates)) {
            Log::info('ShippingService: no rates available', compact('province', 'city', 'district'));
        }

        return $rates;
    }

    /**
     * Get rates by Biteship area ID directly (when stored on address).
     */
    public function getRatesByAreaId(string $destinationAreaId, int $totalWeightGrams, int $orderValueIdr): array
    {
        if (!$this->apiEnabled) {
            return [];
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(15)
                ->post('https://api.biteship.com/v1/rates/couriers', [
                    'origin_area_id'      => $this->originAreaId,
                    'destination_area_id' => $destinationAreaId,
                    'couriers'            => 'gosend,grab_express,anteraja,jne,sicepat,paxel,borzo',
                    'items' => [
                        [
                            'name'        => 'Umar Bakery Order',
                            'description' => 'Produk roti dan kue',
                            'value'       => $orderValueIdr,
                            'length'      => 30,
                            'width'       => 20,
                            'height'      => 15,
                            'weight'      => max($totalWeightGrams, 1),
                        ],
                    ],
                ]);

            if (!$response->successful()) {
                Log::warning('BiteShip getRatesByAreaId failed', ['status' => $response->status()]);
                return [];
            }

            $pricings = $response->json('pricing', []);
            $rates = [];
            foreach ($pricings as $pricing) {
                if (empty($pricing['price']) || $pricing['price'] <= 0) continue;
                $rates[] = [
                    'type'              => 'biteship',
                    'courier_name'      => $pricing['courier_name'] ?? $pricing['courier_code'] ?? 'Kurir',
                    'courier_service'   => $pricing['courier_service_code'] ?? 'REG',
                    'service_type'      => $pricing['type'] ?? 'reguler',
                    'price'             => (int) $pricing['price'],
                    'price_formatted'   => 'Rp ' . number_format($pricing['price'], 0, ',', '.'),
                    'estimated_delivery' => $pricing['shipment_duration_range'] ?? '1-3 hari',
                    'zone_id'           => null,
                ];
            }
            usort($rates, fn ($a, $b) => $a['price'] <=> $b['price']);
            return $rates;
        } catch (\Throwable $e) {
            Log::error('ShippingService::getRatesByAreaId exception', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Get area ID from BiteShip for a location.
     * Results cached 24 hours to reduce API calls.
     */
    public function searchAreas(string $query): array
    {
        if (!$this->apiEnabled) {
            return [];
        }

        $cacheKey = 'biteship_areas_' . md5($query);

        return Cache::remember($cacheKey, 86400, function () use ($query) {
            try {
                $response = Http::withToken($this->apiKey)
                    ->timeout(10)
                    ->get('https://api.biteship.com/v1/maps/areas', [
                        'countries' => 'ID',
                        'input' => $query,
                        'type' => 'single',
                    ]);

                if ($response->successful()) {
                    return $response->json('areas', []);
                }

                Log::warning('BiteShip area search failed', ['status' => $response->status(), 'query' => $query]);
                return [];
            } catch (\Exception $e) {
                Log::error('BiteShip area search exception', ['message' => $e->getMessage()]);
                return [];
            }
        });
    }

    /**
     * Get admin-defined delivery zone options.
     */
    protected function getZoneRates(string $province, string $city, string $district): array
    {
        $zone = DeliveryZone::findFor($province, $city, $district);

        if (!$zone || $zone->manual_shipping_cost <= 0) {
            return [];
        }

        return [
            [
                'type' => 'manual',
                'courier_name' => 'Pengiriman Lokal',
                'courier_service' => 'REGULER',
                'service_type' => 'reguler',
                'price' => $zone->manual_shipping_cost,
                'price_formatted' => 'Rp ' . number_format($zone->manual_shipping_cost, 0, ',', '.'),
                'estimated_delivery' => $zone->estimated_delivery ?? '1-3 hari',
                'zone_id' => $zone->id,
            ],
        ];
    }

    /**
     * Get realtime rates from BiteShip API.
     */
    protected function getBiteshipeRates(string $province, string $city, string $district, int $weightGrams, int $orderValue): array
    {
        if (!$this->apiEnabled) {
            return [];
        }

        // First, resolve destination area ID
        $destinationAreaId = $this->resolveAreaId($province, $city, $district);

        if (!$destinationAreaId) {
            Log::info('BiteShip: could not resolve area ID', compact('province', 'city', 'district'));
            return [];
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(15)
                ->post('https://api.biteship.com/v1/rates/couriers', [
                    'origin_area_id' => $this->originAreaId,
                    'destination_area_id' => $destinationAreaId,
                    'couriers' => 'gosend,grab_express,anteraja,jne,sicepat,paxel,borzo',
                    'items' => [
                        [
                            'name' => 'Umar Bakery Order',
                            'description' => 'Produk roti dan kue',
                            'value' => $orderValue,
                            'length' => 30,
                            'width' => 20,
                            'height' => 15,
                            'weight' => max($weightGrams, 1),
                        ],
                    ],
                ]);

            if (!$response->successful()) {
                Log::warning('BiteShip rates failed', ['status' => $response->status()]);
                return [];
            }

            $pricings = $response->json('pricing', []);

            $rates = [];
            foreach ($pricings as $pricing) {
                if (empty($pricing['price']) || $pricing['price'] <= 0) continue;

                $rates[] = [
                    'type' => 'biteship',
                    'courier_name' => $pricing['courier_name'] ?? $pricing['courier_code'] ?? 'Kurir',
                    'courier_service' => $pricing['courier_service_code'] ?? 'REG',
                    'service_type' => $pricing['type'] ?? 'reguler',
                    'price' => (int) $pricing['price'],
                    'price_formatted' => 'Rp ' . number_format($pricing['price'], 0, ',', '.'),
                    'estimated_delivery' => $pricing['shipment_duration_range'] ?? '1-3 hari',
                    'zone_id' => null,
                ];
            }

            // Sort by price ascending
            usort($rates, fn ($a, $b) => $a['price'] <=> $b['price']);

            return $rates;
        } catch (\Exception $e) {
            Log::error('BiteShip rates exception', ['message' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Resolve BiteShip area ID from province/city/district.
     * Tries district+city first, then city only.
     */
    protected function resolveAreaId(string $province, string $city, string $district): ?string
    {
        // Check if the delivery zone has a pre-saved area ID
        $zone = DeliveryZone::findFor($province, $city, $district);
        if ($zone && $zone->biteship_area_id) {
            return $zone->biteship_area_id;
        }

        // Search BiteShip API for area
        $searchQuery = trim("$district $city");
        $areas = $this->searchAreas($searchQuery);

        foreach ($areas as $area) {
            if (!empty($area['id'])) {
                // Optionally save to zone for future use
                if ($zone) {
                    $zone->update(['biteship_area_id' => $area['id']]);
                }
                return $area['id'];
            }
        }

        // Try city-only as last resort
        $cityAreas = $this->searchAreas($city);
        return $cityAreas[0]['id'] ?? null;
    }
}
