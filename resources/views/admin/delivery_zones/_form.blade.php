<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-xs font-semibold text-stone-600 mb-1">Provinsi <span class="text-rose-500">*</span></label>
        <input type="text" name="province" required
            class="w-full border-stone-300 rounded-xl shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200 transition text-sm">
    </div>
    <div>
        <label class="block text-xs font-semibold text-stone-600 mb-1">Kota/Kabupaten <span class="text-rose-500">*</span></label>
        <input type="text" name="city" required
            class="w-full border-stone-300 rounded-xl shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200 transition text-sm">
    </div>
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-xs font-semibold text-stone-600 mb-1">Kecamatan (Opsional)</label>
        <input type="text" name="district"
            class="w-full border-stone-300 rounded-xl shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200 transition text-sm">
    </div>
    <div>
        <label class="block text-xs font-semibold text-stone-600 mb-1">Kode Pos (Opsional)</label>
        <input type="text" name="postal_code"
            class="w-full border-stone-300 rounded-xl shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200 transition text-sm">
    </div>
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-xs font-semibold text-stone-600 mb-1">Ongkir Manual (Rp) <span class="text-rose-500">*</span></label>
        <input type="number" name="manual_shipping_cost" required min="0" value="0"
            class="w-full border-stone-300 rounded-xl shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200 transition text-sm">
    </div>
    <div>
        <label class="block text-xs font-semibold text-stone-600 mb-1">Estimasi Waktu</label>
        <input type="text" name="estimated_delivery" placeholder="Misal: 1-2 hari"
            class="w-full border-stone-300 rounded-xl shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200 transition text-sm">
    </div>
</div>

<div>
    <label class="block text-xs font-semibold text-stone-600 mb-1">Catatan Internal</label>
    <input type="text" name="notes" placeholder="Hanya untuk dilihat Admin"
        class="w-full border-stone-300 rounded-xl shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200 transition text-sm">
</div>

<div class="flex items-center gap-2 mt-2">
    <input type="checkbox" name="is_active" id="is_active_{{ $editing ?? false ? 'edit' : 'add' }}" value="1" checked
        class="rounded text-amber-600 focus:ring-amber-500 border-stone-300">
    <label for="is_active_{{ $editing ?? false ? 'edit' : 'add' }}" class="text-sm font-medium text-stone-700">Aktifkan zona ini</label>
</div>
