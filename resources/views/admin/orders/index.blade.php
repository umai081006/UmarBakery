@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    <div>
        <h1 class="text-3xl font-extrabold text-stone-900 tracking-tight">Daftar Pesanan Customer</h1>
        <p class="text-sm text-stone-500 mt-1">Pantau dan verifikasi pesanan, status pembayaran, dan pengiriman.</p>
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white p-6 rounded-3xl border border-stone-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
        <!-- Filter Form -->
        <form method="GET" action="{{ route('admin.orders.index') }}" class="w-full flex flex-col sm:flex-row gap-4 items-center">
            
            <!-- Search -->
            <div class="relative w-full sm:w-80">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. Pesanan / nama..." class="w-full pl-4 pr-10 py-2 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm">
                <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-600 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
            </div>

            <!-- Status filter -->
            <div class="w-full sm:w-48">
                <select name="status" onchange="this.form.submit()" class="w-full px-4 py-2 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Bayar</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Sudah Bayar</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Diproses</option>
                    <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Dikirim</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Sampai</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Batal</option>
                </select>
            </div>

            @if(request('search') || request('status'))
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-semibold text-rose-600 hover:text-rose-800 transition">Reset</a>
            @endif
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden">
        @if($orders->isEmpty())
            <div class="p-12 text-center text-sm text-stone-500">
                Belum ada data pesanan masuk.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-stone-50 text-[10px] uppercase font-bold text-stone-500 tracking-wider">
                        <tr>
                            <th class="px-6 py-4">No. Pesanan</th>
                            <th class="px-6 py-4">Pelanggan</th>
                            <th class="px-6 py-4">Tanggal Masuk</th>
                            <th class="px-6 py-4">Penerima</th>
                            <th class="px-6 py-4">Total</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach($orders as $order)
                            <tr class="hover:bg-stone-50/80 transition">
                                <td class="px-6 py-4 font-bold text-stone-900">{{ $order->order_number }}</td>
                                <td class="px-6 py-4 text-stone-600 text-xs">
                                    {{ $order->user->name }}
                                </td>
                                <td class="px-6 py-4 text-stone-500 text-xs">{{ $order->created_at->format('d M Y H:i') }}</td>
                                <td class="px-6 py-4 text-stone-600">{{ $order->recipient_name }}</td>
                                <td class="px-6 py-4 font-bold text-amber-900">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">
                                    @switch($order->status)
                                        @case('pending')
                                            <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-amber-50 border border-amber-200 text-amber-800 uppercase tracking-wider">Menunggu Bayar</span>
                                            @break
                                        @case('paid')
                                            <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-blue-50 border border-blue-200 text-blue-800 uppercase tracking-wider">Sudah Bayar</span>
                                            @break
                                        @case('processing')
                                            <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-orange-50 border border-orange-200 text-orange-850 uppercase tracking-wider">Diproses</span>
                                            @break
                                        @case('shipped')
                                            <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-indigo-50 border border-indigo-200 text-indigo-850 uppercase tracking-wider">Dikirim</span>
                                            @break
                                        @case('delivered')
                                            <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-teal-50 border border-teal-200 text-teal-850 uppercase tracking-wider">Sampai</span>
                                            @break
                                        @case('completed')
                                            <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-emerald-50 border border-emerald-200 text-emerald-800 uppercase tracking-wider">Selesai</span>
                                            @break
                                        @case('cancelled')
                                            <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-rose-50 border border-rose-200 text-rose-800 uppercase tracking-wider">Batal</span>
                                            @break
                                    @endswitch
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="text-xs font-semibold text-amber-700 hover:text-amber-900 bg-amber-50 px-3 py-1.5 rounded-lg border border-amber-100 hover:border-amber-200 transition font-sans">
                                        Periksa
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-6 border-t border-stone-100">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
