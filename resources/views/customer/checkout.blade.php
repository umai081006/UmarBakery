@extends('layouts.app')

@section('content')

{{-- Pass server-side data to Alpine component via a script block --}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('checkoutComponent', () => ({
            isSubmitting: false,
            selectedAddressId: {{ $defaultAddressId ?? 'null' }},
            shippingRates: {!! json_encode($shippingRates ?? []) !!},
            isLoadingRates: false,
            selectedShipping: null,
            subtotal: {{ $subtotal }},
            discount_amount: {{ $discount_amount ?? 0 }},
            errorMessage: '',
            notes: '',

            get total() {
                return (this.subtotal - this.discount_amount) + (this.selectedShipping ? this.selectedShipping.price : 0);
            },

            init() {
                if (this.shippingRates.length > 0) {
                    this.selectedShipping = this.shippingRates[0];
                }
            },

            async submitCheckout() {
                if (this.isSubmitting) return;
                this.isSubmitting = true;
                this.errorMessage = '';

                try {
                    let payload = {
                        address_id: this.selectedAddressId,
                        shipping_price: this.selectedShipping?.price || 0,
                        courier_name: this.selectedShipping?.courier_name || '',
                        courier_service: this.selectedShipping?.courier_service || '',
                        shipping_type: this.selectedShipping?.type || '',
                        notes: this.notes
                    };

                    let res = await fetch('{{ route('checkout.store') }}', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(payload)
                    });

                    let data;
                    try {
                        data = await res.json();
                    } catch (jsonErr) {
                        console.error('[checkout] Non-JSON response:', res.status, res.url);
                        this.errorMessage = 'Respons server tidak valid (status ' + res.status + '). Silakan coba lagi atau hubungi support.';
                        this.isSubmitting = false;
                        return;
                    }

                    if (res.ok && data.success) {
                        window.location.href = data.redirect_url;
                    } else {
                        this.errorMessage = data.message || 'Gagal memproses pesanan (status ' + res.status + '). Silakan coba lagi.';
                        this.isSubmitting = false;
                    }
                } catch (e) {
                    console.error('[checkout] Fetch error:', e);
                    this.errorMessage = 'Terjadi kesalahan jaringan atau server. Silakan coba lagi.';
                    this.isSubmitting = false;
                }
            },

            async fetchRates(addressId) {
                this.selectedAddressId = addressId;
                this.shippingRates = [];
                this.selectedShipping = null;
                this.isLoadingRates = true;

                try {
                    let res = await fetch('{{ route('checkout.shipping_rates') }}?address_id=' + addressId, {
                        headers: { 'Accept': 'application/json' }
                    });

                    let data = await res.json();
                    if (data.available) {
                        this.shippingRates = data.rates;
                        if (this.shippingRates.length > 0) {
                            this.selectedShipping = this.shippingRates[0];
                        }
                    } else {
                        alert(data.message || 'Pengiriman tidak tersedia untuk alamat ini.');
                    }
                } catch (e) {
                    alert('Gagal mengambil data ongkir. Silakan coba lagi.');
                } finally {
                    this.isLoadingRates = false;
                }
            }
        }));
    });
</script>

<div class="bg-cream py-12" x-data="checkoutComponent">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <h1 class="text-3xl font-serif text-cocoa mb-10 tracking-tight">Checkout</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 items-start">
            
            <!-- Recipient Information Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Address Section -->
                <div class="bg-white rounded-4xl p-8 shadow-soft relative overflow-hidden">
                    <h3 class="font-mono text-xs font-semibold text-cocoa uppercase tracking-widest border-b border-dough/30 pb-4 mb-6 flex justify-between items-center">
                        <span>Alamat Pengiriman</span>
                        <a href="{{ route('customer.addresses.index') }}" class="text-caramel hover:text-cocoa transition-colors">+ Kelola Alamat</a>
                    </h3>
                    
                    @if($addresses->isEmpty())
                        <div class="text-center py-12">
                            <svg class="h-12 w-12 text-dough mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <p class="text-sm font-sans text-cocoa/50 mb-6">Anda belum memiliki alamat tersimpan.</p>
                            <a href="{{ route('customer.addresses.index') }}" class="bg-caramel hover:bg-cocoa text-white px-6 py-3 rounded-full text-sm font-mono font-semibold transition-colors inline-block">
                                Tambah Alamat Baru
                            </a>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($addresses as $addr)
                                <label class="block relative border rounded-3xl p-6 cursor-pointer transition-all duration-300"
                                    :class="selectedAddressId == {{ $addr->id }} ? 'border-caramel bg-cream/30 shadow-sm' : 'border-dough/50 bg-white hover:border-caramel/50'">
                                    <div class="flex items-start gap-5">
                                        <div class="mt-1 shrink-0">
                                            <input type="radio" name="temp_address" value="{{ $addr->id }}" class="text-caramel focus:ring-caramel w-4 h-4 border-dough" 
                                                @change="fetchRates({{ $addr->id }})"
                                                :checked="selectedAddressId == {{ $addr->id }}">
                                        </div>
                                        <div class="flex-1 font-sans">
                                            <div class="flex items-center gap-3 mb-2">
                                                <span class="font-bold text-cocoa">{{ $addr->label }}</span>
                                                @if($addr->is_default)
                                                    <span class="bg-dough/40 text-cocoa font-mono text-[10px] font-bold px-2 py-0.5 rounded-md uppercase tracking-widest">Utama</span>
                                                @endif
                                            </div>
                                            <p class="text-cocoa font-semibold mb-1">{{ $addr->recipient_name }} <span class="text-cocoa/50 font-mono font-normal ml-1">({{ $addr->phone }})</span></p>
                                            <p class="text-cocoa/70 leading-relaxed text-sm">{{ $addr->address }}</p>
                                            <p class="text-cocoa/70 text-sm mt-1">{{ $addr->district ? $addr->district . ', ' : '' }}{{ $addr->city }}, {{ $addr->province }} {{ $addr->postal_code }}</p>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Shipping Options Section -->
                @if(!$addresses->isEmpty())
                <div class="bg-white rounded-4xl p-8 shadow-soft relative overflow-hidden">
                    <h3 class="font-mono text-xs font-semibold text-cocoa uppercase tracking-widest border-b border-dough/30 pb-4 mb-6">
                        Opsi Pengiriman
                    </h3>

                    <!-- Loading State -->
                    <div x-show="isLoadingRates" class="py-8 text-center">
                        <svg class="animate-spin h-8 w-8 text-caramel mx-auto mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="text-sm font-sans text-cocoa/60 animate-pulse">Menghitung ongkos kirim terbaik...</p>
                    </div>

                    <!-- Rates List -->
                    <div x-show="!isLoadingRates && shippingRates.length > 0" style="display: none;" class="space-y-4">
                        <template x-for="rate in shippingRates" :key="rate.courier_name + rate.courier_service">
                            <label class="block relative border rounded-2xl p-5 cursor-pointer transition-all duration-200"
                                :class="selectedShipping?.price === rate.price && selectedShipping?.courier_name === rate.courier_name ? 'border-caramel bg-cream/30 ring-1 ring-caramel' : 'border-dough/50 hover:border-caramel/50 bg-white'">
                                <div class="flex items-center gap-4">
                                    <div class="shrink-0">
                                        <input type="radio" name="temp_shipping" 
                                            class="text-caramel focus:ring-caramel w-4 h-4 border-dough"
                                            :checked="selectedShipping?.price === rate.price && selectedShipping?.courier_name === rate.courier_name"
                                            @change="selectedShipping = rate">
                                    </div>
                                    <div class="flex-1 flex justify-between items-center">
                                        <div>
                                            <p class="font-bold text-cocoa font-sans" x-text="rate.courier_name"></p>
                                            <p class="text-xs text-cocoa/60 font-mono uppercase tracking-wide mt-0.5"><span x-text="rate.courier_service"></span> &bull; <span x-text="rate.estimated_delivery"></span></p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-mono font-bold text-caramel text-lg" x-text="rate.price_formatted"></p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </template>
                    </div>

                    <!-- No Rates Found -->
                    <div x-show="!isLoadingRates && shippingRates.length === 0 && selectedAddressId" style="display: none;" class="py-8 text-center bg-strawberry/5 rounded-2xl border border-strawberry/20">
                        <p class="text-sm font-sans font-semibold text-strawberry">Maaf, wilayah Anda belum dapat kami layani.</p>
                        <p class="text-xs text-cocoa/60 mt-1">Silakan pilih alamat lain atau hubungi admin.</p>
                    </div>
                </div>

                <!-- Checkout Form -->
                <form method="POST" action="{{ route('checkout.store') }}" @submit.prevent="submitCheckout" class="bg-white rounded-4xl p-8 shadow-soft relative overflow-hidden">
                    @csrf
                    
                    <!-- Hidden inputs for final payload -->
                    <input type="hidden" name="address_id" :value="selectedAddressId">
                    <input type="hidden" name="shipping_price" :value="selectedShipping?.price || 0">
                    <input type="hidden" name="courier_name" :value="selectedShipping?.courier_name || ''">
                    <input type="hidden" name="courier_service" :value="selectedShipping?.courier_service || ''">
                    <input type="hidden" name="shipping_type" :value="selectedShipping?.type || ''">
                    
                    <!-- Error Message Display -->
                    <div x-show="errorMessage" x-transition style="display: none;" class="mt-4 p-4 bg-rose-50 border border-rose-200 text-rose-600 rounded-xl text-xs font-mono font-semibold">
                        <span x-text="errorMessage"></span>
                    </div>

                    <div class="mb-8 mt-4">
                        <label for="notes" class="block text-xs font-mono font-semibold text-cocoa uppercase tracking-widest mb-3">Catatan Pesanan (Opsional)</label>
                        <textarea x-model="notes" id="notes" rows="2" class="w-full px-4 py-3 rounded-2xl border border-dough/50 bg-cream/30 focus:bg-white focus:outline-none focus:ring-2 focus:ring-caramel focus:border-caramel text-sm font-sans placeholder-cocoa/30 transition-colors" placeholder="Misal: Tolong titipkan di pos satpam"></textarea>
                    </div>

                    <div class="bg-dough/20 p-5 rounded-2xl border border-dough flex items-start gap-4 mb-8">
                        <svg class="h-6 w-6 text-caramel shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <div class="text-sm text-cocoa/80 leading-relaxed font-sans">
                            <p class="font-bold text-cocoa">Sistem Pembayaran Otomatis</p>
                            <p class="mt-1">Anda akan diarahkan ke Midtrans setelah checkout untuk memilih metode pembayaran (GoPay, QRIS, Virtual Account, dll).</p>
                        </div>
                    </div>

                    <button type="submit" :disabled="isSubmitting || !selectedShipping || shippingRates.length === 0" 
                        class="w-full flex items-center justify-center gap-2 bg-cocoa hover:bg-caramel text-white font-mono font-semibold py-5 rounded-full shadow-soft hover:shadow-float transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!isSubmitting">Buat Pesanan &amp; Bayar</span>
                        <span x-show="isSubmitting">Memproses...</span>
                    </button>
                    
                    @if($errors->any())
                        <div class="mt-4 p-4 bg-rose-50 border border-rose-200 text-rose-600 rounded-xl text-xs font-mono">
                            <ul class="list-disc pl-4">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </form>
                @endif
            </div>

            <!-- Side Order Summaries -->
            <div class="bg-white rounded-4xl border border-dough/30 p-8 shadow-soft space-y-6 sticky top-24">
                <h3 class="font-mono text-xs font-semibold text-cocoa uppercase tracking-widest border-b border-dough/30 pb-4">Ringkasan Pesanan</h3>
                
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
                        <span>Ongkos Kirim</span>
                        <span class="font-mono text-caramel font-semibold" x-text="selectedShipping ? selectedShipping.price_formatted : 'Rp 0'"></span>
                    </div>
                    @if(isset($discount_amount) && $discount_amount > 0)
                        <div class="flex justify-between text-strawberry font-semibold">
                            <span>Diskon Promo</span>
                            <span class="font-mono">-Rp {{ number_format($discount_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="border-t border-dough/30 pt-4 flex justify-between font-mono font-bold text-cocoa text-lg">
                        <span>Total Pembayaran</span>
                        <span>Rp <span x-text="new Intl.NumberFormat('id-ID').format(total)"></span></span>
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
