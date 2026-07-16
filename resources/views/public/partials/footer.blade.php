<footer class="bg-cocoa text-cream/60 mt-auto pb-20 md:pb-0">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
            <!-- Brand -->
            <div class="md:col-span-2">
                <span class="font-serif text-3xl font-bold text-cream block mb-2">UMAR BAKERY</span>
                <span class="font-serif italic text-caramel text-lg block mb-6">Hangat di Oven, Dekat di Hati.</span>
                <p class="text-sm font-sans leading-relaxed text-cream/70 max-w-sm mb-6">
                    Fresh bread • Pastry • Pizza • Donut • Dessert
                </p>
                <p class="text-sm font-sans leading-relaxed text-cream/50 max-w-sm italic">
                    Dibuat setiap hari untuk menemani momen kecil yang layak dirayakan.
                </p>
            </div>

            <!-- Links -->
            <div>
                <h3 class="font-mono text-xs font-semibold text-cream tracking-widest uppercase mb-6">Navigation</h3>
                <ul class="space-y-4">
                    <li><a href="{{ route('home') }}" class="text-sm font-sans text-cream/60 hover:text-butter transition-colors">Home</a></li>
                    <li><a href="{{ route('products.index') }}" class="text-sm font-sans text-cream/60 hover:text-butter transition-colors">Semua Menu</a></li>
                    <li><a href="{{ route('home') }}#story" class="text-sm font-sans text-cream/60 hover:text-butter transition-colors">Tentang Kami</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h3 class="font-mono text-xs font-semibold text-cream tracking-widest uppercase mb-6">Hubungi Kami</h3>
                <ul class="space-y-4 text-sm font-sans text-cream/60">
                    <li class="flex items-start gap-3">
                        <svg class="h-5 w-5 text-caramel shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Purwantoro, Jawa Tengah</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="h-5 w-5 text-caramel shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span>+62 895-2512-1811</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="h-5 w-5 text-caramel shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Buka Setiap Hari<br>07.00 – 21.00 WIB</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="mt-16 pt-8 border-t border-cream/10 flex flex-col md:flex-row justify-between items-center gap-4 text-center md:text-left">
            <p class="font-mono text-xs text-cream/30">&copy; {{ date('Y') }} Umar Bakery. All rights reserved.</p>
        </div>
    </div>
</footer>
