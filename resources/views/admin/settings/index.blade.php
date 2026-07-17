@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight text-stone-900">Pengaturan Website</h1>
        <p class="text-stone-500 mt-2">Sesuaikan konten, gambar, dan elemen tampilan website di sini.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-3">
            <svg class="h-5 w-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span class="font-medium text-sm">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden">
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-8">
            @csrf

            <!-- Hero Section -->
            <div>
                <h3 class="text-lg font-bold text-stone-900 mb-4 pb-2 border-b border-stone-100">Hero Section (Beranda Utama)</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-1">Background Image (Hero)</label>
                        @if(isset($settings['hero_bg']->value))
                            <div class="mb-3">
                                <img src="{{ $settings['hero_bg']->value }}" alt="Hero Preview" class="h-32 w-auto object-cover rounded-xl border border-stone-200">
                            </div>
                        @endif
                        <input type="hidden" name="hero_bg" value="{{ $settings['hero_bg']->value ?? 'https://images.unsplash.com/photo-1509440159596-0249088772ff?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80' }}">
                        <input type="file" name="hero_bg_file" accept="image/*" class="w-full border-stone-300 rounded-xl shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200 transition">
                        <p class="text-xs text-stone-500 mt-1">Upload gambar background utama halaman beranda.</p>
                    </div>
                </div>
            </div>

            <!-- Tentang Kami Section -->
            <div>
                <h3 class="text-lg font-bold text-stone-900 mb-4 pb-2 border-b border-stone-100">Bagian 'Tentang Umar Bakery'</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-1">Image (Tentang Kami)</label>
                        @if(isset($settings['about_img']->value))
                            <div class="mb-3">
                                <img src="{{ $settings['about_img']->value }}" alt="About Preview" class="h-32 w-auto object-cover rounded-xl border border-stone-200">
                            </div>
                        @endif
                        <input type="hidden" name="about_img" value="{{ $settings['about_img']->value ?? 'https://images.unsplash.com/photo-1517433622965-0e62058e235e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80' }}">
                        <input type="file" name="about_img_file" accept="image/*" class="w-full border-stone-300 rounded-xl shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200 transition">
                        <p class="text-xs text-stone-500 mt-1">Upload gambar untuk bagian Tentang Umar Bakery.</p>
                    </div>
                </div>
            </div>

            <!-- Behind the Bake Section -->
            <div>
                <h3 class="text-lg font-bold text-stone-900 mb-4 pb-2 border-b border-stone-100">Bagian 'Behind the Bake'</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-1">Image (Behind the Bake)</label>
                        @if(isset($settings['process_img']->value))
                            <div class="mb-3">
                                <img src="{{ $settings['process_img']->value }}" alt="Process Preview" class="h-32 w-auto object-cover rounded-xl border border-stone-200">
                            </div>
                        @endif
                        <input type="hidden" name="process_img" value="{{ $settings['process_img']->value ?? 'https://images.unsplash.com/photo-1486427944781-dbf259a2b4a7?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80' }}">
                        <input type="file" name="process_img_file" accept="image/*" class="w-full border-stone-300 rounded-xl shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200 transition">
                        <p class="text-xs text-stone-500 mt-1">Upload gambar untuk bagian Behind the Bake.</p>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-stone-100 flex justify-end">
                <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-bold py-3 px-8 rounded-xl shadow-sm transition active:scale-95">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
