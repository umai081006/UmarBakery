<?php

namespace App\Services;

use App\Models\Address;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AddressService
{
    protected ShippingService $shippingService;

    public function __construct(ShippingService $shippingService)
    {
        $this->shippingService = $shippingService;
    }

    /**
     * Set a specific address as default for the user.
     */
    public function setDefaultAddress(User $user, Address $address): void
    {
        if ($address->user_id !== $user->id) {
            throw new Exception("Unauthorized to set this address as default.");
        }

        DB::transaction(function () use ($user, $address) {
            $user->addresses()->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        });
    }

    /**
     * Create or update an address.
     * After saving, automatically attempts to resolve & persist Biteship area ID.
     */
    public function saveAddress(User $user, array $data, ?Address $address = null): Address
    {
        $savedAddress = DB::transaction(function () use ($user, $data, $address) {
            $isFirstAddress = $user->addresses()->count() === 0;

            // Clear biteship_area_id if province/city/district changed (needs re-resolution)
            if ($address) {
                $locationChanged =
                    ($data['province'] ?? '') !== ($address->province ?? '') ||
                    ($data['city']     ?? '') !== ($address->city     ?? '') ||
                    ($data['district'] ?? '') !== ($address->district ?? '');

                if ($locationChanged) {
                    $data['biteship_area_id'] = null;
                }
            }

            $data['is_default'] = $isFirstAddress ? true : ($data['is_default'] ?? false);

            if ($address) {
                if ($address->user_id !== $user->id) {
                    throw new Exception("Unauthorized to update this address.");
                }
                $address->update($data);
                $savedAddress = $address->fresh();
            } else {
                $savedAddress = $user->addresses()->create($data);
            }

            if ($savedAddress->is_default && !$isFirstAddress) {
                $user->addresses()
                    ->where('id', '!=', $savedAddress->id)
                    ->update(['is_default' => false]);
            }

            return $savedAddress;
        });

        // After saving, resolve Biteship area ID asynchronously (if not already set)
        if (empty($savedAddress->biteship_area_id) && $savedAddress->province && $savedAddress->city) {
            try {
                $this->shippingService->resolveAndSaveAreaId($savedAddress);
            } catch (\Throwable $e) {
                // Non-fatal — address is saved, area ID just isn't resolved yet
                Log::warning('[address] Biteship area ID resolution failed after save', [
                    'address_id' => $savedAddress->id,
                    'error'      => $e->getMessage(),
                ]);
            }
        }

        return $savedAddress->fresh();
    }
}
