@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    <div>
        <h1 class="text-3xl font-extrabold text-stone-900 tracking-tight">Dashboard Admin</h1>
        <p class="text-sm text-stone-500 mt-1">Ringkasan penjualan dan operasional Umar Bakery hari ini.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Stat 1 -->
        <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm flex items-center gap-4">
            <div class="p-3.5 bg-amber-50 rounded-xl text-amber-700">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <span class="text-xs font-semibold text-stone-400 uppercase tracking-wider block">Penjualan Hari Ini</span>
                <span class="text-xl font-extrabold text-amber-900 block">Rp {{ number_format($totalSalesToday, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Stat 2 -->
        <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm flex items-center gap-4">
            <div class="p-3.5 bg-amber-50 rounded-xl text-amber-700">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <div>
                <span class="text-xs font-semibold text-stone-400 uppercase tracking-wider block">Pesanan Hari Ini</span>
                <span class="text-xl font-extrabold text-stone-900 block">{{ $ordersTodayCount }} Pesanan</span>
            </div>
        </div>

        <!-- Stat 3 -->
        <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm flex items-center gap-4">
            <div class="p-3.5 bg-rose-50 rounded-xl text-rose-700">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <span class="text-xs font-semibold text-stone-400 uppercase tracking-wider block">Belum Diverifikasi</span>
                <span class="text-xl font-extrabold text-rose-800 block">{{ $pendingOrdersCount }} Pesanan</span>
            </div>
        </div>

        <!-- Stat 4 -->
        <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm flex items-center gap-4">
            <div class="p-3.5 bg-emerald-50 rounded-xl text-emerald-700">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div>
                <span class="text-xs font-semibold text-stone-400 uppercase tracking-wider block">Pelanggan Aktif</span>
                <span class="text-xl font-extrabold text-emerald-800 block">{{ $totalCustomers }} Akun</span>
            </div>
        </div>
    </div>

    <!-- Recent Orders Grid -->
    <div class="bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-stone-150 flex justify-between items-center">
            <h3 class="font-bold text-stone-900 text-lg">Pesanan Masuk Terbaru</h3>
            <a href="{{ route('admin.orders.index') }}" class="text-xs font-semibold text-amber-700 hover:text-amber-900 transition">Semua Pesanan</a>
        </div>
        
        @if($recentOrders->isEmpty())
            <div class="p-8 text-center text-sm text-stone-500">
                Belum ada pesanan masuk.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-stone-50 text-[10px] uppercase font-bold text-stone-500 tracking-wider">
                        <tr>
                            <th class="px-6 py-4">No. Pesanan</th>
                            <th class="px-6 py-4">Pelanggan</th>
                            <th class="px-6 py-4">Penerima</th>
                            <th class="px-6 py-4">Total</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach($recentOrders as $order)
                            <tr class="hover:bg-stone-50/80 transition">
                                <td class="px-6 py-4 font-bold text-stone-900">{{ $order->order_number }}</td>
                                <td class="px-6 py-4 text-stone-600 text-xs">
                                    {{ $order->user->name }}
                                </td>
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
        @endif
    </div>
</div>
@endsection
