<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-cream scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Umar Bakery') }} - Premium Artisan Bakery</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="flex flex-col min-h-screen font-sans text-cocoa antialiased selection:bg-butter selection:text-cocoa" x-data="{ sidebarOpen: false }">
        
        <!-- Header / Navbar -->
        <x-navbar />

        <!-- Alerts -->
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="fixed bottom-5 right-5 z-50 max-w-sm w-full bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl shadow-xl flex items-start gap-3" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform translate-y-2">
                <svg class="h-5 w-5 text-emerald-600 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <div class="flex-1">
                    <p class="font-semibold text-sm">Berhasil!</p>
                    <p class="text-xs text-emerald-600 mt-0.5">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="text-emerald-400 hover:text-emerald-600 shrink-0">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="fixed bottom-5 right-5 z-50 max-w-sm w-full bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl shadow-xl flex items-start gap-3" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform translate-y-2">
                <svg class="h-5 w-5 text-rose-600 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <div class="flex-1">
                    <p class="font-semibold text-sm">Gagal!</p>
                    <p class="text-xs text-rose-600 mt-0.5">{{ session('error') }}</p>
                </div>
                <button @click="show = false" class="text-rose-400 hover:text-rose-600 shrink-0">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        <!-- Main Content -->
        <main class="flex-1 mt-20">
            @yield('content')
        </main>
        
        <x-cart-float />

        <!-- Mobile Bottom Navigation -->
        <div class="md:hidden fixed bottom-0 w-full bg-white border-t border-dough/30 shadow-[0_-4px_24px_rgba(59,42,26,0.05)] z-40 pb-safe">
            <div class="flex justify-between items-center px-6 h-16">
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-caramel' : 'text-cocoa/40' }} hover:text-caramel transition-colors p-2">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3l8 6v12h-5v-7h-6v7H4V9l8-6zm0-2.5L2 8h2v14h8v-7h2v7h8V8h2L12 .5z"/></svg>
                </a>
                <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'text-caramel' : 'text-cocoa/40' }} hover:text-caramel transition-colors p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </a>
                @if(!auth()->check() || auth()->user()->isCustomer())
                    <a href="{{ route('cart.index') }}" class="{{ request()->routeIs('cart.*') ? 'text-caramel' : 'text-cocoa/40' }} hover:text-caramel transition-colors p-2 relative">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        @auth
                            @php $cartCount = \App\Models\Cart::where('user_id', auth()->id())->sum('quantity'); @endphp
                            @if($cartCount > 0)
                                <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-strawberry rounded-full"></span>
                            @endif
                        @endauth
                    </a>
                @endif
                <a href="{{ route('customer.wishlists.index') }}" class="{{ request()->routeIs('customer.wishlists.*') ? 'text-caramel' : 'text-cocoa/40' }} hover:text-caramel transition-colors p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </a>
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') || request()->routeIs('customer.*') ? 'text-caramel' : 'text-cocoa/40' }} hover:text-caramel transition-colors p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="hidden md:block">
            @include('public.partials.footer')
        </div>

    </body>
</html>
