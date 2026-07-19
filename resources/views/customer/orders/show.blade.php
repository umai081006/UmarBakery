@extends('layouts.customer')

@section('content')
<div class="space-y-10">
    
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
        <div>
            <div class="flex items-center gap-4">
                <a href="{{ route('customer.orders.index') }}" class="w-10 h-10 rounded-full bg-cream flex items-center justify-center text-cocoa/50 hover:text-cocoa hover:bg-dough/30 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <h1 class="text-3xl font-serif text-cocoa tracking-tight">Detail Pesanan</h1>
            </div>
            <p class="text-sm font-mono text-cocoa/50 mt-3 ml-14">Dipesan pada {{ $order->created_at->format('d M Y, H:i') }}</p>
        </div>

        <div class="flex items-center gap-4">
            @switch($order->status)
                @case('pending')
                    <span class="px-4 py-2 text-xs font-mono font-bold rounded-full bg-butter/20 text-cocoa uppercase tracking-widest">Menunggu Pembayaran</span>
                    @break
                @case('paid')
                    <span class="px-4 py-2 text-xs font-mono font-bold rounded-full bg-blue-50 text-blue-700 uppercase tracking-widest">Dibayar</span>
                    @break
                @case('processing')
                    <span class="px-4 py-2 text-xs font-mono font-bold rounded-full bg-orange-50 text-orange-700 uppercase tracking-widest">Diproses</span>
                    @break
                @case('shipped')
                    <span class="px-4 py-2 text-xs font-mono font-bold rounded-full bg-indigo-50 text-indigo-700 uppercase tracking-widest">Dikirim</span>
                    @break
                @case('delivered')
                    <span class="px-4 py-2 text-xs font-mono font-bold rounded-full bg-teal-50 text-teal-700 uppercase tracking-widest">Diterima</span>
                    @break
                @case('completed')
                    <span class="px-4 py-2 text-xs font-mono font-bold rounded-full bg-green-50 text-green-700 uppercase tracking-widest">Selesai</span>
                    @break
                @case('cancelled')
                    <span class="px-4 py-2 text-xs font-mono font-bold rounded-full bg-strawberry/10 text-strawberry uppercase tracking-widest">Dibatalkan</span>
                    @break
            @endswitch
            
            @if(in_array($order->status, ['pending', 'paid']))
            <form method="POST" action="{{ route('customer.orders.cancel', $order->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                    @csrf
                    <button type="submit" class="px-5 py-2 text-xs font-mono font-semibold rounded-full bg-white hover:bg-strawberry/10 text-cocoa/60 hover:text-strawberry border border-dough hover:border-strawberry/30 transition-colors">
                        Batalkan Pesanan
                    </button>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 items-start">
        
        <!-- Order Items & Shipping Address -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Address Card -->
            <div class="bg-white rounded-4xl border border-dough/30 p-8 shadow-soft">
                <h3 class="font-mono text-xs font-semibold text-cocoa uppercase tracking-widest border-b border-dough/30 pb-4 mb-6">Alamat Pengiriman</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm font-sans leading-relaxed">
                    <div>
                        <span class="text-cocoa/50 block text-xs font-mono uppercase tracking-widest mb-1">Penerima</span>
                        <span class="font-semibold text-cocoa text-base">{{ $order->recipient_name }}</span>
                    </div>
                    <div>
                        <span class="text-cocoa/50 block text-xs font-mono uppercase tracking-widest mb-1">Telepon</span>
                        <span class="font-semibold text-cocoa text-base">{{ $order->phone }}</span>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="text-cocoa/50 block text-xs font-mono uppercase tracking-widest mb-1">Alamat</span>
                        <span class="font-semibold text-cocoa text-base block">
                            {{ $order->address }}, {{ $order->city }}, {{ $order->postal_code }}
                        </span>
                    </div>
                    @if($order->notes)
                        <div class="sm:col-span-2 p-4 bg-cream/50 rounded-2xl border border-dough/50 text-sm text-cocoa/80 italic mt-2">
                            <span class="font-bold font-mono not-italic block mb-1 uppercase text-[10px] tracking-widest">Catatan Pesanan</span>
                            {{ $order->notes }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Items Table -->
            <div class="bg-white rounded-4xl border border-dough/30 shadow-soft overflow-hidden">
                <div class="px-8 py-6 border-b border-dough/30">
                    <h3 class="font-serif text-xl text-cocoa">Daftar Produk</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-cream text-[10px] uppercase font-mono font-bold text-cocoa/50 tracking-widest">
                            <tr>
                                <th class="px-8 py-4">Produk</th>
                                <th class="px-8 py-4">Harga</th>
                                <th class="px-8 py-4">Jml</th>
                                <th class="px-8 py-4 text-right">Total</th>
                                @if($order->status === 'completed')
                                    <th class="px-8 py-4 text-center">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-dough/20 font-sans">
                            @foreach($order->items as $item)
                                <tr class="hover:bg-cream/50 transition-colors">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 bg-dough/30 rounded-xl overflow-hidden flex items-center justify-center shrink-0">
                                                @if($item->product && $item->product->image_url)
                                                    <img src="{{ $item->product->image_url }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                                @else
                                                    <span class="text-xs font-serif font-bold text-cocoa/40">UB</span>
                                                @endif
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-cocoa">{{ $item->product_name }}</h4>
                                                <span class="text-xs font-mono text-cocoa/40 block mt-0.5">SKU: {{ $item->product_sku }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 text-cocoa/70 font-mono">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td class="px-8 py-5 font-semibold text-cocoa">{{ $item->quantity }}</td>
                                    <td class="px-8 py-5 text-right font-mono font-bold text-caramel">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    @if($order->status === 'completed')
                                        <td class="px-8 py-5 text-center" x-data="{ reviewModalOpen: false, rating: 5, hoverRating: 0 }">
                                            @php
                                                $hasReviewed = $order->reviews->contains('product_id', $item->product_id);
                                            @endphp
                                            @if($hasReviewed)
                                                <span class="text-xs font-mono font-bold text-green-600 bg-green-50 px-3 py-1.5 rounded-full border border-green-200">Sudah Direview</span>
                                            @else
                                                <button @click="reviewModalOpen = true" class="text-xs font-mono font-bold text-caramel bg-cream hover:bg-dough/30 px-4 py-2 rounded-full transition-colors border border-dough/50">Tulis Ulasan</button>

                                                <!-- Review Modal -->
                                                <div x-show="reviewModalOpen" style="display: none" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                                                    <!-- Backdrop -->
                                                    <div @click="reviewModalOpen = false" class="fixed inset-0 bg-cocoa/60 backdrop-blur-sm" x-transition.opacity></div>
                                                    
                                                    <div class="bg-white rounded-4xl w-full max-w-md shadow-float overflow-hidden flex flex-col relative z-10" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                                                        <div class="px-8 py-6 border-b border-dough/30 flex justify-between items-center bg-cream/50">
                                                            <h3 class="font-serif text-2xl text-cocoa">Tulis Ulasan</h3>
                                                            <button @click="reviewModalOpen = false" class="text-cocoa/30 hover:text-cocoa transition-colors">
                                                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                            </button>
                                                        </div>
                                                        <div class="p-8">
                                                            <div class="flex items-center gap-5 mb-8">
                                                                <div class="w-16 h-16 bg-dough/30 rounded-2xl overflow-hidden flex items-center justify-center shrink-0">
                                                                    @if($item->product && $item->product->image_url)
                                                                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                                                    @else
                                                                        <span class="text-sm font-serif font-bold text-cocoa/40">UB</span>
                                                                    @endif
                                                                </div>
                                                                <div>
                                                                    <h4 class="font-semibold font-sans text-cocoa text-lg">{{ $item->product_name }}</h4>
                                                                    <span class="text-sm font-sans text-cocoa/50">Bagikan pengalaman Anda</span>
                                                                </div>
                                                            </div>

                                                            <form method="POST" action="{{ route('customer.reviews.store') }}" class="space-y-6">
                                                                @csrf
                                                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                                <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                                                <input type="hidden" name="rating" x-model="rating">

                                                                <div class="space-y-3 text-center">
                                                                    <label class="block text-xs font-mono font-semibold text-cocoa uppercase tracking-widest">Rating</label>
                                                                    <div class="flex justify-center items-center gap-2">
                                                                        <template x-for="i in 5">
                                                                            <button type="button" 
                                                                                @mouseenter="hoverRating = i" 
                                                                                @mouseleave="hoverRating = 0" 
                                                                                @click="rating = i" 
                                                                                class="focus:outline-none transition-transform hover:scale-110 active:scale-95">
                                                                                <svg class="w-12 h-12 transition-colors duration-200" 
                                                                                    :class="{'text-butter': i <= (hoverRating || rating), 'text-dough': i > (hoverRating || rating)}" 
                                                                                    fill="currentColor" viewBox="0 0 20 20">
                                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                                                </svg>
                                                                            </button>
                                                                        </template>
                                                                    </div>
                                                                </div>

                                                                <div class="space-y-3">
                                                                    <label class="block text-xs font-mono font-semibold text-cocoa uppercase tracking-widest">Komentar (Opsional)</label>
                                                                    <textarea name="comment" rows="4" class="w-full rounded-2xl border border-dough/50 bg-cream/50 focus:bg-white focus:ring-2 focus:ring-caramel focus:border-caramel text-sm font-sans px-4 py-3 resize-none transition-colors" placeholder="Ceritakan pengalaman Anda..."></textarea>
                                                                </div>

                                                                <button type="submit" class="w-full bg-cocoa hover:bg-caramel text-white font-mono font-semibold py-4 rounded-full shadow-soft hover:shadow-float transition-all duration-300 mt-4 active:scale-95">
                                                                    Kirim Ulasan
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Payment Processing Details -->
        <div class="space-y-8">
            
            <!-- Summary Info Box -->
            <div class="bg-white rounded-4xl border border-dough/30 p-8 shadow-soft space-y-6">
                <h3 class="font-mono text-xs font-semibold text-cocoa uppercase tracking-widest border-b border-dough/30 pb-4">Ringkasan Pembayaran</h3>
                
                <div class="space-y-4 text-sm font-sans text-cocoa/70">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span class="font-mono font-semibold text-cocoa">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Ongkos Kirim</span>
                        <span class="font-mono font-semibold text-cocoa">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t border-dough/30 pt-4 flex justify-between font-mono font-bold text-caramel text-lg">
                        <span>Total Bayar</span>
                        <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Payment Section -->
            @php
                $payment = \App\Models\Payment::where('order_id', $order->id)->latest()->first();
            @endphp

            @if($order->status === 'paid' && $payment)
                <div class="bg-white rounded-4xl border border-dough/30 p-8 shadow-soft space-y-4">
                    <h3 class="font-mono text-xs font-semibold text-cocoa uppercase tracking-widest border-b border-dough/30 pb-4 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Pembayaran Berhasil Diverifikasi
                    </h3>
                    
                    <div class="grid grid-cols-2 gap-4 text-sm font-sans text-cocoa/80">
                        <div>
                            <span class="block text-xs font-mono uppercase tracking-widest text-cocoa/50 mb-1">Status</span>
                            <span class="font-bold text-green-600 uppercase">{{ $payment->status }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-mono uppercase tracking-widest text-cocoa/50 mb-1">Provider</span>
                            <span class="font-semibold uppercase">{{ $payment->provider }}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="block text-xs font-mono uppercase tracking-widest text-cocoa/50 mb-1">Transaction ID</span>
                            <span class="font-mono text-xs bg-cream px-2 py-1 rounded">{{ $payment->transaction_id }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-mono uppercase tracking-widest text-cocoa/50 mb-1">Tipe Pembayaran</span>
                            <span class="font-semibold">{{ $payment->raw_response['payment_type'] ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-mono uppercase tracking-widest text-cocoa/50 mb-1">Jumlah Dibayar</span>
                            <span class="font-bold text-caramel">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="col-span-2 border-t border-dough/30 pt-4 mt-2">
                            <span class="block text-xs font-mono uppercase tracking-widest text-cocoa/50 mb-1">Waktu Dibayar (Paid At)</span>
                            <span class="font-semibold">{{ $payment->paid_at ? $payment->paid_at->format('d M Y, H:i:s') : 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            @endif

            @if($order->status === 'pending')
                @if($payment && $payment->provider === 'midtrans' && $payment->snap_token)
                    <div class="bg-white rounded-4xl border border-dough/30 p-8 shadow-soft text-center">
                        <h3 class="font-mono text-xs font-semibold text-cocoa uppercase tracking-widest border-b border-dough/30 pb-4 mb-6">Selesaikan Pembayaran</h3>
                        <div class="w-16 h-16 mx-auto mb-4 bg-cream rounded-full flex items-center justify-center text-caramel">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" /></svg>
                        </div>
                        <p class="text-sm font-sans text-cocoa/70 mb-8">Klik tombol di bawah untuk melakukan pembayaran aman melalui Midtrans.</p>
                        <button id="pay-button" class="w-full bg-caramel hover:bg-cocoa text-white font-mono font-semibold py-4 rounded-full shadow-soft hover:shadow-float transition-all duration-300 active:scale-95">
                            Bayar Sekarang
                        </button>
                    </div>

                    @push('scripts')
                    <script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
                    <script type="text/javascript">
                        document.getElementById('pay-button').onclick = function(){
                            window.snap.pay('{{ $payment->snap_token }}', {
                                onSuccess: function(result){
                                    alert("Payment successful. Thank you!"); window.location.reload();
                                },
                                onPending: function(result){
                                    alert("Please complete the payment instructions."); window.location.reload();
                                },
                                onError: function(result){
                                    alert("Payment failed or encountered an error.");
                                },
                                onClose: function(){
                                    // Customer closed popup
                                }
                            });
                        };
                    </script>
                    @endpush
                @else
                    <div class="bg-white rounded-4xl border border-dough/30 p-8 shadow-soft space-y-6">
                        <div>
                            <h3 class="font-mono text-xs font-semibold text-cocoa uppercase tracking-widest border-b border-dough/30 pb-4">Instruksi Pembayaran</h3>
                            <p class="text-sm font-sans text-cocoa/50 mt-4">Silakan transfer sesuai total pesanan ke salah satu rekening berikut:</p>
                        </div>

                        <div class="space-y-3" x-data="{ openBank: null }">
                            <!-- BCA -->
                            <div class="bg-cream/50 rounded-2xl border border-dough/50 overflow-hidden">
                                <button @click="openBank = openBank === 'bca' ? null : 'bca'" class="w-full flex items-center justify-between px-5 py-3.5 text-left">
                                    <span class="font-mono font-bold text-cocoa text-sm">Bank BCA</span>
                                    <svg class="w-4 h-4 text-cocoa/40 transition-transform duration-200" :class="{'rotate-180': openBank === 'bca'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="openBank === 'bca'" class="px-5 pb-4 space-y-1">
                                    <span class="font-mono font-bold text-caramel text-lg block">5466026294</span>
                                    <span class="text-[11px] text-cocoa/60 font-sans">A.N Umar Bakery</span>
                                </div>
                            </div>

                            <!-- Mandiri -->
                            <div class="bg-cream/50 rounded-2xl border border-dough/50 overflow-hidden">
                                <button @click="openBank = openBank === 'mandiri' ? null : 'mandiri'" class="w-full flex items-center justify-between px-5 py-3.5 text-left">
                                    <span class="font-mono font-bold text-cocoa text-sm">Bank Mandiri</span>
                                    <svg class="w-4 h-4 text-cocoa/40 transition-transform duration-200" :class="{'rotate-180': openBank === 'mandiri'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="openBank === 'mandiri'" class="px-5 pb-4 space-y-1">
                                    <span class="font-mono font-bold text-caramel text-lg block">1380027797583</span>
                                    <span class="text-[11px] text-cocoa/60 font-sans">A.N Umar Bakery</span>
                                </div>
                            </div>

                            <!-- Seabank -->
                            <div class="bg-cream/50 rounded-2xl border border-dough/50 overflow-hidden">
                                <button @click="openBank = openBank === 'seabank' ? null : 'seabank'" class="w-full flex items-center justify-between px-5 py-3.5 text-left">
                                    <span class="font-mono font-bold text-cocoa text-sm">Seabank</span>
                                    <svg class="w-4 h-4 text-cocoa/40 transition-transform duration-200" :class="{'rotate-180': openBank === 'seabank'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="openBank === 'seabank'" class="px-5 pb-4 space-y-1">
                                    <span class="font-mono font-bold text-caramel text-lg block">901512077966</span>
                                    <span class="text-[11px] text-cocoa/60 font-sans">A.N Umar Bakery</span>
                                </div>
                            </div>

                            <!-- Gopay / QRIS (always visible) -->
                            <div class="bg-cream/50 rounded-2xl border border-dough/50 overflow-hidden">
                                <button @click="openBank = openBank === 'gopay' ? null : 'gopay'" class="w-full flex items-center justify-between px-5 py-3.5 text-left">
                                    <span class="font-mono font-bold text-cocoa text-sm">Gopay / QRIS</span>
                                    <svg class="w-4 h-4 text-cocoa/40 transition-transform duration-200" :class="{'rotate-180': openBank === 'gopay'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="openBank === 'gopay'" class="px-5 pb-4 space-y-3">
                                    <span class="font-mono font-bold text-caramel text-lg block">089525121811</span>
                                    <span class="text-[11px] text-cocoa/60 font-sans block">A.N Umar Bakery</span>
                                    <div class="flex justify-center bg-white p-3 rounded-xl border border-dough/30">
                                        <img src="/docs/qris.jpeg" alt="QRIS Umar Bakery" class="w-44 h-44 object-contain rounded-lg">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Proof Form -->
                        <div class="border-t border-dough/30 pt-6 space-y-5">
                            <span class="text-xs font-mono font-semibold text-cocoa uppercase tracking-widest block">Unggah Bukti Transfer</span>
                            
                            <form method="POST" action="{{ route('customer.orders.upload_proof', $order->id) }}" enctype="multipart/form-data" class="space-y-5">
                                @csrf
                                
                                <div class="relative border-2 border-dashed border-dough/50 hover:border-caramel rounded-3xl p-6 text-center cursor-pointer bg-cream/30 hover:bg-cream/80 transition-colors" x-data="{ fileName: '' }">
                                    <input type="file" name="payment_proof" required @change="fileName = $event.target.files[0].name" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                    <div class="space-y-2">
                                        <svg class="h-10 w-10 text-caramel/50 mx-auto" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z" />
                                        </svg>
                                        <p class="text-sm font-sans font-semibold text-cocoa" x-text="fileName || 'Klik untuk pilih file'"></p>
                                        <p class="text-xs font-mono text-cocoa/40">JPG, PNG, atau WEBP (Maks 2MB)</p>
                                    </div>
                                </div>
                                @error('payment_proof')
                                    <p class="text-strawberry text-xs font-mono font-semibold">{{ $message }}</p>
                                @enderror

                                <button type="submit" class="w-full bg-cocoa hover:bg-caramel text-white font-mono font-semibold py-4 rounded-full shadow-soft hover:shadow-float transition-all duration-300 active:scale-95 text-sm">
                                    Kirim Bukti Transfer
                                </button>
                            </form>
                            <form method="POST" action="{{ route('customer.orders.cancel', $order->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?');">
                                @csrf
                                <button type="submit" class="w-full mt-3 bg-cream hover:bg-strawberry/10 text-strawberry border border-strawberry/30 font-mono font-semibold py-4 rounded-full transition-all duration-300 active:scale-95 text-sm">
                                    Batalkan Pesanan
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            @endif

            <!-- Proof of Payment View if uploaded -->
            @if($order->payment_proof)
                <div class="bg-white rounded-4xl border border-dough/30 p-8 shadow-soft space-y-6">
                    <h3 class="font-mono text-xs font-semibold text-cocoa uppercase tracking-widest border-b border-dough/30 pb-4">Bukti Transfer</h3>
                    
                    <div class="rounded-3xl overflow-hidden border border-dough/50 aspect-video bg-cream flex items-center justify-center relative group">
                        <img src="{{ $order->payment_proof }}" alt="Bukti Transfer" class="w-full h-full object-cover">
                        <a href="{{ $order->payment_proof }}" target="_blank" class="absolute inset-0 bg-cocoa/60 backdrop-blur-sm flex items-center justify-center text-white text-sm font-mono font-semibold opacity-0 group-hover:opacity-100 transition-all duration-300">
                            Lihat Gambar Penuh
                        </a>
                    </div>
                    
                    <div class="bg-butter/10 p-4 border border-butter/20 rounded-2xl text-center text-sm font-sans text-cocoa/80 leading-relaxed">
                        Bukti sudah dikirim. Tim kami sedang memverifikasi pembayaran Anda.
                    </div>
                </div>
            @endif

        </div>

    </div>
</div>
@endsection
