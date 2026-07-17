@extends('layouts.app')

@php
    $heroBg = \App\Models\Setting::getVal('hero_bg', 'https://images.unsplash.com/photo-1509440159596-0249088772ff?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
    $aboutImg = \App\Models\Setting::getVal('about_img', 'https://images.unsplash.com/photo-1517433622965-0e62058e235e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80');
    $processImg = \App\Models\Setting::getVal('process_img', 'https://images.unsplash.com/photo-1486427944781-dbf259a2b4a7?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80');
@endphp

@section('content')
<!-- Mobile-First Hero Section -->
<div class="relative min-h-[75vh] md:min-h-[85vh] flex items-center justify-center overflow-hidden -mt-20 pt-24 pb-12">
    <!-- Background Image with Overlay -->
    <div class="absolute inset-0 z-0">
        <!-- Using a dark, moody bakery image focusing on fresh bread / butter melting -->
        <div class="absolute inset-0 bg-gradient-to-t from-softblack/90 via-softblack/50 to-softblack/30 z-10"></div>
        <img src="{{ $heroBg }}" alt="Fresh Bakery" class="w-full h-full object-cover">
    </div>

    <!-- Mobile Hero Content -->
    <div class="relative z-20 w-full px-6 pt-10 md:hidden flex flex-col h-full">
        <div class="mt-auto mb-10">
            <span class="text-butter text-xs font-mono tracking-widest uppercase mb-3 block">Umar Bakery</span>
            <h1 class="text-[36px] font-serif text-white leading-[1.1] drop-shadow-md mb-4">Fresh from the Oven, Siap Jadi Mood Booster Hari Ini.</h1>
            <p class="text-white/80 font-sans text-sm mb-6 leading-relaxed">
                Dibuat untuk nemenin sarapan, kerja, nongkrong, atau sekadar menikmati waktu sendiri.
            </p>
            
            <div class="flex flex-col gap-3">
                <a href="{{ route('products.index') }}" class="text-center bg-caramel hover:bg-cocoa text-white font-mono font-semibold py-3.5 rounded-full shadow-soft transition-all active:scale-95 text-sm">
                    Pesan Sekarang
                </a>
                <a href="#menu" class="text-center bg-white/10 backdrop-blur-md border border-white/20 hover:bg-white/20 text-white font-mono font-semibold py-3.5 rounded-full transition-all active:scale-95 text-sm">
                    Lihat Menu
                </a>
            </div>
        </div>
    </div>

    <!-- Desktop Hero Content (Hidden on Mobile) -->
    <div class="relative z-20 text-center px-4 max-w-4xl mx-auto hidden md:block">
        <span class="text-butter text-sm font-mono tracking-widest uppercase mb-4 block">Umar Bakery</span>
        <h1 class="text-5xl lg:text-7xl font-serif text-white leading-tight mb-6 drop-shadow-lg">Fresh from the Oven,<br><span class="italic text-dough">Siap Jadi Mood Booster Hari Ini.</span></h1>
        <p class="text-lg text-white/90 font-sans font-light mb-10 max-w-2xl mx-auto leading-relaxed">
            Dibuat untuk nemenin sarapan, kerja, nongkrong, atau sekadar menikmati waktu sendiri.
        </p>
        <div class="flex items-center justify-center gap-4">
            <a href="{{ route('products.index') }}" class="bg-caramel hover:bg-cocoa text-white font-mono font-semibold py-4 px-10 rounded-full shadow-soft transition-all text-base hover:scale-105">
                Pesan Sekarang
            </a>
            <a href="#menu" class="bg-white/10 backdrop-blur-md border border-white/20 hover:bg-white/20 text-white font-mono font-semibold py-4 px-10 rounded-full transition-all text-base hover:scale-105">
                Lihat Menu
            </a>
        </div>
    </div>
</div>

<!-- Category Strip (Kategori Produk) -->
<div class="bg-cream pt-6 pb-4 sticky top-0 md:top-20 z-30 shadow-sm" id="menu">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex overflow-x-auto hide-scrollbar gap-3 pb-2 snap-x">
            <a href="{{ route('products.index') }}" class="snap-start shrink-0 px-6 py-2.5 rounded-full text-sm font-mono font-semibold transition-colors bg-dough/50 text-cocoa hover:bg-caramel hover:text-white border border-transparent">
                Semua Menu
            </a>
            @foreach($categories as $category)
                <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="snap-start shrink-0 flex items-center gap-2 pl-2 pr-5 py-1.5 rounded-full text-sm font-mono font-semibold transition-colors bg-dough/50 text-cocoa hover:bg-caramel hover:text-white border border-transparent group">
                    @if($category->image)
                        <img src="{{ $category->image }}" alt="{{ $category->name }}" class="w-7 h-7 rounded-full object-cover border border-white/50 shadow-sm">
                    @else
                        <div class="w-7 h-7 rounded-full bg-white/50 flex items-center justify-center text-[10px] font-bold text-cocoa group-hover:text-caramel">UB</div>
                    @endif
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </div>
</div>

<!-- Produk Favorit Section -->
<div class="bg-lightgray py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex justify-between items-end mb-8 md:mb-12">
            <div>
                <h3 class="text-caramel font-mono text-xs md:text-sm font-bold uppercase tracking-widest mb-2">Terlaris Kami</h3>
                <h2 class="text-3xl md:text-4xl font-serif text-cocoa">Produk Favorit</h2>
            </div>
            <a href="{{ route('products.index') }}" class="hidden md:inline-flex font-mono text-sm font-semibold text-cocoa hover:text-caramel transition-colors items-center gap-2 border-b border-cocoa hover:border-caramel pb-1">
                Lihat Semua
            </a>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-x-4 gap-y-8 md:gap-8">
            @foreach($featuredProducts as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
        
        <div class="mt-10 text-center md:hidden">
            <a href="{{ route('products.index') }}" class="inline-block font-mono text-sm font-semibold text-caramel hover:text-cocoa transition-colors">
                Lihat Semua Produk →
            </a>
        </div>
    </div>
</div>

<!-- Menu Baru Section -->
<div class="bg-cocoa text-cream py-16 md:py-24 overflow-hidden relative">
    <!-- Decorative background element -->
    <div class="absolute top-0 right-0 w-64 h-64 bg-caramel rounded-full mix-blend-multiply filter blur-3xl opacity-30 translate-x-1/2 -translate-y-1/2"></div>
    
    <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10 text-center max-w-3xl">
        <span class="text-butter font-mono text-xs md:text-sm font-bold uppercase tracking-widest mb-4 block">Musiman & Terbatas</span>
        <h2 class="text-4xl md:text-5xl font-serif mb-6 text-white drop-shadow-md">Menu Baru</h2>
        <p class="text-cream/80 text-base md:text-lg leading-relaxed mb-8 font-sans font-light">
            Selalu ada ruang untuk eksplorasi. Setiap periode, Umar Bakery menghadirkan varian edisi terbatas yang terinspirasi dari tren, musim, dan masukan pelanggan.<br><br>Karena bosan itu wajar. Makanya menu juga ikut berkembang.
        </p>
        <a href="{{ route('products.index') }}" class="inline-block bg-dough hover:bg-white text-cocoa font-mono font-semibold py-3.5 px-8 rounded-full shadow-soft transition-all active:scale-95 text-sm">
            Cek Menu Terbaru
        </a>
    </div>
</div>

<!-- Tentang Umar Bakery (Story) Section -->
<div class="bg-cream py-16 md:py-24 border-t border-dough/30" id="story">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 md:gap-16 items-center">
            <div class="order-2 md:order-1">
                <h3 class="text-sm font-mono font-bold text-caramel uppercase tracking-widest mb-3">Cerita Kami</h3>
                <h2 class="text-4xl md:text-5xl font-serif text-cocoa mb-6 leading-tight">Tentang<br>Umar Bakery</h2>
                <p class="text-cocoa/80 text-base md:text-lg leading-relaxed mb-6 font-sans">
                    Kami percaya makanan yang baik tidak perlu berlebihan. Cukup dibuat dengan bahan yang tepat, proses yang benar, dan perhatian pada setiap detail.
                </p>
                <p class="text-cocoa/80 text-base md:text-lg leading-relaxed mb-8 font-sans">
                    Umar Bakery lahir dari semangat menghadirkan produk yang nyaman dinikmati siapa saja—mulai dari roti harian, pastry, pizza, hingga dessert—dengan kualitas yang konsisten dan rasa yang selalu ingin diulang.
                </p>
            </div>
            <div class="order-1 md:order-2">
                <div class="relative rounded-3xl overflow-hidden shadow-soft aspect-[4/3] md:aspect-square">
                    <img src="{{ $aboutImg }}" alt="Tentang Umar Bakery" class="w-full h-full object-cover">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Kenapa Memilih Umar Bakery? -->
<div class="bg-white py-16 md:py-24 border-t border-dough/30">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-serif text-cocoa">Kenapa Memilih Umar Bakery?</h2>
            <div class="w-20 h-1 bg-caramel mx-auto mt-6 rounded-full"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 md:gap-12 text-center">
            <!-- Poin 1 -->
            <div class="flex flex-col items-center">
                <div class="w-16 h-16 bg-cream rounded-full flex items-center justify-center text-caramel mb-6 shadow-sm border border-dough/50">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h4 class="font-serif text-xl text-cocoa mb-3">Dipanggang Berkala</h4>
                <p class="text-cocoa/70 text-sm font-sans leading-relaxed">Supaya yang sampai ke tangan pelanggan tetap hangat dan fresh.</p>
            </div>
            <!-- Poin 2 -->
            <div class="flex flex-col items-center">
                <div class="w-16 h-16 bg-cream rounded-full flex items-center justify-center text-caramel mb-6 shadow-sm border border-dough/50">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                </div>
                <h4 class="font-serif text-xl text-cocoa mb-3">Fokus pada Rasa</h4>
                <p class="text-cocoa/70 text-sm font-sans leading-relaxed">Visual penting, tapi rasa tetap nomor satu.</p>
            </div>
            <!-- Poin 3 -->
            <div class="flex flex-col items-center">
                <div class="w-16 h-16 bg-cream rounded-full flex items-center justify-center text-caramel mb-6 shadow-sm border border-dough/50">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
                <h4 class="font-serif text-xl text-cocoa mb-3">Selalu Berkembang</h4>
                <p class="text-cocoa/70 text-sm font-sans leading-relaxed">Menu baru terus hadir mengikuti selera tanpa kehilangan identitas.</p>
            </div>
            <!-- Poin 4 -->
            <div class="flex flex-col items-center">
                <div class="w-16 h-16 bg-cream rounded-full flex items-center justify-center text-caramel mb-6 shadow-sm border border-dough/50">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                </div>
                <h4 class="font-serif text-xl text-cocoa mb-3">Cocok untuk Semua</h4>
                <p class="text-cocoa/70 text-sm font-sans leading-relaxed">Sarapan, kerja, belajar, kumpul keluarga, atau sekadar self reward.</p>
            </div>
        </div>
    </div>
</div>

<!-- Behind the Bake -->
<div class="bg-lightgray py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 md:gap-16 items-center">
            <div>
                <div class="relative rounded-3xl overflow-hidden shadow-soft aspect-[4/3] md:aspect-video">
                    <img src="{{ $processImg }}" alt="Proses di Balik Roti" class="w-full h-full object-cover">
                </div>
            </div>
            <div>
                <h3 class="text-sm font-mono font-bold text-caramel uppercase tracking-widest mb-3">Keahlian Kami</h3>
                <h2 class="text-4xl md:text-5xl font-serif text-cocoa mb-6 leading-tight">Di Balik Proses</h2>
                <p class="text-cocoa/80 text-base md:text-lg leading-relaxed mb-6 font-sans">
                    Lihat bagaimana setiap produk dibuat. Mulai dari adonan pertama, proses proofing, keluar dari oven, sampai akhirnya siap dinikmati.
                </p>
                <p class="text-cocoa/80 text-base md:text-lg leading-relaxed font-sans font-medium italic">
                    Karena proses yang baik menghasilkan rasa yang baik.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Kata Mereka (Testimonials) -->
<div class="bg-cream py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-serif text-cocoa">Kata Mereka</h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
            <div class="bg-white p-8 rounded-3xl shadow-soft border border-dough/30 text-left">
                <div class="flex text-butter mb-4">★★★★★</div>
                <p class="text-cocoa/80 font-sans leading-relaxed mb-6 italic">"Pizza Rendangnya beda dari yang lain, rasanya familiar tapi tetap spesial."</p>
            </div>
            <div class="bg-white p-8 rounded-3xl shadow-soft border border-dough/30 text-left">
                <div class="flex text-butter mb-4">★★★★★</div>
                <p class="text-cocoa/80 font-sans leading-relaxed mb-6 italic">"Croissant-nya renyah di luar, lembut di dalam. Langsung jadi favorit."</p>
            </div>
            <div class="bg-white p-8 rounded-3xl shadow-soft border border-dough/30 text-left">
                <div class="flex text-butter mb-4">★★★★★</div>
                <p class="text-cocoa/80 font-sans leading-relaxed mb-6 italic">"Donut dan rotinya fresh, anak-anak di rumah langsung habis."</p>
            </div>
        </div>
    </div>
</div>

<!-- FAQ Section -->
<div class="bg-white py-16 md:py-24 border-t border-dough/30">
    <div class="max-w-3xl mx-auto px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-serif text-cocoa">FAQ</h2>
        </div>
        
        <div class="space-y-6">
            <!-- Item 1 -->
            <div class="border-b border-dough/50 pb-6">
                <h4 class="font-serif text-lg text-cocoa font-bold mb-2">Apakah diproduksi setiap hari?</h4>
                <p class="font-sans text-cocoa/70 text-sm md:text-base leading-relaxed">Ya. Produksi dilakukan setiap hari agar kualitas tetap terjaga.</p>
            </div>
            <!-- Item 2 -->
            <div class="border-b border-dough/50 pb-6">
                <h4 class="font-serif text-lg text-cocoa font-bold mb-2">Bisa pesan untuk acara?</h4>
                <p class="font-sans text-cocoa/70 text-sm md:text-base leading-relaxed">Bisa. Tersedia layanan pre-order untuk berbagai kebutuhan.</p>
            </div>
            <!-- Item 3 -->
            <div class="border-b border-dough/50 pb-6">
                <h4 class="font-serif text-lg text-cocoa font-bold mb-2">Ada menu musiman?</h4>
                <p class="font-sans text-cocoa/70 text-sm md:text-base leading-relaxed">Ada. Beberapa menu hanya hadir dalam periode tertentu.</p>
            </div>
            <!-- Item 4 -->
            <div class="border-b border-dough/50 pb-6">
                <h4 class="font-serif text-lg text-cocoa font-bold mb-2">Bisa dijadikan hampers?</h4>
                <p class="font-sans text-cocoa/70 text-sm md:text-base leading-relaxed">Bisa, dengan pilihan kemasan yang menyesuaikan kebutuhan.</p>
            </div>
        </div>
    </div>
</div>

<!-- Lokasi & Jam Operasional -->
<div class="bg-cream py-16 md:py-24 border-t border-dough/50">
    <div class="max-w-4xl mx-auto px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-serif text-cocoa mb-8">Lokasi & Jam Operasional</h2>
        
        <div class="flex flex-col md:flex-row items-center justify-center gap-10 md:gap-20">
            <div class="flex flex-col items-center">
                <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-caramel mb-4 shadow-sm border border-dough/50">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <h4 class="font-mono font-bold text-cocoa mb-1">Purwantoro, Jawa Tengah</h4>
                <p class="text-cocoa/70 text-sm font-sans">Datang langsung atau pesan untuk dibawa pulang.</p>
            </div>
            
            <div class="flex flex-col items-center">
                <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-caramel mb-4 shadow-sm border border-dough/50">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h4 class="font-mono font-bold text-cocoa mb-1">Buka Setiap Hari</h4>
                <p class="text-cocoa/70 text-sm font-sans">07.00 – 21.00 WIB</p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Utility for hiding scrollbar but keeping functionality */
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection
