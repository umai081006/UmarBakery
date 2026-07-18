@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-extrabold text-stone-900 tracking-tight">Zona Pengiriman</h1>
            <p class="text-sm text-stone-500 mt-1">Kelola area pengiriman dan harga ongkos kirim manual. Digunakan sebagai fallback jika BiteShip API tidak tersedia.</p>
        </div>
        <button onclick="document.getElementById('modal-add').classList.remove('hidden')"
            class="bg-amber-600 hover:bg-amber-700 text-white font-bold py-2.5 px-5 rounded-xl shadow-sm transition text-sm">
            + Tambah Zona
        </button>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-3 text-sm font-medium">
            ✓ {{ session('success') }}
        </div>
    @endif

    {{-- BiteShip Status --}}
    <div class="bg-white rounded-2xl border border-stone-200 p-5 flex items-start gap-4">
        @if(config('services.biteship.key'))
            <div class="p-2 bg-emerald-100 rounded-xl text-emerald-700">✓</div>
            <div>
                <p class="font-semibold text-stone-800 text-sm">BiteShip API Aktif</p>
                <p class="text-xs text-stone-500 mt-0.5">Sistem akan otomatis mengambil harga kurir (GoSend, JNE, dll) via BiteShip. Zona di bawah digunakan sebagai <em>fallback</em> jika BiteShip tidak mengembalikan tarif.</p>
            </div>
        @else
            <div class="p-2 bg-amber-100 rounded-xl text-amber-700">⚠</div>
            <div>
                <p class="font-semibold text-stone-800 text-sm">BiteShip API Belum Dikonfigurasi</p>
                <p class="text-xs text-stone-500 mt-0.5">Tambahkan <code class="bg-stone-100 px-1 rounded">BITESHIP_API_KEY</code> dan <code class="bg-stone-100 px-1 rounded">BITESHIP_ORIGIN_AREA_ID</code> ke Railway Variables. Saat ini sistem hanya menggunakan harga manual di bawah.</p>
            </div>
        @endif
    </div>

    {{-- Zone Table --}}
    <div class="bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden">
        @if($zones->isEmpty())
            <div class="p-10 text-center text-stone-500 text-sm">
                Belum ada zona pengiriman. Tambahkan zona pertama untuk memulai.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-stone-50 text-[10px] uppercase font-bold text-stone-500 tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Provinsi</th>
                            <th class="px-6 py-4">Kota</th>
                            <th class="px-6 py-4">Kecamatan</th>
                            <th class="px-6 py-4">Kode Pos</th>
                            <th class="px-6 py-4">Ongkir Manual</th>
                            <th class="px-6 py-4">Estimasi</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach($zones as $zone)
                        <tr class="hover:bg-stone-50/80 transition">
                            <td class="px-6 py-4 font-medium text-stone-900">{{ $zone->province }}</td>
                            <td class="px-6 py-4 text-stone-600">{{ $zone->city }}</td>
                            <td class="px-6 py-4 text-stone-500">{{ $zone->district ?? '-' }}</td>
                            <td class="px-6 py-4 text-stone-500">{{ $zone->postal_code ?? '-' }}</td>
                            <td class="px-6 py-4 font-bold text-amber-700">
                                {{ $zone->manual_shipping_cost > 0 ? 'Rp ' . number_format($zone->manual_shipping_cost, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-stone-500">{{ $zone->estimated_delivery ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <form action="{{ route('admin.delivery_zones.toggle', $zone) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border transition
                                        {{ $zone->is_active ? 'bg-emerald-50 border-emerald-200 text-emerald-700 hover:bg-emerald-100' : 'bg-stone-100 border-stone-200 text-stone-500 hover:bg-stone-200' }}">
                                        {{ $zone->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4 text-right flex justify-end gap-2">
                                <button onclick="openEditModal({{ $zone->toJson() }})"
                                    class="text-xs font-semibold text-amber-700 hover:text-amber-900 bg-amber-50 px-3 py-1.5 rounded-lg border border-amber-100 transition">
                                    Edit
                                </button>
                                <form action="{{ route('admin.delivery_zones.destroy', $zone) }}" method="POST"
                                    onsubmit="return confirm('Hapus zona ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800 bg-rose-50 px-3 py-1.5 rounded-lg border border-rose-100 transition">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- Add Modal --}}
<div id="modal-add" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg p-8">
        <h3 class="text-xl font-bold text-stone-900 mb-6">Tambah Zona Pengiriman</h3>
        <form action="{{ route('admin.delivery_zones.store') }}" method="POST" class="space-y-4">
            @csrf
            @include('admin.delivery_zones._form')
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-add').classList.add('hidden')"
                    class="px-5 py-2.5 rounded-xl border border-stone-200 text-stone-600 text-sm font-medium hover:bg-stone-50 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div id="modal-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg p-8">
        <h3 class="text-xl font-bold text-stone-900 mb-6">Edit Zona Pengiriman</h3>
        <form id="edit-form" method="POST" class="space-y-4">
            @csrf @method('PUT')
            @include('admin.delivery_zones._form', ['editing' => true])
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')"
                    class="px-5 py-2.5 rounded-xl border border-stone-200 text-stone-600 text-sm font-medium hover:bg-stone-50 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold transition">
                    Perbarui
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(zone) {
    const form = document.getElementById('edit-form');
    form.action = `/admin/delivery-zones/${zone.id}`;
    form.querySelector('[name="province"]').value = zone.province;
    form.querySelector('[name="city"]').value = zone.city;
    form.querySelector('[name="district"]').value = zone.district ?? '';
    form.querySelector('[name="postal_code"]').value = zone.postal_code ?? '';
    form.querySelector('[name="manual_shipping_cost"]').value = zone.manual_shipping_cost;
    form.querySelector('[name="estimated_delivery"]').value = zone.estimated_delivery ?? '';
    form.querySelector('[name="notes"]').value = zone.notes ?? '';
    form.querySelector('[name="is_active"]').checked = zone.is_active;
    document.getElementById('modal-edit').classList.remove('hidden');
}
</script>
@endsection
