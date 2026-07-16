@props(['product'])

<div class="group flex flex-col bg-white rounded-2xl shadow-soft hover:shadow-float transition-all duration-300 overflow-hidden relative border border-dough/30">
    <!-- Image Section -->
    <a href="{{ route('products.show', $product->slug) }}" class="relative w-full aspect-[4/3] overflow-hidden bg-lightgray block">
        @if($product->image_url)
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out">
        @else
            <div class="w-full h-full flex items-center justify-center text-caramel/50">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
        @endif
        
        <!-- Floating Quick Add -->
        <div class="absolute bottom-3 right-3 z-10" onclick="event.preventDefault();">
            <form action="{{ route('cart.store') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="bg-caramel text-white w-10 h-10 rounded-full flex items-center justify-center shadow-md hover:bg-cocoa transition-colors active:scale-95" aria-label="Quick Add">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </button>
            </form>
        </div>
    </a>

    <!-- Content Section -->
    <a href="{{ route('products.show', $product->slug) }}" class="p-4 flex flex-col grow">
        <h3 class="font-serif text-[18px] text-cocoa leading-tight mb-1">{{ $product->name }}</h3>
        
        <div class="mt-auto pt-2 flex items-center justify-between">
            <span class="font-mono text-caramel font-semibold text-base">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
            <div class="flex items-center gap-1">
                <svg class="w-4 h-4 text-butter" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                <span class="font-sans text-sm text-cocoa font-medium">4.8</span>
            </div>
        </div>
    </a>
</div>
