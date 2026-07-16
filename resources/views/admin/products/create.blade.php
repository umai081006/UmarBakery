@extends('layouts.admin')

@section('content')
<div class="space-y-8 max-w-4xl">
    <div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.products.index') }}" class="text-stone-400 hover:text-stone-600 transition">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="text-3xl font-extrabold text-stone-900 tracking-tight font-sans">Tambah Roti Baru</h1>
        </div>
        <p class="text-sm text-stone-500 mt-1">Lengkapi informasi roti premium di bawah ini.</p>
    </div>

    <div class="bg-white rounded-3xl border border-stone-200 p-6 sm:p-8 shadow-sm">
        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">Nama Roti</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: Roti Sobek Cokelat" class="w-full px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm">
                    @error('name')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- SKU -->
                <div>
                    <label for="sku" class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">SKU (Kode Produk)</label>
                    <input type="text" name="sku" id="sku" value="{{ old('sku') }}" required placeholder="Contoh: ROTI-SB-COK" class="w-full px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm">
                    @error('sku')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">Kategori</label>
                    <select name="category_id" id="category_id" required class="w-full px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Weight -->
                <div>
                    <label for="weight" class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">Berat Roti (Gram)</label>
                    <input type="number" name="weight" id="weight" value="{{ old('weight') }}" required placeholder="Contoh: 250" class="w-full px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm">
                    @error('weight')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Price -->
                <div>
                    <label for="price" class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">Harga (Rupiah)</label>
                    <input type="number" name="price" id="price" value="{{ old('price') }}" required placeholder="Contoh: 15000" class="w-full px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm">
                    @error('price')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Stock -->
                <div>
                    <label for="stock" class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">Stok Awal</label>
                    <input type="number" name="stock" id="stock" value="{{ old('stock') }}" required placeholder="Contoh: 50" class="w-full px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm">
                    @error('stock')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">Deskripsi Roti</label>
                <textarea name="description" id="description" rows="4" required placeholder="Tuliskan keunggulan, rasa, dan deskripsi produk..." class="w-full px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Composition -->
            <div>
                <label for="composition" class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">Komposisi / Bahan-bahan (Opsional)</label>
                <textarea name="composition" id="composition" rows="2" placeholder="Tepung gandum, mentega, susu segar, ragi, cokelat Belgia..." class="w-full px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm">{{ old('composition') }}</textarea>
                @error('composition')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Image -->
            <div>
                <label for="image" class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">Gambar Roti (Opsional)</label>
                <input type="file" name="image" id="image" class="w-full text-sm text-stone-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-800 hover:file:bg-amber-100">
                <p class="text-[10px] text-stone-400 mt-1">Rekomendasi rasio gambar lanskap atau persegi, Maksimal 2MB</p>
                @error('image')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status Checkbox -->
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" checked class="h-4 w-4 rounded border-stone-300 text-amber-600 focus:ring-amber-500">
                <label for="is_active" class="text-sm font-semibold text-stone-700">Tampilkan produk ini di katalog langsung</label>
            </div>

            <button type="submit" class="w-full bg-amber-700 hover:bg-amber-800 text-white font-bold py-4 rounded-xl shadow transition text-sm">
                Simpan Roti / Produk
            </button>
        </form>
    </div>
</div>
@endsection
