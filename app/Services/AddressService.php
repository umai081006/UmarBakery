<?php

namespace App\Services;

use App\Models\Address;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class AddressService
{
    /**
     * Set a specific address as default for the user.
     */
    public function setDefaultAddress(User $user, Address $address): void
    {
        if ($address->user_id !== $user->id) {
            throw new Exception("Unauthorized to set this address as default.");
        }

        DB::transaction(function () use ($user, $address) {
            // Unset previous default
            $user->addresses()->update(['is_default' => false]);
            
            // Set new default
            $address->update(['is_default' => true]);
        });
    }

    /**
     * Create or update an address. Ensure first address becomes default.
     */
    public function saveAddress(User $user, array $data, ?Address $address = null): Address
    {
        return DB::transaction(function () use ($user, $data, $address) {
            $isFirstAddress = $user->addresses()->count() === 0;
            
            $data['is_default'] = $isFirstAddress ? true : ($data['is_default'] ?? false);

            if ($address) {
                if ($address->user_id !== $user->id) {
                    throw new Exception("Unauthorized to update this address.");
                }
                $address->update($data);
                $savedAddress = $address;
            } else {
                $savedAddress = $user->addresses()->create($data);
            }

            if ($savedAddress->is_default && !$isFirstAddress) {
                // If it was explicitly set to default, unset others
                $user->addresses()
                    ->where('id', '!=', $savedAddress->id)
                    ->update(['is_default' => false]);
            }

            return $savedAddress;
        });
    }
}
