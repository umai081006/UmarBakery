<?php

namespace App\Services;

use App\Models\Address;
use App\Models\DeliveryZone;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Hybrid Shipping Service
 *
 * Priority:
 *  1. Biteship live rates (requires valid destination_area_id from Maps API)
 *  2. Admin DeliveryZone manual rate
 *  3. Returns empty array → checkout cannot proceed
 *
 * SECURITY: shipping costs are ALWAYS computed server-side.
 * NEVER trust shipping_cost from the frontend form.
 */
class ShippingService
{
    protected ?string $apiKey;
    protected ?string $originAreaId;
    protected bool $apiEnabled;

    private const BITESHIP_BASE = 'https://api.biteship.com/v1';
    private const AREA_CACHE_HOURS = 24 * 7; // 7 days

    public function __construct()
    {
        $this->apiKey       = config('services.biteship.api_key') ?: null;
        $this->originAreaId = config('services.biteship.origin_area_id') ?: null;
        $this->apiEnabled   = !empty($this->apiKey) && !empty($this->originAreaId);
    }

    // =========================================================================
    // PUBLIC API
    // =========================================================================

    /**
     * Primary entry point: get shipping rates for a destination.
     *
     * @param  string      $province
     * @param  string      $city
     * @param  string      $district
     * @param  int         $totalWeightGrams
     * @param  int         $orderValueIdr
     * @param  string|null $biteshipAreaId    Pre-resolved area ID from addresses table
     * @return array
     */
    public function getRates(
        string $province,
        string $city,
        string $district,
        int $totalWeightGrams,
        int $orderValueIdr,
        ?string $biteshipAreaId = null
    ): array {
        $rates = [];

        // STEP 1: Biteship live rates (requires valid area ID)
        if ($this->apiEnabled) {
            $destinationAreaId = $biteshipAreaId ?? $this->resolveAreaId($province, $city, $district);

            if ($destinationAreaId) {
                try {
                    $rates = $this->fetchBiteshipRates($destinationAreaId, $totalWeightGrams, $orderValueIdr);
                } catch (\Throwable $e) {
                    Log::error('[shipping] Biteship rates exception', [
                        'error'   => $e->getMessage(),
                        'area_id' => $destinationAreaId,
                    ]);
                    $rates = [];
                }
            } else {
                Log::info('[shipping] Biteship: could not resolve area ID, trying fallback', [
                    'province' => $province,
                    'city'     => $city,
                    'district' => $district,
                ]);
            }
        }

        // STEP 2: Fallback to admin DeliveryZone
        if (empty($rates)) {
            $rates = $this->getZoneRates($province, $city, $district);
        }

        if (empty($rates)) {
            Log::info('[shipping] No rates available for destination', [
                'province' => $province,
                'city'     => $city,
                'district' => $district,
            ]);
        }

        return $rates;
    }

    /**
     * Get rates directly by Biteship area ID (used when area ID is already stored).
     */
    public function getRatesByAreaId(string $destinationAreaId, int $totalWeightGrams, int $orderValueIdr): array
    {
        if (!$this->apiEnabled) {
            return [];
        }
        return $this->fetchBiteshipRates($destinationAreaId, $totalWeightGrams, $orderValueIdr);
    }

    /**
     * Resolve a Biteship area ID from province/city/district text.
     * Tries multiple query formats for best match.
     * Caches results to reduce API calls.
     *
     * @return string|null
     */
    public function resolveAreaId(string $province, string $city, string $district): ?string
    {
        if (!$this->apiEnabled) {
            return null;
        }

        // Try district-level first (most accurate)
        if ($district) {
            $areaId = $this->searchSingleAreaId("{$district}, {$city}, {$province}");
            if ($areaId) return $areaId;

            // Try without province
            $areaId = $this->searchSingleAreaId("{$district}, {$city}");
            if ($areaId) return $areaId;
        }

        // Try city-level
        $areaId = $this->searchSingleAreaId("{$city}, {$province}");
        if ($areaId) return $areaId;

        return null;
    }

    /**
     * Resolve area ID and optionally persist it to an Address model.
     * Safe to call at address save time or lazily at checkout.
     */
    public function resolveAndSaveAreaId(Address $address): ?string
    {
        if (!$this->apiEnabled) {
            return null;
        }

        // Already resolved
        if (!empty($address->biteship_area_id)) {
            return $address->biteship_area_id;
        }

        $areaId = $this->resolveAreaId(
            $address->province ?? '',
            $address->city ?? '',
            $address->district ?? ''
        );

        if ($areaId) {
            $address->biteship_area_id = $areaId;
            $address->saveQuietly(); // no events, just persist
            Log::info('[shipping] Biteship area ID resolved and saved', [
                'address_id' => $address->id,
                'area_id'    => $areaId,
            ]);
        } else {
            Log::warning('[shipping] Biteship area ID could not be resolved for address', [
                'address_id' => $address->id,
                'province'   => $address->province,
                'city'       => $address->city,
                'district'   => $address->district,
            ]);
        }

        return $areaId;
    }

    /**
     * Search Biteship Maps API and return first matching area ID.
     * Caches result per query string.
     */
    public function searchAreas(string $query): array
    {
        if (!$this->apiEnabled) {
            return [];
        }

        $cacheKey = 'biteship_area_search_' . md5($query);

        return Cache::remember($cacheKey, self::AREA_CACHE_HOURS * 3600, function () use ($query) {
            try {
                $response = Http::withToken($this->apiKey)
                    ->timeout(10)
                    ->get(self::BITESHIP_BASE . '/maps/areas', [
                        'countries' => 'ID',
                        'input'     => $query,
                        'type'      => 'single',
                    ]);

                if ($response->successful()) {
                    return $response->json('areas', []);
                }

                Log::warning('[shipping] Biteship Maps API non-success', [
                    'status'   => $response->status(),
                    'query'    => $query,
                    'response' => $response->json(),
                ]);
                return [];
            } catch (\Throwable $e) {
                Log::error('[shipping] Biteship Maps API exception', [
                    'query' => $query,
                    'error' => $e->getMessage(),
                ]);
                return [];
            }
        });
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Perform Maps API search and return first area ID, or null.
     */
    private function searchSingleAreaId(string $query): ?string
    {
        $areas = $this->searchAreas($query);
        if (!empty($areas[0]['id'])) {
            return $areas[0]['id'];
        }
        return null;
    }

    /**
     * Call Biteship Rates API and return normalized rate array.
     */
    private function fetchBiteshipRates(string $destinationAreaId, int $weightGrams, int $orderValue): array
    {
        $payload = [
            'origin_area_id'      => $this->originAreaId,
            'destination_area_id' => $destinationAreaId,
            'items'               => [
                [
                    'name'        => 'Umar Bakery Order',
                    'description' => 'Produk roti dan kue',
                    'value'       => max($orderValue, 1),
                    'length'      => 30,
                    'width'       => 20,
                    'height'      => 15,
                    'weight'      => max($weightGrams, 1),
                    'quantity'    => 1,
                ],
            ],
        ];

        $response = Http::withToken($this->apiKey)
            ->timeout(20)
            ->post(self::BITESHIP_BASE . '/rates/couriers', $payload);

        if (!$response->successful()) {
            // Log full detail (no API key exposed)
            Log::error('[shipping] Biteship Rates API failed', [
                'endpoint'             => self::BITESHIP_BASE . '/rates/couriers',
                'http_status'          => $response->status(),
                'origin_area_id'       => $this->originAreaId,
                'destination_area_id'  => $destinationAreaId,
                'weight_grams'         => $weightGrams,
                'response_body'        => $response->json(),
            ]);
            return [];
        }

        $pricings = $response->json('pricing', []);
        $rates    = [];

        foreach ($pricings as $pricing) {
            $price = (int) ($pricing['price'] ?? 0);
            if ($price <= 0) continue;

            $rates[] = [
                'type'               => 'biteship',
                'courier_name'       => $pricing['courier_name'] ?? ($pricing['courier_code'] ?? 'Kurir'),
                'courier_service'    => $pricing['courier_service_code'] ?? 'REG',
                'courier_service_name' => $pricing['courier_service_name'] ?? ($pricing['courier_service_code'] ?? 'Reguler'),
                'service_type'       => $pricing['type'] ?? 'reguler',
                'price'              => $price,
                'price_formatted'    => 'Rp ' . number_format($price, 0, ',', '.'),
                'estimated_delivery' => $pricing['shipment_duration_range'] ?? ($pricing['shipment_duration_unit'] ?? '1-3 hari'),
                'zone_id'            => null,
            ];
        }

        usort($rates, fn($a, $b) => $a['price'] <=> $b['price']);

        Log::info('[shipping] Biteship Rates API success', [
            'destination_area_id' => $destinationAreaId,
            'rate_count'          => count($rates),
        ]);

        return $rates;
    }

    /**
     * Get admin-defined manual shipping rate from DeliveryZone.
     */
    private function getZoneRates(string $province, string $city, string $district): array
    {
        $zone = DeliveryZone::findFor($province, $city, $district);

        if (!$zone || $zone->manual_shipping_cost <= 0) {
            return [];
        }

        return [
            [
                'type'               => 'manual',
                'courier_name'       => 'Pengiriman Lokal',
                'courier_service'    => 'REGULER',
                'courier_service_name' => 'Reguler',
                'service_type'       => 'reguler',
                'price'              => $zone->manual_shipping_cost,
                'price_formatted'    => 'Rp ' . number_format($zone->manual_shipping_cost, 0, ',', '.'),
                'estimated_delivery' => $zone->estimated_delivery ?? '1-3 hari',
                'zone_id'            => $zone->id,
            ],
        ];
    }
}
