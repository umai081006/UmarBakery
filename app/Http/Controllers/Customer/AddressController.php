<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Services\AddressService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AddressController extends Controller
{
    protected $addressService;

    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    public function index(Request $request): View
    {
        $addresses = $request->user()->addresses()->orderByDesc('is_default')->latest()->get();
        return view('customer.addresses.index', compact('addresses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'label' => 'required|string|max:100',
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'is_default' => 'boolean',
        ]);

        try {
            $this->addressService->saveAddress($request->user(), $data);
            return redirect()->back()->with('success', 'Alamat berhasil ditambahkan.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, Address $address): RedirectResponse
    {
        $data = $request->validate([
            'label' => 'required|string|max:100',
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'is_default' => 'boolean',
        ]);

        try {
            $this->addressService->saveAddress($request->user(), $data, $address);
            return redirect()->back()->with('success', 'Alamat berhasil diperbarui.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, Address $address): RedirectResponse
    {
        if ($address->user_id !== $request->user()->id) {
            return redirect()->back()->with('error', 'Unauthorized.');
        }

        $address->delete();
        return redirect()->back()->with('success', 'Alamat berhasil dihapus.');
    }

    public function setDefault(Request $request, Address $address): RedirectResponse
    {
        try {
            $this->addressService->setDefaultAddress($request->user(), $address);
            return redirect()->back()->with('success', 'Alamat utama berhasil diubah.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
