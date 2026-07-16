<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-stone-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Umar Bakery') }} - Panel Admin</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body {
                font-family: 'Outfit', sans-serif;
            }
        </style>
    </head>
    <body class="flex flex-col min-h-screen text-stone-850 antialiased selection:bg-amber-200 selection:text-amber-900" x-data="{ sidebarOpen: false }">
        
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

        <!-- Layout Wrapper -->
        <div class="flex flex-1">
            <!-- Sidebar Desktop -->
            <aside class="hidden lg:flex flex-col w-64 bg-stone-900 border-r border-stone-850 text-stone-300 shrink-0">
                <div class="p-6 border-b border-stone-800">
                    <a href="{{ route('home') }}" class="text-xl font-bold tracking-tight text-white block hover:text-amber-400 transition">
                        UMAR BAKERY
                    </a>
                    <span class="text-[10px] uppercase font-bold tracking-wider text-amber-500 mt-0.5 block">Panel Admin ({{ auth()->user()->role }})</span>
                </div>
                <nav class="flex-1 p-4 space-y-1">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('admin.dashboard') ? 'bg-amber-700 text-white' : 'hover:bg-stone-800 hover:text-white' }} transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('admin.categories.*') ? 'bg-amber-700 text-white' : 'hover:bg-stone-800 hover:text-white' }} transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        Kategori Roti
                    </a>
                    <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('admin.products.*') ? 'bg-amber-700 text-white' : 'hover:bg-stone-800 hover:text-white' }} transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg>
                        Semua Roti / Produk
                    </a>
                    <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('admin.orders.*') ? 'bg-amber-700 text-white' : 'hover:bg-stone-800 hover:text-white' }} transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        Daftar Pesanan
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('admin.settings.*') ? 'bg-amber-700 text-white' : 'hover:bg-stone-800 hover:text-white' }} transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Pengaturan
                    </a>
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('profile.edit') ? 'bg-amber-700 text-white' : 'hover:bg-stone-800 hover:text-white' }} transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Profil Saya
                    </a>
                </nav>
                <div class="p-4 border-t border-stone-800">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-sm font-semibold text-rose-400 hover:bg-stone-800 hover:text-rose-300 transition">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col min-w-0">
                <!-- Top Header / Mobile Bar -->
                <header class="bg-white border-b border-stone-200 h-16 flex items-center justify-between px-6 shrink-0">
                    <button @click="sidebarOpen = true" class="lg:hidden text-stone-500 hover:text-amber-700">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    
                    <a href="{{ route('home') }}" target="_blank" class="text-xs font-semibold text-stone-500 hover:text-amber-700 flex items-center gap-1 transition">
                        Lihat Website Utama
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>

                    <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-stone-700">{{ auth()->user()->name }}</span>
                        <span class="px-2 py-0.5 bg-amber-100 text-[10px] font-bold uppercase rounded-full text-amber-800">
                            {{ auth()->user()->role }}
                        </span>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 p-6 sm:p-10 overflow-y-auto">
                    @yield('content')
                </main>
            </div>
        </div>

        <!-- Sidebar Mobile Panel -->
        <div x-show="sidebarOpen" class="fixed inset-0 z-50 lg:hidden flex" x-transition>
            <div class="fixed inset-0 bg-stone-900/60" @click="sidebarOpen = false"></div>
            
            <aside class="relative flex flex-col w-64 bg-stone-900 text-stone-300 max-w-xs z-50" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full">
                <div class="p-6 border-b border-stone-800 flex justify-between items-center">
                    <span class="text-xl font-bold tracking-tight text-white">UMAR BAKERY</span>
                    <button @click="sidebarOpen = false" class="text-stone-400 hover:text-white">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <nav class="flex-1 p-4 space-y-1">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('admin.dashboard') ? 'bg-amber-700 text-white' : 'hover:bg-stone-800 hover:text-white' }} transition" @click="sidebarOpen = false">
                        Dashboard
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('admin.categories.*') ? 'bg-amber-700 text-white' : 'hover:bg-stone-800 hover:text-white' }} transition" @click="sidebarOpen = false">
                        Kategori Roti
                    </a>
                    <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('admin.products.*') ? 'bg-amber-700 text-white' : 'hover:bg-stone-800 hover:text-white' }} transition" @click="sidebarOpen = false">
                        Semua Roti / Produk
                    </a>
                    <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('admin.orders.*') ? 'bg-amber-700 text-white' : 'hover:bg-stone-800 hover:text-white' }} transition" @click="sidebarOpen = false">
                        Daftar Pesanan
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('admin.settings.*') ? 'bg-amber-700 text-white' : 'hover:bg-stone-800 hover:text-white' }} transition" @click="sidebarOpen = false">
                        Pengaturan
                    </a>
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold {{ request()->routeIs('profile.edit') ? 'bg-amber-700 text-white' : 'hover:bg-stone-800 hover:text-white' }} transition" @click="sidebarOpen = false">
                        Profil Saya
                    </a>
                </nav>
                <div class="p-4 border-t border-stone-800">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-sm font-semibold text-rose-400 hover:bg-stone-800 hover:text-rose-300 transition">
                            Keluar
                        </button>
                    </form>
                </div>
            </aside>
        </div>

    </body>
</html>
