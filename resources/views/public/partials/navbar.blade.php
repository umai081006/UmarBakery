<nav class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-amber-100 shadow-sm" x-data="{ open: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <!-- Logo & Brand -->
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                    <span class="text-2xl font-extrabold tracking-tight bg-gradient-to-r from-amber-700 to-amber-900 bg-clip-text text-transparent group-hover:from-amber-600 group-hover:to-amber-800 transition">
                        UMAR BAKERY
                    </span>
                    <span class="hidden sm:inline-block px-2.5 py-0.5 text-[10px] font-bold tracking-wider uppercase bg-amber-100 text-amber-800 rounded-full">
                        Premium
                    </span>
                </a>
            </div>

            <!-- Desktop Nav Links -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ route('home') }}" class="text-sm font-medium {{ request()->routeIs('home') ? 'text-amber-700 font-semibold' : 'text-stone-600 hover:text-amber-700' }} transition">
                    Beranda
                </a>
                <a href="{{ route('products.index') }}" class="text-sm font-medium {{ request()->routeIs('products.*') ? 'text-amber-700 font-semibold' : 'text-stone-600 hover:text-amber-700' }} transition">
                    Produk
                </a>
            </div>

            <!-- Right-side Actions -->
            <div class="hidden md:flex items-center space-x-6">
                <!-- Cart Icon -->
                @auth
                    @if(auth()->user()->isCustomer())
                        <a href="{{ route('cart.index') }}" class="relative p-2 text-stone-600 hover:text-amber-700 transition">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                            </svg>
                            @php
                                $cartCount = auth()->user()->carts()->sum('quantity');
                            @endphp
                            @if($cartCount > 0)
                                <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-amber-600 rounded-full shadow-sm animate-pulse">
                                    {{ $cartCount }}
                                </span>
                            @endif
                        </a>
                    @endif
                @endauth

                <!-- Auth links -->
                @auth
                    <div class="relative" x-data="{ userMenuOpen: false }" @click.away="userMenuOpen = false">
                        <button @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-2 py-1 px-3 rounded-full hover:bg-stone-50 border border-transparent hover:border-stone-200 transition focus:outline-none">
                            <span class="text-sm font-semibold text-stone-700">{{ auth()->user()->name }}</span>
                            <svg class="h-4 w-4 text-stone-400 transition" :class="{'rotate-180': userMenuOpen}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        
                        <div x-show="userMenuOpen" x-transition class="absolute right-0 mt-2 w-48 bg-white border border-stone-200 rounded-xl shadow-lg py-1 z-50">
                            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'owner')
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-stone-700 hover:bg-amber-50 hover:text-amber-800">Dashboard Admin</a>
                            @else
                                <a href="{{ route('customer.dashboard') }}" class="block px-4 py-2 text-sm text-stone-700 hover:bg-amber-50 hover:text-amber-800">Dashboard Saya</a>
                                <a href="{{ route('customer.orders.index') }}" class="block px-4 py-2 text-sm text-stone-700 hover:bg-amber-50 hover:text-amber-800">Pesanan Saya</a>
                            @endif
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-stone-700 hover:bg-amber-50 hover:text-amber-800">Edit Profil</a>
                            <div class="border-t border-stone-100 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 font-medium">
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-stone-600 hover:text-amber-700 transition">Masuk</a>
                    <a href="{{ route('register') }}" class="text-sm font-semibold bg-amber-700 text-white px-5 py-2.5 rounded-full hover:bg-amber-800 shadow-sm hover:shadow transition">Daftar</a>
                @endauth
            </div>

            <!-- Mobile Menu Toggle -->
            <div class="flex items-center md:hidden gap-4">
                @auth
                    @if(auth()->user()->isCustomer())
                        <a href="{{ route('cart.index') }}" class="relative p-2 text-stone-600 hover:text-amber-700 transition">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                            </svg>
                            @php
                                $cartCount = auth()->user()->carts()->sum('quantity');
                            @endphp
                            @if($cartCount > 0)
                                <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-amber-600 rounded-full shadow-sm">
                                    {{ $cartCount }}
                                </span>
                            @endif
                        </a>
                    @endif
                @endauth
                
                <button @click="open = !open" class="text-stone-500 hover:text-amber-700 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path :class="{'hidden': open, 'inline-flex': !open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="open" x-transition class="md:hidden border-b border-amber-100 bg-white">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-stone-700 hover:bg-amber-50 hover:text-amber-800">Beranda</a>
            <a href="{{ route('products.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-stone-700 hover:bg-amber-50 hover:text-amber-800">Produk</a>
        </div>
        <div class="pt-4 pb-3 border-t border-stone-100">
            @auth
                <div class="px-5 mb-3">
                    <div class="text-base font-semibold text-stone-800">{{ auth()->user()->name }}</div>
                    <div class="text-sm font-medium text-stone-500">{{ auth()->user()->email }}</div>
                </div>
                <div class="px-2 space-y-1">
                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'owner')
                        <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-stone-700 hover:bg-amber-50 hover:text-amber-800">Dashboard Admin</a>
                    @else
                        <a href="{{ route('customer.dashboard') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-stone-700 hover:bg-amber-50 hover:text-amber-800">Dashboard Saya</a>
                        <a href="{{ route('customer.orders.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-stone-700 hover:bg-amber-50 hover:text-amber-800">Pesanan Saya</a>
                    @endif
                    <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-stone-700 hover:bg-amber-50 hover:text-amber-800">Edit Profil</a>
                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                        @csrf
                        <button type="submit" class="block w-full text-left px-3 py-2 rounded-lg text-base font-medium text-rose-600 hover:bg-rose-50">
                            Keluar
                        </button>
                    </form>
                </div>
            @else
                <div class="px-5 flex flex-col gap-2">
                    <a href="{{ route('login') }}" class="w-full text-center px-4 py-2.5 border border-amber-600 text-amber-700 rounded-lg text-sm font-medium hover:bg-amber-50 transition">Masuk</a>
                    <a href="{{ route('register') }}" class="w-full text-center px-4 py-2.5 bg-amber-700 text-white rounded-lg text-sm font-semibold hover:bg-amber-800 shadow transition">Daftar</a>
                </div>
            @endauth
        </div>
    </div>
</nav>
