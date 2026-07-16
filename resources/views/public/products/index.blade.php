@extends('layouts.app')

@section('content')
<div class="bg-cream py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="border-b border-dough/30 pb-8 mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-4xl font-serif text-cocoa">Our Menu</h1>
                <p class="text-sm text-cocoa/60 mt-2 font-sans">Browse our selection of freshly baked goods.</p>
            </div>
            
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs font-mono text-cocoa/50">
                <a href="{{ route('home') }}" class="hover:text-caramel transition-colors">Home</a>
                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                <span class="text-cocoa font-medium">Menu</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-10">
            <!-- Sidebar Filters -->
            <div class="space-y-6">
                <!-- Search Box -->
                <div class="bg-white p-6 rounded-4xl shadow-soft">
                    <h3 class="font-mono text-xs font-semibold text-cocoa uppercase tracking-widest mb-4">Search</h3>
                    <form method="GET" action="{{ route('products.index') }}">
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        @if(request('sort'))
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                        @endif
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search breads..." class="w-full pl-4 pr-10 py-3 rounded-2xl border border-dough/50 focus:outline-none focus:ring-2 focus:ring-caramel focus:border-caramel text-sm font-sans placeholder-cocoa/30 bg-cream/50">
                            <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-cocoa/40 hover:text-caramel transition-colors">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.602 10.602Z" />
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Categories filter -->
                <div class="bg-white p-6 rounded-4xl shadow-soft">
                    <h3 class="font-mono text-xs font-semibold text-cocoa uppercase tracking-widest mb-4">Categories</h3>
                    <div class="flex flex-col gap-1">
                        <a href="{{ route('products.index', request()->except(['category', 'page'])) }}" class="flex items-center justify-between text-sm px-4 py-2.5 rounded-2xl {{ !request('category') ? 'bg-cocoa text-white font-semibold' : 'text-cocoa/70 hover:bg-dough/30 hover:text-cocoa' }} transition-all duration-200">
                            <span>All Bakes</span>
                        </a>
                        
                        @foreach($categories as $category)
                            <a href="{{ route('products.index', array_merge(request()->query(), ['category' => $category->slug])) }}" class="flex items-center justify-between text-sm px-4 py-2.5 rounded-2xl {{ request('category') === $category->slug ? 'bg-cocoa text-white font-semibold' : 'text-cocoa/70 hover:bg-dough/30 hover:text-cocoa' }} transition-all duration-200">
                                <span>{{ $category->name }}</span>
                                <span class="text-xs {{ request('category') === $category->slug ? 'bg-white/20' : 'bg-dough/50 text-cocoa/60' }} px-2 py-0.5 rounded-full font-mono">
                                    {{ $category->products_count ?? $category->products()->where('is_active', true)->count() }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Sort options -->
                <div class="bg-white p-6 rounded-4xl shadow-soft">
                    <h3 class="font-mono text-xs font-semibold text-cocoa uppercase tracking-widest mb-4">Sort By</h3>
                    <div class="flex flex-col gap-1">
                        <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'newest'])) }}" class="text-sm px-4 py-2.5 rounded-2xl {{ request('sort', 'newest') === 'newest' ? 'bg-dough/40 text-cocoa font-semibold' : 'text-cocoa/60 hover:bg-dough/20 hover:text-cocoa' }} transition-all duration-200">
                            Newest First
                        </a>
                        <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'price_asc'])) }}" class="text-sm px-4 py-2.5 rounded-2xl {{ request('sort') === 'price_asc' ? 'bg-dough/40 text-cocoa font-semibold' : 'text-cocoa/60 hover:bg-dough/20 hover:text-cocoa' }} transition-all duration-200">
                            Price: Low to High
                        </a>
                        <a href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'price_desc'])) }}" class="text-sm px-4 py-2.5 rounded-2xl {{ request('sort') === 'price_desc' ? 'bg-dough/40 text-cocoa font-semibold' : 'text-cocoa/60 hover:bg-dough/20 hover:text-cocoa' }} transition-all duration-200">
                            Price: High to Low
                        </a>
                    </div>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="lg:col-span-3 space-y-8">
                @if($products->isEmpty())
                    <div class="bg-white rounded-4xl p-16 text-center shadow-soft">
                        <div class="w-20 h-20 bg-dough/30 rounded-full flex items-center justify-center text-caramel mx-auto mb-6">
                            <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="text-xl font-serif text-cocoa">No products found</h3>
                        <p class="text-cocoa/50 text-sm mt-2 font-sans">Try a different keyword or browse another category.</p>
                        <a href="{{ route('products.index') }}" class="mt-8 inline-flex items-center gap-2 font-mono text-sm font-semibold bg-caramel text-white px-6 py-3 rounded-full hover:bg-cocoa transition-colors">Reset Search</a>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($products as $product)
                            <x-product-card :product="$product" />
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="pt-8">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
