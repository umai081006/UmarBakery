@extends('layouts.app')

@section('content')
<div class="bg-cream py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <h1 class="text-4xl font-serif text-cocoa mb-10">Your Cart</h1>

        @if($items->isEmpty())
            <div class="bg-white rounded-4xl p-16 text-center shadow-soft">
                <div class="w-20 h-20 bg-dough/30 rounded-full flex items-center justify-center text-caramel mx-auto mb-6">
                    <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-serif text-cocoa">Your cart is empty</h2>
                <p class="text-cocoa/50 text-sm mt-2 font-sans">You haven't added any delicious bakes yet.</p>
                <a href="{{ route('products.index') }}" class="mt-8 inline-flex items-center gap-2 font-mono text-sm font-semibold bg-caramel text-white px-8 py-4 rounded-full hover:bg-cocoa transition-colors">
                    Start Shopping
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 items-start">
                
                <!-- Cart Items List -->
                <div class="lg:col-span-2 space-y-4">
                    @foreach($items as $item)
                        <div class="bg-white rounded-4xl shadow-soft p-6 flex gap-6 items-center hover:shadow-float transition-shadow duration-300">
                            <!-- Image -->
                            <div class="w-24 h-24 bg-dough/30 rounded-2xl overflow-hidden shrink-0 flex items-center justify-center">
                                @if($item->product->image_url)
                                    <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-caramel/30 font-serif text-sm">UB</span>
                                @endif
                            </div>

                            <!-- Details -->
                            <div class="flex-1 flex flex-col justify-between min-w-0">
                                <div class="flex justify-between items-start">
                                    <div class="min-w-0">
                                        <h3 class="font-serif text-cocoa text-lg leading-tight truncate">{{ $item->product->name }}</h3>
                                        <p class="text-xs text-cocoa/40 mt-0.5 font-mono">SKU: {{ $item->product->sku }}</p>
                                    </div>
                                    <form method="POST" action="{{ route('cart.destroy', $item->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-cocoa/30 hover:text-strawberry transition-colors p-1 ml-4 shrink-0">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>

                                <div class="flex justify-between items-end mt-4">
                                    <p class="font-mono font-semibold text-caramel">
                                        Rp {{ number_format($item->product->price, 0, ',', '.') }}
                                    </p>
                                    
                                    <!-- Qty Selector -->
                                    <form method="POST" action="{{ route('cart.update', $item->id) }}" class="flex items-center border border-dough rounded-full bg-cream overflow-hidden">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" name="quantity" value="{{ $item->quantity - 1 }}" class="px-3 py-2 text-xs text-cocoa/50 hover:text-cocoa hover:bg-dough/30 transition-colors font-mono">−</button>
                                        <span class="w-8 text-center text-xs font-mono font-bold text-cocoa">{{ $item->quantity }}</span>
                                        <button type="submit" name="quantity" value="{{ $item->quantity + 1 }}" class="px-3 py-2 text-xs text-cocoa/50 hover:text-cocoa hover:bg-dough/30 transition-colors font-mono">+</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Order Summary -->
                <div class="bg-white rounded-4xl shadow-soft p-8 space-y-6 sticky top-24">
                    <!-- Promo Section -->
                    <div class="border-b border-dough/30 pb-6">
                        <h3 class="font-mono text-xs font-semibold text-cocoa uppercase tracking-widest mb-4">Promo Code</h3>
                        @if($promo_code)
                            <div class="flex items-center justify-between bg-cream p-3 rounded-2xl border border-dough/30">
                                <div>
                                    <span class="font-mono text-xs font-bold text-cocoa bg-dough/40 px-2 py-1 rounded-lg">{{ $promo_code }}</span>
                                    <p class="text-[10px] text-caramel mt-1 font-mono font-semibold">{{ $promo_message }}</p>
                                </div>
                                <form method="POST" action="{{ route('cart.promo.remove') }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-cocoa/30 hover:text-strawberry transition-colors">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </form>
                            </div>
                        @else
                            <form method="POST" action="{{ route('cart.promo.apply') }}" class="flex gap-2">
                                @csrf
                                <input type="text" name="promo_code" placeholder="Enter code" class="w-full text-sm font-mono rounded-full border border-dough/50 focus:outline-none focus:ring-2 focus:ring-caramel uppercase px-4 py-2.5 bg-cream/50" required>
                                <button type="submit" class="bg-cocoa hover:bg-caramel text-white text-sm font-mono font-semibold px-5 rounded-full transition-colors whitespace-nowrap">Apply</button>
                            </form>
                            @if(session('applied_promo_code') && !$promo_code)
                                <p class="text-strawberry text-xs mt-2 font-mono font-semibold">{{ $promo_message }}</p>
                            @endif
                        @endif
                    </div>

                    <h3 class="font-mono text-xs font-semibold text-cocoa uppercase tracking-widest">Order Summary</h3>
                    <div class="space-y-3 text-sm font-sans">
                        <div class="flex justify-between text-cocoa/60">
                            <span>Subtotal</span>
                            <span class="font-mono">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-cocoa/60">
                            <span>Shipping (Flat)</span>
                            <span class="font-mono">Rp {{ number_format($shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        @if($discount_amount > 0)
                            <div class="flex justify-between text-caramel font-semibold">
                                <span>Promo Discount</span>
                                <span class="font-mono">-Rp {{ number_format($discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="border-t border-dough/30 pt-4 flex justify-between font-mono font-bold text-cocoa text-lg">
                            <span>Total</span>
                            <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <a href="{{ route('checkout.index') }}" class="w-full text-center block bg-caramel hover:bg-cocoa text-white font-mono font-semibold py-4 px-6 rounded-full shadow-soft hover:shadow-float transition-all duration-300">
                        Proceed to Checkout
                    </a>
                    
                    <div class="bg-cream p-4 rounded-2xl text-center text-xs text-cocoa/40 font-sans">
                        Baked fresh and shipped the same day. 🍞
                    </div>
                </div>

            </div>
        @endif

    </div>
</div>
@endsection
