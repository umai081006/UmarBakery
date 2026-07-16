@extends('layouts.customer')

@section('content')
<div class="space-y-10">

    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-serif text-cocoa tracking-tight">My Wishlist</h1>
            <p class="text-sm font-sans text-cocoa/50 mt-2">Your collection of favorite artisan bakes.</p>
        </div>
    </div>

    @if($wishlists->isEmpty())
        <div class="bg-white rounded-4xl p-16 text-center shadow-soft border border-dough/20">
            <svg class="h-12 w-12 text-dough mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
            <h3 class="text-xl font-serif text-cocoa">Your wishlist is empty</h3>
            <p class="text-sm font-sans text-cocoa/50 mt-2">You haven't saved any bakes yet.</p>
            <a href="{{ route('products.index') }}" class="mt-8 inline-flex items-center gap-2 text-sm font-mono font-semibold bg-caramel text-white px-8 py-4 rounded-full hover:bg-cocoa shadow-soft transition-all duration-300">Explore Menu</a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            @foreach($wishlists as $wishlist)
                <div class="group bg-white rounded-4xl shadow-soft hover:shadow-float transition-all duration-500 overflow-hidden flex flex-col h-full relative">
                    <!-- Image -->
                    <a href="{{ route('products.show', $wishlist->product->slug) }}" class="relative bg-dough w-full aspect-square overflow-hidden flex items-center justify-center block">
                        @if($wishlist->product->image_url)
                            <img src="{{ $wishlist->product->image_url }}" alt="{{ $wishlist->product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out">
                        @else
                            <div class="flex flex-col items-center justify-center text-caramel/50">
                                <span class="font-serif font-bold text-xl">UB</span>
                            </div>
                        @endif
                        
                        @if($wishlist->product->stock === 0)
                            <span class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm border border-strawberry/20 text-strawberry text-[10px] font-mono font-bold uppercase tracking-widest px-3 py-1 rounded-md">
                                Out of Stock
                            </span>
                        @endif

                        <form action="{{ route('customer.wishlists.toggle', $wishlist->product->id) }}" method="POST" class="absolute top-4 right-4 z-10" onclick="event.preventDefault(); this.submit();">
                            @csrf
                            <button type="submit" class="bg-white/80 backdrop-blur-md hover:bg-white text-strawberry p-2.5 rounded-full shadow-soft hover:shadow-float transition-all duration-300" title="Remove from Wishlist">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                            </button>
                        </form>
                    </a>

                    <!-- Details -->
                    <a href="{{ route('products.show', $wishlist->product->slug) }}" class="p-5 flex flex-col flex-1">
                        <span class="text-[10px] font-mono font-semibold text-cocoa/40 uppercase tracking-widest">{{ $wishlist->product->category->name }}</span>
                        <h3 class="font-serif text-lg text-cocoa mt-2 group-hover:text-caramel transition-colors line-clamp-1">
                            {{ $wishlist->product->name }}
                        </h3>
                        
                        <div class="mt-auto pt-4 flex items-center justify-between">
                            <p class="font-mono font-semibold text-caramel text-sm">
                                Rp {{ number_format($wishlist->product->price, 0, ',', '.') }}
                            </p>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
