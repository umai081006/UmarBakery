@extends('layouts.app')

@section('content')
<div class="bg-cream py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-mono text-cocoa/50 mb-8">
            <a href="{{ route('home') }}" class="hover:text-caramel transition-colors">Home</a>
            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('products.index') }}" class="hover:text-caramel transition-colors">Menu</a>
            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            <span class="text-cocoa font-medium line-clamp-1">{{ $product->name }}</span>
        </div>

        <!-- Product Card -->
        <div class="bg-white rounded-4xl shadow-soft overflow-hidden p-6 sm:p-10 lg:p-16 mb-20">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">
                
                <!-- Product Image -->
                <div class="space-y-4">
                    <div class="relative bg-dough/30 rounded-4xl overflow-hidden aspect-square flex items-center justify-center">
                        @if($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="flex flex-col items-center justify-center text-caramel/30">
                                <svg class="h-24 w-24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" /></svg>
                                <span class="text-sm text-caramel/40 font-serif mt-2">Umar Bakery</span>
                            </div>
                        @endif

                        @auth
                            @php $inWishlist = isset($wishlistIds) && in_array($product->id, $wishlistIds); @endphp
                            <form action="{{ route('customer.wishlists.toggle', $product->id) }}" method="POST" class="absolute top-6 right-6 z-10">
                                @csrf
                                <button type="submit" class="bg-white/80 backdrop-blur-md hover:bg-white {{ $inWishlist ? 'text-strawberry' : 'text-cocoa/30 hover:text-strawberry' }} p-3 rounded-full shadow-soft transition-all duration-300">
                                    <svg class="h-6 w-6" fill="{{ $inWishlist ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="absolute top-6 right-6 z-10 bg-white/80 backdrop-blur-md hover:bg-white text-cocoa/30 hover:text-strawberry p-3 rounded-full shadow-soft transition-all duration-300">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Product Details -->
                <div class="space-y-8">
                    <div>
                        <span class="inline-block px-3 py-1 text-[10px] font-mono font-semibold uppercase bg-dough/40 text-cocoa tracking-widest rounded-full mb-4">
                            {{ $product->category->name }}
                        </span>
                        <div class="flex items-center gap-3 mb-3">
                            @if($product->reviews_count > 0)
                                <div class="flex items-center gap-1 text-butter">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    <span class="text-sm font-mono font-bold text-cocoa">{{ number_format($product->reviews_avg_rating, 1) }}</span>
                                </div>
                                <span class="text-xs text-cocoa/40 font-mono">({{ $product->reviews_count }} reviews)</span>
                            @else
                                <span class="text-xs font-mono text-cocoa/40">No reviews yet</span>
                            @endif
                        </div>
                        <h1 class="text-3xl lg:text-4xl font-serif text-cocoa leading-tight">
                            {{ $product->name }}
                        </h1>
                        <p class="font-mono text-xs text-cocoa/40 mt-2">SKU: {{ $product->sku }}</p>
                    </div>

                    <!-- Price -->
                    <div class="p-6 bg-cream rounded-4xl flex items-center justify-between">
                        <div>
                            <span class="text-xs text-cocoa/50 font-mono">Price</span>
                            <p class="text-3xl font-mono font-bold text-caramel">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="text-right">
                            @if($product->stock > 0)
                                <span class="inline-flex items-center gap-2 text-xs font-mono font-semibold text-cocoa bg-white border border-dough/50 px-4 py-2 rounded-full">
                                    <span class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></span>
                                    In Stock ({{ $product->stock }})
                                </span>
                            @else
                                <span class="inline-flex items-center gap-2 text-xs font-mono font-semibold text-strawberry bg-white border border-strawberry/20 px-4 py-2 rounded-full">
                                    <span class="h-2 w-2 rounded-full bg-strawberry"></span>
                                    Out of Stock
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Info Grid -->
                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div class="bg-cream rounded-2xl p-4">
                            <span class="text-cocoa/40 font-mono block">Net Weight</span>
                            <span class="text-cocoa font-mono font-semibold mt-1 inline-block">{{ $product->weight }}g</span>
                        </div>
                        <div class="bg-cream rounded-2xl p-4">
                            <span class="text-cocoa/40 font-mono block">Packaging</span>
                            <span class="text-cocoa font-mono font-semibold mt-1 inline-block">Premium Wrap</span>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <h3 class="font-mono text-xs font-semibold text-cocoa uppercase tracking-widest mb-3">Description</h3>
                        <p class="text-sm text-cocoa/70 leading-relaxed font-sans">
                            {{ $product->description }}
                        </p>
                    </div>

                    @if($product->composition)
                        <div>
                            <h3 class="font-mono text-xs font-semibold text-cocoa uppercase tracking-widest mb-3">Ingredients</h3>
                            <p class="text-xs text-cocoa/50 leading-relaxed italic font-sans">
                                {{ $product->composition }}
                            </p>
                        </div>
                    @endif

                    <!-- Add to Cart -->
                    <div class="border-t border-dough/30 pt-8">
                        @auth
                            @if(auth()->user()->isCustomer())
                                @if($product->stock > 0)
                                    <form method="POST" action="{{ route('cart.store') }}" class="space-y-6" x-data="{ qty: 1 }">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        
                                        <div class="flex items-center gap-6">
                                            <span class="text-sm font-mono font-medium text-cocoa">Quantity:</span>
                                            <div class="flex items-center border border-dough rounded-full bg-cream overflow-hidden">
                                                <button type="button" @click="if(qty > 1) qty--" class="px-4 py-3 text-cocoa/50 hover:text-cocoa hover:bg-dough/30 transition-colors font-mono">−</button>
                                                <input type="number" name="quantity" x-model="qty" readonly class="w-12 text-center border-none bg-transparent focus:ring-0 text-sm font-mono font-bold text-cocoa py-1">
                                                <button type="button" @click="if(qty < {{ $product->stock }}) qty++" class="px-4 py-3 text-cocoa/50 hover:text-cocoa hover:bg-dough/30 transition-colors font-mono">+</button>
                                            </div>
                                        </div>

                                        <button type="submit" class="w-full flex items-center justify-center gap-3 bg-caramel text-white font-mono font-semibold py-5 px-8 rounded-full hover:bg-cocoa shadow-soft hover:shadow-float transition-all duration-300 active:scale-95">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                            </svg>
                                            Add to Cart
                                        </button>
                                    </form>
                                @else
                                    <button disabled class="w-full bg-dough text-cocoa/40 font-mono font-semibold py-5 px-8 rounded-full cursor-not-allowed">
                                        Currently Out of Stock
                                    </button>
                                @endif
                            @else
                                <div class="bg-cream p-6 rounded-4xl text-center text-sm font-sans text-cocoa/70">
                                    Please log in as a customer to purchase.
                                </div>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="w-full flex items-center justify-center gap-2 bg-caramel text-white font-mono font-semibold py-5 px-8 rounded-full hover:bg-cocoa shadow-soft transition-all duration-300 text-center">
                                Sign in to Purchase
                            </a>
                        @endauth
                    </div>
                </div>

            </div>
        </div>

        <!-- Customer Reviews -->
        <div class="mb-20">
            <h2 class="text-3xl font-serif text-cocoa mb-10">Customer Reviews</h2>
            
            @if($product->reviews->isEmpty())
                <div class="bg-white rounded-4xl p-12 text-center shadow-soft">
                    <svg class="h-12 w-12 text-dough mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    <p class="text-cocoa/50 text-sm font-sans">No reviews yet. Be the first to share your experience!</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($product->reviews as $review)
                        <div class="bg-white rounded-4xl shadow-soft p-8 flex flex-col">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-dough flex items-center justify-center text-cocoa font-serif font-bold text-sm">
                                        {{ substr($review->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="font-mono font-semibold text-cocoa text-sm">{{ $review->user->name }}</h4>
                                        <p class="text-[10px] text-cocoa/40 font-mono">{{ $review->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="flex text-butter">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $review->rating ? 'fill-current' : 'text-dough fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-sm text-cocoa/70 leading-relaxed italic font-sans flex-1">
                                "{{ $review->comment ?: 'No comment left.' }}"
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Related Products -->
        @if($relatedProducts->isNotEmpty())
            <div>
                <h2 class="text-3xl font-serif text-cocoa mb-10">You Might Also Like</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    @foreach($relatedProducts as $relProduct)
                        <x-product-card :product="$relProduct" />
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
