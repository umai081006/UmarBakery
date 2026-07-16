<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
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
    <body class="flex flex-col min-h-screen font-sans text-cocoa antialiased selection:bg-butter selection:text-cocoa bg-cream" x-data="{ sidebarOpen: false }">

        <!-- Alerts -->
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="fixed bottom-5 right-5 z-[100] max-w-sm w-full bg-white border border-dough text-cocoa px-5 py-4 rounded-2xl shadow-float flex items-start gap-3" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform translate-y-2">
                <svg class="h-5 w-5 text-caramel mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <p class="text-sm font-sans font-medium">{{ session('success') }}</p>
                <button @click="show = false" class="ml-auto text-cocoa/30 hover:text-cocoa">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 7000)" class="fixed bottom-5 right-5 z-[100] max-w-sm w-full bg-white border border-strawberry/30 text-strawberry px-5 py-4 rounded-2xl shadow-float flex items-start gap-3" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform translate-y-2">
                <svg class="h-5 w-5 text-strawberry mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <p class="text-sm font-sans font-medium">{{ session('error') }}</p>
                <button @click="show = false" class="ml-auto text-strawberry/50 hover:text-strawberry">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        @endif

        <!-- Layout Wrapper -->
        <div class="flex flex-1 min-h-screen">
            <!-- Sidebar Desktop -->
            <aside class="hidden lg:flex flex-col w-72 bg-cocoa text-cream/70 shrink-0 fixed inset-y-0 left-0 z-30">
                <div class="p-8 border-b border-cream/10">
                    <a href="{{ route('home') }}" class="block group">
                        <span class="font-serif text-2xl font-bold text-white tracking-tight">
                            Umar <span class="text-butter italic">Bakery</span>
                        </span>
                        <span class="text-[10px] uppercase font-mono font-semibold tracking-widest text-cream/30 mt-1 block">Customer Portal</span>
                    </a>
                </div>
                <nav class="flex-1 p-5 space-y-1 overflow-y-auto">
                    <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-mono font-medium {{ request()->routeIs('customer.dashboard') ? 'bg-caramel text-white' : 'hover:bg-cream/10 hover:text-white' }} transition-all duration-200">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('customer.orders.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-mono font-medium {{ request()->routeIs('customer.orders.*') ? 'bg-caramel text-white' : 'hover:bg-cream/10 hover:text-white' }} transition-all duration-200">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        My Orders
                    </a>
                    <a href="{{ route('customer.addresses.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-mono font-medium {{ request()->routeIs('customer.addresses.*') ? 'bg-caramel text-white' : 'hover:bg-cream/10 hover:text-white' }} transition-all duration-200">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Addresses
                    </a>
                    <a href="{{ route('customer.wishlists.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-mono font-medium {{ request()->routeIs('customer.wishlists.*') ? 'bg-caramel text-white' : 'hover:bg-cream/10 hover:text-white' }} transition-all duration-200">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        Wishlist
                    </a>
                    <a href="{{ route('customer.notifications.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-mono font-medium {{ request()->routeIs('customer.notifications.*') ? 'bg-caramel text-white' : 'hover:bg-cream/10 hover:text-white' }} transition-all duration-200">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        Notifications
                        @php $nb = auth()->user()->unreadNotifications()->count() @endphp
                        @if($nb > 0)
                            <span class="ml-auto text-[9px] font-mono font-bold bg-strawberry text-white rounded-full px-2 py-0.5">{{ $nb }}</span>
                        @endif
                    </a>
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-mono font-medium {{ request()->routeIs('profile.edit') ? 'bg-caramel text-white' : 'hover:bg-cream/10 hover:text-white' }} transition-all duration-200">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        My Profile
                    </a>
                </nav>
                <div class="p-5 border-t border-cream/10">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 w-full px-4 py-3 rounded-2xl text-sm font-mono font-medium text-strawberry/70 hover:bg-cream/10 hover:text-strawberry transition-all duration-200">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Sign Out
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Main Area -->
            <div class="flex-1 flex flex-col min-w-0 lg:ml-72">
                <!-- Top Header / Mobile Bar -->
                <header class="bg-white/80 backdrop-blur-lg border-b border-dough/30 h-16 flex items-center justify-between px-6 shrink-0 sticky top-0 z-20">
                    <!-- Mobile Hamburger -->
                    <button @click="sidebarOpen = true" class="lg:hidden text-cocoa/50 hover:text-caramel transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    
                    <a href="{{ route('products.index') }}" class="text-xs font-mono font-semibold text-caramel hover:text-cocoa flex items-center gap-2 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Back to Shop
                    </a>

                    <div class="flex items-center gap-4">
                        @auth
                            <x-notification-bell />
                        @endauth
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-dough rounded-full flex items-center justify-center text-cocoa font-serif font-bold text-sm">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <span class="text-sm font-mono font-medium text-cocoa hidden sm:inline">{{ auth()->user()->name }}</span>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 p-6 sm:p-10 overflow-y-auto">
                    @yield('content')
                </main>
            </div>
        </div>

        <!-- Sidebar Mobile Panel -->
        <div x-show="sidebarOpen" class="fixed inset-0 z-50 lg:hidden flex" x-transition style="display: none;">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-cocoa/60 backdrop-blur-sm" @click="sidebarOpen = false"></div>
            
            <!-- Panel Content -->
            <aside class="relative flex flex-col w-72 bg-cocoa text-cream/70 max-w-xs z-50" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full">
                <div class="p-6 border-b border-cream/10 flex justify-between items-center">
                    <span class="font-serif text-xl font-bold text-white">Umar <span class="text-butter italic">Bakery</span></span>
                    <button @click="sidebarOpen = false" class="text-cream/30 hover:text-white transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <nav class="flex-1 p-4 space-y-1">
                    <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-mono font-medium {{ request()->routeIs('customer.dashboard') ? 'bg-caramel text-white' : 'hover:bg-cream/10 hover:text-white' }} transition-all" @click="sidebarOpen = false">
                        Dashboard
                    </a>
                    <a href="{{ route('customer.orders.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-mono font-medium {{ request()->routeIs('customer.orders.*') ? 'bg-caramel text-white' : 'hover:bg-cream/10 hover:text-white' }} transition-all" @click="sidebarOpen = false">
                        My Orders
                    </a>
                    <a href="{{ route('customer.addresses.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-mono font-medium {{ request()->routeIs('customer.addresses.*') ? 'bg-caramel text-white' : 'hover:bg-cream/10 hover:text-white' }} transition-all" @click="sidebarOpen = false">
                        Addresses
                    </a>
                    <a href="{{ route('customer.wishlists.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-mono font-medium {{ request()->routeIs('customer.wishlists.*') ? 'bg-caramel text-white' : 'hover:bg-cream/10 hover:text-white' }} transition-all" @click="sidebarOpen = false">
                        Wishlist
                    </a>
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-mono font-medium {{ request()->routeIs('profile.edit') ? 'bg-caramel text-white' : 'hover:bg-cream/10 hover:text-white' }} transition-all" @click="sidebarOpen = false">
                        My Profile
                    </a>
                </nav>
                <div class="p-4 border-t border-cream/10">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 w-full px-4 py-3 rounded-2xl text-sm font-mono font-medium text-strawberry/70 hover:bg-cream/10 hover:text-strawberry transition-all">
                            Sign Out
                        </button>
                    </form>
                </div>
            </aside>
        </div>

        @stack('scripts')
    </body>
</html>
