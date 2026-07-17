@php
    $isHome = request()->routeIs('home');
    $isHomeStr = $isHome ? 'true' : 'false';
@endphp

<nav x-data="{ scrolled: false }" 
     @scroll.window="scrolled = (window.pageYOffset > 20)" 
     :class="{ 'bg-white/90 backdrop-blur-md shadow-sm': scrolled || !{{ $isHomeStr }}, 'bg-transparent': !scrolled && {{ $isHomeStr }} }" 
     class="fixed top-0 w-full z-40 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <!-- Mobile Menu Button -->
            <button @click="sidebarOpen = !sidebarOpen" :class="{ 'text-cocoa': scrolled || !{{ $isHomeStr }}, 'text-white': !scrolled && {{ $isHomeStr }} }" class="md:hidden focus:outline-none p-2 -ml-2 rounded-full transition-colors">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center md:flex-none flex-1 justify-center md:justify-start">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <img src="/docs/logo.jpeg" alt="Umar Bakery" class="h-10 w-10 rounded-full object-cover shadow-sm border border-dough/30">
                    <span :class="{ 'text-cocoa': scrolled || !{{ $isHomeStr }}, 'text-white': !scrolled && {{ $isHomeStr }} }" class="font-serif text-xl font-bold tracking-tight hidden sm:inline">
                        Umar <span class="text-caramel italic">Bakery</span>
                    </span>
                </a>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ route('home') }}" :class="{ 'text-cocoa hover:text-caramel': scrolled || !{{ $isHomeStr }}, 'text-white hover:text-white/80': !scrolled && {{ $isHomeStr }} }" class="font-mono text-sm font-medium transition-colors">Beranda</a>
                <a href="{{ route('home') }}#menu" :class="{ 'text-cocoa hover:text-caramel': scrolled || !{{ $isHomeStr }}, 'text-white hover:text-white/80': !scrolled && {{ $isHomeStr }} }" class="font-mono text-sm font-medium transition-colors">Menu</a>
                <a href="{{ route('home') }}#story" :class="{ 'text-cocoa hover:text-caramel': scrolled || !{{ $isHomeStr }}, 'text-white hover:text-white/80': !scrolled && {{ $isHomeStr }} }" class="font-mono text-sm font-medium transition-colors">Cerita Kami</a>
            </div>

            <!-- Right Side (Search, Cart & Profile) -->
            <div class="flex items-center space-x-1 md:space-x-3 relative" x-data="{ searchOpen: false }">
                
                <!-- Search Form Dropdown -->
                <div x-show="searchOpen" x-transition.opacity @click.away="searchOpen = false" class="absolute right-0 top-full mt-2 w-64 bg-white rounded-2xl shadow-xl border border-dough/30 p-2 z-50">
                    <form action="{{ route('products.index') }}" method="GET" class="flex items-center relative">
                        <input type="text" name="search" placeholder="Cari roti, pastry..." class="w-full bg-cream rounded-xl border-none focus:ring-0 text-sm text-cocoa py-2 pl-4 pr-10 placeholder-cocoa/50" x-ref="searchInput">
                        <button type="submit" class="absolute right-2 p-1 text-cocoa/50 hover:text-caramel transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </button>
                    </form>
                </div>

                <button @click="searchOpen = !searchOpen; if(searchOpen) $nextTick(() => $refs.searchInput.focus())" :class="{ 'text-cocoa': scrolled || !{{ $isHomeStr }}, 'text-white': !scrolled && {{ $isHomeStr }} }" class="p-2 rounded-full hover:bg-dough/20 transition-colors hidden md:block">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
                @if(!auth()->check() || auth()->user()->isCustomer())
                    <a href="{{ route('cart.index') }}" :class="{ 'text-cocoa': scrolled || !{{ $isHomeStr }}, 'text-white': !scrolled && {{ $isHomeStr }} }" class="p-2 rounded-full hover:bg-dough/20 transition-colors relative">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        @auth
                            @php $cartCount = \App\Models\Cart::where('user_id', auth()->id())->sum('quantity'); @endphp
                            @if($cartCount > 0)
                                <span class="absolute top-0 right-0 w-4 h-4 bg-caramel text-white text-[9px] font-bold flex items-center justify-center rounded-full border border-white">{{ $cartCount }}</span>
                            @endif
                        @endauth
                    </a>
                @endif
                <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" :class="{ 'text-cocoa': scrolled || !{{ $isHomeStr }}, 'text-white': !scrolled && {{ $isHomeStr }} }" class="p-2 rounded-full hover:bg-dough/20 transition-colors hidden md:block">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Mobile Sidebar (controlled by body x-data) -->
<div x-cloak x-show="sidebarOpen" class="fixed inset-0 z-50 overflow-hidden md:hidden">
    <div x-show="sidebarOpen" x-transition.opacity class="absolute inset-0 bg-cocoa/40 backdrop-blur-sm" @click="sidebarOpen = false"></div>
    <div x-show="sidebarOpen" 
         x-transition:enter="transform transition ease-in-out duration-300"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transform transition ease-in-out duration-300"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="fixed inset-y-0 left-0 max-w-xs w-full bg-cream shadow-float flex flex-col py-6 px-6">
        
        <div class="flex items-center justify-between mb-8">
            <span class="font-serif text-xl font-bold text-cocoa">Umar Bakery</span>
            <button @click="sidebarOpen = false" class="text-cocoa/50 hover:text-cocoa p-2">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        
        <div class="flex flex-col space-y-6">
            <a href="{{ route('home') }}" class="font-mono text-lg text-cocoa hover:text-caramel" @click="sidebarOpen = false">Beranda</a>
            <a href="{{ route('home') }}#menu" class="font-mono text-lg text-cocoa hover:text-caramel" @click="sidebarOpen = false">Menu</a>
            <a href="{{ route('home') }}#story" class="font-mono text-lg text-cocoa hover:text-caramel" @click="sidebarOpen = false">Cerita Kami</a>
            <hr class="border-dough/50">
            @auth
                <a href="{{ route('dashboard') }}" class="font-mono text-lg text-cocoa hover:text-caramel">Akun Saya</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="font-mono text-lg text-strawberry hover:text-strawberry/80">Keluar</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="font-mono text-lg text-cocoa hover:text-caramel">Masuk</a>
                <a href="{{ route('register') }}" class="font-mono text-lg text-cocoa hover:text-caramel">Daftar</a>
            @endauth
        </div>
    </div>
</div>
