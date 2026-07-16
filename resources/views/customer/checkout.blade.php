@extends('layouts.app')

@section('content')
<div class="bg-cream py-12" x-data="{ isSubmitting: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <h1 class="text-3xl font-serif text-cocoa mb-10 tracking-tight">Checkout</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 items-start">
            
            <!-- Recipient Information Form -->
            <div class="lg:col-span-2 bg-white rounded-4xl p-8 shadow-soft relative overflow-hidden">
                
                <!-- Loading Overlay -->
                <div x-show="isSubmitting" 
                     x-transition.opacity 
                     class="absolute inset-0 bg-white/70 backdrop-blur-sm z-20 flex flex-col items-center justify-center">
                    <svg class="animate-spin h-10 w-10 text-caramel mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="font-mono text-sm font-semibold text-cocoa animate-pulse">Processing your order...</p>
                </div>

                <h3 class="font-mono text-xs font-semibold text-cocoa uppercase tracking-widest border-b border-dough/30 pb-4 mb-6 flex justify-between items-center">
                    <span>Shipping Address</span>
                    <a href="{{ route('customer.addresses.index') }}" class="text-caramel hover:text-cocoa transition-colors">+ Manage Addresses</a>
                </h3>
                
                @if($addresses->isEmpty())
                    <div class="text-center py-12">
                        <svg class="h-12 w-12 text-dough mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <p class="text-sm font-sans text-cocoa/50 mb-6">You don't have any saved addresses yet.</p>
                        <a href="{{ route('customer.addresses.index') }}" class="bg-caramel hover:bg-cocoa text-white px-6 py-3 rounded-full text-sm font-mono font-semibold transition-colors inline-block">
                            Add New Address
                        </a>
                    </div>
                @else
                    <form method="POST" action="{{ route('checkout.store') }}" @submit="isSubmitting = true" class="space-y-8">
                        @csrf
                        
                        <div class="space-y-4">
                            @foreach($addresses as $addr)
                                <label class="block relative border {{ $addr->is_default ? 'border-caramel bg-cream/30 shadow-sm' : 'border-dough/50 bg-white hover:border-caramel/50' }} rounded-3xl p-6 cursor-pointer transition-all duration-300">
                                    <div class="flex items-start gap-5">
                                        <div class="mt-1 shrink-0">
                                            <input type="radio" name="address_id" value="{{ $addr->id }}" class="text-caramel focus:ring-caramel w-4 h-4 border-dough" {{ (old('address_id') == $addr->id || (is_null(old('address_id')) && $addr->is_default)) ? 'checked' : '' }} onchange="
                                                document.querySelectorAll('label').forEach(l => {
                                                    l.classList.remove('border-caramel', 'bg-cream/30', 'shadow-sm');
                                                    l.classList.add('border-dough/50', 'bg-white', 'hover:border-caramel/50');
                                                });
                                                this.closest('label').classList.remove('border-dough/50', 'bg-white', 'hover:border-caramel/50');
                                                this.closest('label').classList.add('border-caramel', 'bg-cream/30', 'shadow-sm');
                                            ">
                                        </div>
                                        <div class="flex-1 font-sans">
                                            <div class="flex items-center gap-3 mb-2">
                                                <span class="font-bold text-cocoa">{{ $addr->label }}</span>
                                                @if($addr->is_default)
                                                    <span class="bg-dough/40 text-cocoa font-mono text-[10px] font-bold px-2 py-0.5 rounded-md uppercase tracking-widest">Primary</span>
                                                @endif
                                            </div>
                                            <p class="text-cocoa font-semibold mb-1">{{ $addr->recipient_name }} <span class="text-cocoa/50 font-mono font-normal ml-1">({{ $addr->phone }})</span></p>
                                            <p class="text-cocoa/70 leading-relaxed text-sm">{{ $addr->address }}</p>
                                            <p class="text-cocoa/70 text-sm mt-1">{{ $addr->city }}, {{ $addr->postal_code }}</p>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                            @error('address_id')
                                <p class="text-strawberry text-xs mt-2 font-mono font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-6 border-t border-dough/30">
                            <label for="notes" class="block text-xs font-mono font-semibold text-cocoa uppercase tracking-widest mb-3">Order Notes (Optional)</label>
                            <textarea name="notes" id="notes" rows="2" class="w-full px-4 py-3 rounded-2xl border border-dough/50 bg-cream/30 focus:bg-white focus:outline-none focus:ring-2 focus:ring-caramel focus:border-caramel text-sm font-sans placeholder-cocoa/30 transition-colors" placeholder="e.g. Leave package at the security desk">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="text-strawberry text-xs mt-2 font-mono font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="bg-dough/20 p-5 rounded-2xl border border-dough flex items-start gap-4">
                            <svg class="h-6 w-6 text-caramel shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <div class="text-sm text-cocoa/80 leading-relaxed font-sans">
                                <p class="font-bold text-cocoa">Secure Payment Gateway</p>
                                <p class="mt-1">You will be redirected to our secure payment portal after placing the order to choose your preferred payment method (VA, QRIS, e-Wallet, etc).</p>
                            </div>
                        </div>

                        <button type="submit" :disabled="isSubmitting" class="w-full flex items-center justify-center gap-2 bg-cocoa hover:bg-caramel text-white font-mono font-semibold py-5 rounded-full shadow-soft hover:shadow-float transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!isSubmitting">Place Order</span>
                            <span x-show="isSubmitting">Processing...</span>
                        </button>
                    </form>
                @endif
            </div>

            <!-- Side Order Summaries -->
            <div class="bg-white rounded-4xl border border-dough/30 p-8 shadow-soft space-y-6 sticky top-24">
                <h3 class="font-mono text-xs font-semibold text-cocoa uppercase tracking-widest border-b border-dough/30 pb-4">Order Summary</h3>
                
                <div class="space-y-4 max-h-72 overflow-y-auto pr-2 custom-scrollbar">
                    @foreach($items as $item)
                        <div class="flex gap-4 justify-between items-center text-sm">
                            <div class="flex gap-4 items-center min-w-0">
                                <div class="w-12 h-12 bg-dough/30 rounded-xl overflow-hidden flex items-center justify-center shrink-0">
                                    @if($item->product->image_url)
                                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-[10px] font-serif font-bold text-cocoa/40">UB</span>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-semibold font-sans text-cocoa truncate text-sm">{{ $item->product->name }}</h4>
                                    <span class="text-xs text-cocoa/50 font-mono block mt-0.5">Qty: {{ $item->quantity }}</span>
                                </div>
                            </div>
                            <span class="font-mono font-semibold text-caramel shrink-0 text-sm">
                                Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-dough/30 pt-6 space-y-3 text-sm font-sans">
                    <div class="flex justify-between text-cocoa/60">
                        <span>Subtotal</span>
                        <span class="font-mono">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-cocoa/60">
                        <span>Shipping</span>
                        <span class="font-mono">Rp {{ number_format($shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    @if(isset($discount_amount) && $discount_amount > 0)
                        <div class="flex justify-between text-caramel font-semibold">
                            <span>Promo Discount</span>
                            <span class="font-mono">-Rp {{ number_format($discount_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="border-t border-dough/30 pt-4 flex justify-between font-mono font-bold text-cocoa text-lg">
                        <span>Total to Pay</span>
                        <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: theme('colors.dough');
        border-radius: 20px;
    }
</style>
@endsection
