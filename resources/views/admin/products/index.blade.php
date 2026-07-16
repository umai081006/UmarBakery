@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-stone-900 tracking-tight">Daftar Roti / Produk</h1>
            <p class="text-sm text-stone-500 mt-1">Kelola data roti, harga, stok, dan deskripsinya.</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-2 bg-amber-700 hover:bg-amber-800 text-white font-bold py-2.5 px-5 rounded-xl shadow transition text-sm">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Tambah Roti Baru
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden">
        @if($products->isEmpty())
            <div class="p-12 text-center text-sm text-stone-500">
                Belum ada produk roti yang ditambahkan.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-stone-50 text-[10px] uppercase font-bold text-stone-500 tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Gambar</th>
                            <th class="px-6 py-4">Roti / SKU</th>
                            <th class="px-6 py-4">Kategori</th>
                            <th class="px-6 py-4">Harga</th>
                            <th class="px-6 py-4">Stok</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach($products as $product)
                            <tr class="hover:bg-stone-50/80 transition">
                                <td class="px-6 py-4">
                                    <div class="w-10 h-10 bg-amber-50 rounded-lg overflow-hidden border border-stone-200 flex items-center justify-center shrink-0">
                                        @if($product->image_url)
                                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-amber-700 text-[10px] font-bold uppercase">UB</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-stone-900 leading-tight text-sm">{{ $product->name }}</div>
                                    <div class="text-[10px] text-stone-400 font-bold uppercase mt-0.5">{{ $product->sku }}</div>
                                </td>
                                <td class="px-6 py-4 text-stone-600 font-semibold text-xs">{{ $product->category->name }}</td>
                                <td class="px-6 py-4 font-bold text-amber-900">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">
                                    @if($product->stock === 0)
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-rose-800 bg-rose-50 px-2 py-0.5 rounded-full">
                                            Habis
                                        </span>
                                    @elseif($product->stock <= 5)
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-800 bg-amber-50 px-2 py-0.5 rounded-full">
                                            Kritis ({{ $product->stock }})
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-stone-700 bg-stone-100 px-2 py-0.5 rounded-full">
                                            {{ $product->stock }} Pcs
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($product->is_active)
                                        <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 border border-emerald-250 text-emerald-800 uppercase tracking-wider">Tampil</span>
                                    @else
                                        <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-stone-100 border border-stone-250 text-stone-600 uppercase tracking-wider">Arsip</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('admin.products.edit', $product->id) }}" class="text-xs font-semibold text-amber-700 hover:text-amber-900 bg-amber-50 px-2.5 py-1.5 rounded-lg border border-amber-100 transition">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.products.destroy', $product->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus roti ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800 bg-rose-50 px-2.5 py-1.5 rounded-lg border border-rose-100 transition">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-6 border-t border-stone-100">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
