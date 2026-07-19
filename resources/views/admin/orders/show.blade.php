@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    
    <!-- Top Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.orders.index') }}" class="text-stone-400 hover:text-stone-600 transition">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Detail Pesanan {{ $order->order_number }}</h1>
            </div>
            <p class="text-xs text-stone-500 mt-1">Dibuat pada {{ $order->created_at->format('d M Y H:i') }} WIB oleh {{ $order->user->name }} ({{ $order->user->email }})</p>
        </div>

        <div class="flex items-center gap-3">
            @switch($order->status)
                @case('pending')
                    <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-amber-50 border border-amber-200 text-amber-800 uppercase tracking-wider">Menunggu Bayar</span>
                    @break
                @case('paid')
                    <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-blue-50 border border-blue-200 text-blue-800 uppercase tracking-wider">Sudah Bayar</span>
                    @break
                @case('processing')
                    <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-orange-50 border border-orange-200 text-orange-850 uppercase tracking-wider">Diproses</span>
                    @break
                @case('shipped')
                    <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-indigo-50 border border-indigo-200 text-indigo-850 uppercase tracking-wider">Dikirim</span>
                    @break
                @case('delivered')
                    <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-teal-50 border border-teal-200 text-teal-850 uppercase tracking-wider">Sampai</span>
                    @break
                @case('completed')
                    <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-emerald-50 border border-emerald-200 text-emerald-800 uppercase tracking-wider">Selesai</span>
                    @break
                @case('cancelled')
                    <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-rose-50 border border-rose-200 text-rose-800 uppercase tracking-wider">Batal</span>
                    @break
            @endswitch
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        
        <!-- Recipient & Items Detail -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Address details -->
            <div class="bg-white rounded-3xl border border-stone-200 p-6 shadow-sm">
                <h3 class="font-bold text-stone-900 text-base border-b border-stone-150 pb-3 mb-4">Informasi Pengiriman</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm leading-relaxed">
                    <div>
                        <span class="text-stone-400 block text-xs">Nama Penerima</span>
                        <span class="font-bold text-stone-850">{{ $order->recipient_name }}</span>
                    </div>
                    <div>
                        <span class="text-stone-400 block text-xs">WhatsApp / Telepon</span>
                        <span class="font-bold text-stone-850">{{ $order->phone }}</span>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="text-stone-400 block text-xs">Alamat Lengkap</span>
                        <span class="font-semibold text-stone-850">
                            {{ $order->address }}, {{ $order->city }}, {{ $order->postal_code }}
                        </span>
                    </div>
                    @if($order->notes)
                        <div class="sm:col-span-2 p-3.5 bg-amber-50/50 border border-amber-100 rounded-2xl text-xs text-amber-900">
                            <span class="font-bold block">Catatan Tambahan Customer:</span>
                            <span class="mt-1 inline-block">{{ $order->notes }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Items Table -->
            <div class="bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-stone-150">
                    <h3 class="font-bold text-stone-900 text-base">Produk yang Dibeli</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-stone-50 text-[10px] uppercase font-bold text-stone-500 tracking-wider">
                            <tr>
                                <th class="px-6 py-4">Roti</th>
                                <th class="px-6 py-4">Harga Beli</th>
                                <th class="px-6 py-4">Jumlah</th>
                                <th class="px-6 py-4 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @foreach($order->items as $item)
                                <tr class="hover:bg-stone-50/50 transition">
                                    <td class="px-6 py-4 font-bold text-stone-900">
                                        {{ $item->product_name }}
                                        <span class="block text-[9px] text-stone-400 font-bold uppercase mt-0.5">SKU: {{ $item->product_sku }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-stone-600">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 font-semibold text-stone-700">{{ $item->quantity }} Pcs</td>
                                    <td class="px-6 py-4 text-right font-bold text-amber-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Order Life-cycle control & Verification -->
        <div class="space-y-6">
            
            <!-- Summary Pay Box -->
            <div class="bg-white rounded-3xl border border-stone-200 p-6 shadow-sm space-y-4">
                <h3 class="font-bold text-stone-900 text-base border-b border-stone-150 pb-3">Ringkasan Pembayaran</h3>
                
                <div class="space-y-2.5 text-xs text-stone-600">
                    <div class="flex justify-between">
                        <span>Subtotal Roti</span>
                        <span class="font-semibold text-stone-850">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Ongkos Kirim</span>
                        <span class="font-semibold text-stone-850">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t border-stone-100 pt-3 flex justify-between font-extrabold text-amber-900 text-sm">
                        <span>Total Tagihan</span>
                        <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Payment Provider Info -->
            @php
                $payment = \App\Models\Payment::where('order_id', $order->id)->latest()->first();
            @endphp
            @if($payment)
                <div class="bg-white rounded-3xl border border-stone-200 p-6 shadow-sm space-y-4">
                    <h3 class="font-bold text-stone-900 text-base border-b border-stone-150 pb-3">Status Sistem Pembayaran</h3>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="text-stone-500">Provider</span>
                            <span class="font-bold text-stone-850 uppercase">{{ $payment->provider }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-stone-500">Status</span>
                            <span class="font-bold text-stone-850 uppercase">{{ $payment->status }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-stone-500">Transaction ID</span>
                            <span class="font-mono text-stone-850">{{ $payment->transaction_id ?? '-' }}</span>
                        </div>
                        @if($payment->paid_at)
                            <div class="flex justify-between">
                                <span class="text-stone-500">Dibayar Pada</span>
                                <span class="font-bold text-green-700">{{ $payment->paid_at->format('d M Y H:i:s') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Proof Image -->
            @if($order->payment_proof)
                <div class="bg-white rounded-3xl border border-stone-200 p-6 shadow-sm space-y-4">
                    <h3 class="font-bold text-stone-900 text-base border-b border-stone-150 pb-3">Bukti Pembayaran Customer</h3>
                    
                    <div class="rounded-2xl overflow-hidden border border-stone-200 aspect-video bg-stone-50 flex items-center justify-center relative group">
                        <img src="{{ $order->payment_proof }}" alt="Bukti Transfer" class="w-full h-full object-cover">
                        <a href="{{ $order->payment_proof }}" target="_blank" class="absolute inset-0 bg-black/40 flex items-center justify-center text-white text-xs opacity-0 group-hover:opacity-100 transition font-semibold">
                            Lihat Ukuran Asli
                        </a>
                    </div>
                </div>
            @else
                @if($order->status === 'pending')
                    <div class="bg-amber-50/50 p-4 border border-amber-100 rounded-3xl text-center text-xs text-amber-900">
                        Belum ada bukti pembayaran yang diunggah oleh customer.
                    </div>
                @endif
            @endif

            <!-- Admin Actions: Status transitions -->
            @if($order->status !== 'completed' && $order->status !== 'cancelled')
                <div class="bg-white rounded-3xl border border-stone-200 p-6 shadow-sm space-y-4">
                    <h3 class="font-bold text-stone-900 text-base border-b border-stone-150 pb-3">Ubah Status Pesanan</h3>
                    
                    <div class="flex flex-col gap-2.5">
                        @if($order->status === 'pending')
                            @if(!$payment || $payment->provider !== 'midtrans')
                                <form method="POST" action="{{ route('admin.orders.update_status', $order->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="paid">
                                    <button type="submit" class="w-full bg-amber-700 hover:bg-amber-800 text-white font-bold py-3 rounded-xl shadow text-xs transition">
                                        Konfirmasi Pembayaran (Tandai PAID)
                                    </button>
                                </form>
                            @else
                                <div class="bg-blue-50 border border-blue-100 p-4 rounded-xl text-xs text-blue-800 text-center">
                                    Pembayaran online (Midtrans). Status PAID akan diperbarui secara otomatis melalui Webhook.
                                </div>
                            @endif
                            
                            <form method="POST" action="{{ route('admin.orders.update_status', $order->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit" class="w-full bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-200 font-bold py-3 rounded-xl text-xs transition">
                                    Batalkan Pesanan (CANCELLED)
                                </button>
                            </form>
                        @elseif($order->status === 'paid')
                            <form method="POST" action="{{ route('admin.orders.update_status', $order->id) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="processing">
                                <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 rounded-xl shadow text-xs transition">
                                    Proses Pembuatan Roti (Tandai PROCESSING)
                                </button>
                            </form>
                        @elseif($order->status === 'processing')
                            <form method="POST" action="{{ route('admin.orders.update_status', $order->id) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="shipped">
                                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl shadow text-xs transition">
                                    Kirim Roti ke Kurir (Tandai SHIPPED)
                                </button>
                            </form>
                        @elseif($order->status === 'shipped')
                            <form method="POST" action="{{ route('admin.orders.update_status', $order->id) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="delivered">
                                <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 rounded-xl shadow text-xs transition">
                                    Tandai Roti Sampai (Tandai DELIVERED)
                                </button>
                            </form>
                        @elseif($order->status === 'delivered')
                            <form method="POST" action="{{ route('admin.orders.update_status', $order->id) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl shadow text-xs transition">
                                    Selesaikan Transaksi (Tandai COMPLETED)
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif

        </div>

    </div>
</div>
@endsection
