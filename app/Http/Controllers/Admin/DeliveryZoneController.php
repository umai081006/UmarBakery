<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeliveryZoneController extends Controller
{
    public function index(): View
    {
        $zones = DeliveryZone::orderBy('province')->orderBy('city')->orderBy('district')->get();
        return view('admin.delivery_zones.index', compact('zones'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'province'              => 'required|string|max:100',
            'city'                  => 'required|string|max:100',
            'district'              => 'nullable|string|max:100',
            'postal_code'           => 'nullable|string|max:10',
            'manual_shipping_cost'  => 'required|integer|min:0',
            'estimated_delivery'    => 'nullable|string|max:50',
            'notes'                 => 'nullable|string|max:255',
            'is_active'             => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        DeliveryZone::create($data);

        return redirect()->route('admin.delivery_zones.index')
            ->with('success', 'Zona pengiriman berhasil ditambahkan.');
    }

    public function update(Request $request, DeliveryZone $deliveryZone): RedirectResponse
    {
        $data = $request->validate([
            'province'              => 'required|string|max:100',
            'city'                  => 'required|string|max:100',
            'district'              => 'nullable|string|max:100',
            'postal_code'           => 'nullable|string|max:10',
            'manual_shipping_cost'  => 'required|integer|min:0',
            'estimated_delivery'    => 'nullable|string|max:50',
            'notes'                 => 'nullable|string|max:255',
            'is_active'             => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', false);

        $deliveryZone->update($data);

        return redirect()->route('admin.delivery_zones.index')
            ->with('success', 'Zona pengiriman berhasil diperbarui.');
    }

    public function destroy(DeliveryZone $deliveryZone): RedirectResponse
    {
        $deliveryZone->delete();
        return redirect()->route('admin.delivery_zones.index')
            ->with('success', 'Zona pengiriman berhasil dihapus.');
    }

    public function toggle(DeliveryZone $deliveryZone): RedirectResponse
    {
        $deliveryZone->update(['is_active' => !$deliveryZone->is_active]);
        $status = $deliveryZone->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.delivery_zones.index')
            ->with('success', "Zona pengiriman berhasil {$status}.");
    }
}
