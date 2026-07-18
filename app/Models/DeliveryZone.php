<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'province',
        'city',
        'district',
        'postal_code',
        'biteship_area_id',
        'manual_shipping_cost',
        'is_active',
        'estimated_delivery',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'manual_shipping_cost' => 'integer',
    ];

    /**
     * Scope to get only active zones.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get all distinct provinces.
     */
    public static function provinces(): array
    {
        return static::active()
            ->distinct()
            ->orderBy('province')
            ->pluck('province')
            ->toArray();
    }

    /**
     * Get cities for a given province.
     */
    public static function citiesFor(string $province): array
    {
        return static::active()
            ->where('province', $province)
            ->distinct()
            ->orderBy('city')
            ->pluck('city')
            ->toArray();
    }

    /**
     * Get districts for a given province+city.
     */
    public static function districtsFor(string $province, string $city): array
    {
        return static::active()
            ->where('province', $province)
            ->where('city', $city)
            ->orderBy('district')
            ->get(['id', 'district', 'postal_code', 'manual_shipping_cost', 'estimated_delivery'])
            ->toArray();
    }

    /**
     * Find the best matching zone for a given address.
     */
    public static function findFor(string $province, string $city, string $district = null): ?static
    {
        $query = static::active()
            ->where('province', $province)
            ->where('city', $city);

        if ($district) {
            // Try district-level match first
            $zone = (clone $query)->where('district', $district)->first();
            if ($zone) return $zone;
        }

        // Fallback to city-level match (district is null in zone)
        return $query->whereNull('district')->first();
    }

    /**
     * Full location label.
     */
    public function getFullLabelAttribute(): string
    {
        $parts = array_filter([$this->district, $this->city, $this->province]);
        return implode(', ', $parts);
    }
}
