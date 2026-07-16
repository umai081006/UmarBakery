@php
    $cartQuantity = 0;
    if (auth()->check()) {
        $cartQuantity = \App\Models\Cart::where('user_id', auth()->id())->sum('quantity');
    }
@endphp

<!-- Floating Cart Button -->
<a href="{{ route('cart.index') }}" class="fixed bottom-6 right-6 z-40 group flex items-center justify-center w-16 h-16 bg-cocoa text-white rounded-full shadow-float hover:scale-105 active:scale-95 transition-all duration-300">
    <div class="relative">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
        
        @if($cartQuantity > 0)
            <span class="absolute -top-2 -right-2 bg-strawberry text-white text-xs font-mono font-bold w-5 h-5 flex items-center justify-center rounded-full border-2 border-cocoa shadow-sm group-hover:scale-110 transition-transform">
                {{ $cartQuantity }}
            </span>
        @endif
    </div>
</a>
