@extends('layouts.admin')

@section('content')
<div class="space-y-8 max-w-2xl">
    <div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.categories.index') }}" class="text-stone-400 hover:text-stone-600 transition">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="text-3xl font-extrabold text-stone-900 tracking-tight font-sans">Edit Kategori</h1>
        </div>
        <p class="text-sm text-stone-500 mt-1">Ubah informasi kategori roti.</p>
    </div>

    <div class="bg-white rounded-3xl border border-stone-200 p-6 sm:p-8 shadow-sm">
        <form method="POST" action="{{ route('admin.categories.update', $category->id) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">Nama Kategori</label>
                <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required class="w-full px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm">
                @error('name')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-3">
                <label for="image" class="block text-xs font-semibold text-stone-500 uppercase tracking-wider">Gambar Kategori</label>
                
                @if($category->image)
                    <div class="w-24 h-24 rounded-xl overflow-hidden border border-stone-200 bg-stone-50">
                        <img src="{{ $category->image }}" alt="{{ $category->name }}" class="w-full h-full object-cover">
                    </div>
                @endif

                <input type="file" name="image" id="image" class="w-full text-sm text-stone-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-800 hover:file:bg-amber-100">
                <p class="text-[10px] text-stone-400">Pilih gambar baru jika ingin mengganti gambar sebelumnya (Maks 2MB)</p>
                @error('image')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ $category->is_active ? 'checked' : '' }} class="h-4 w-4 rounded border-stone-300 text-amber-600 focus:ring-amber-500">
                <label for="is_active" class="text-sm font-semibold text-stone-700">Aktifkan Kategori ini</label>
            </div>

            <button type="submit" class="w-full bg-amber-700 hover:bg-amber-800 text-white font-bold py-3.5 rounded-xl shadow transition text-sm">
                Perbarui Kategori
            </button>
        </form>
    </div>
</div>
@endsection
