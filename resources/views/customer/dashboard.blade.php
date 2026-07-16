@extends('layouts.customer')

@section('content')
<div class="space-y-10">
    <div>
        <h1 class="text-3xl font-serif text-cocoa">Welcome back, {{ auth()->user()->name }}</h1>
        <p class="text-sm text-cocoa/50 mt-2 font-sans">Here's a summary of your account and recent orders.</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <!-- Card 1 -->
        <div class="bg-white p-6 rounded-4xl shadow-soft flex items-center gap-5">
            <div class="p-4 bg-dough/30 rounded-2xl text-caramel">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <div>
                <span class="text-xs font-mono font-semibold text-cocoa/40 uppercase tracking-widest block">Total Orders</span>
                <span class="text-3xl font-mono font-bold text-cocoa block">{{ $totalOrders }}</span>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white p-6 rounded-4xl shadow-soft flex items-center gap-5">
            <div class="p-4 bg-butter/20 rounded-2xl text-butter">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <span class="text-xs font-mono font-semibold text-cocoa/40 uppercase tracking-widest block">Awaiting Payment</span>
                <span class="text-3xl font-mono font-bold text-cocoa block">{{ $pendingOrders }}</span>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white p-6 rounded-4xl shadow-soft flex items-center gap-5">
            <div class="p-4 bg-green-50 rounded-2xl text-green-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <span class="text-xs font-mono font-semibold text-cocoa/40 uppercase tracking-widest block">Completed</span>
                <span class="text-3xl font-mono font-bold text-green-700 block">{{ $completedOrders }}</span>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-4xl shadow-soft overflow-hidden">
        <div class="px-8 py-6 border-b border-dough/30 flex justify-between items-center">
            <h3 class="font-serif text-xl text-cocoa">Recent Orders</h3>
            <a href="{{ route('customer.orders.index') }}" class="text-xs font-mono font-semibold text-caramel hover:text-cocoa transition-colors">View All →</a>
        </div>
        
        @if($recentOrders->isEmpty())
            <div class="p-12 text-center text-sm text-cocoa/50 font-sans">
                No orders yet. Start shopping to see them here!
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-cream text-[10px] uppercase font-mono font-bold text-cocoa/50 tracking-widest">
                        <tr>
                            <th class="px-8 py-4">Order #</th>
                            <th class="px-8 py-4">Recipient</th>
                            <th class="px-8 py-4">Total</th>
                            <th class="px-8 py-4">Status</th>
                            <th class="px-8 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-dough/20">
                        @foreach($recentOrders as $order)
                            <tr class="hover:bg-cream/50 transition-colors">
                                <td class="px-8 py-4 font-mono font-semibold text-cocoa">{{ $order->order_number }}</td>
                                <td class="px-8 py-4 text-cocoa/70 font-sans">{{ $order->recipient_name }}</td>
                                <td class="px-8 py-4 font-mono font-semibold text-caramel">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                <td class="px-8 py-4">
                                    @switch($order->status)
                                        @case('pending')
                                            <span class="px-3 py-1 text-[10px] font-mono font-bold rounded-full bg-butter/20 text-cocoa uppercase tracking-wider">Pending</span>
                                            @break
                                        @case('paid')
                                            <span class="px-3 py-1 text-[10px] font-mono font-bold rounded-full bg-blue-50 text-blue-700 uppercase tracking-wider">Paid</span>
                                            @break
                                        @case('processing')
                                            <span class="px-3 py-1 text-[10px] font-mono font-bold rounded-full bg-orange-50 text-orange-700 uppercase tracking-wider">Processing</span>
                                            @break
                                        @case('shipped')
                                            <span class="px-3 py-1 text-[10px] font-mono font-bold rounded-full bg-indigo-50 text-indigo-700 uppercase tracking-wider">Shipped</span>
                                            @break
                                        @case('delivered')
                                            <span class="px-3 py-1 text-[10px] font-mono font-bold rounded-full bg-teal-50 text-teal-700 uppercase tracking-wider">Delivered</span>
                                            @break
                                        @case('completed')
                                            <span class="px-3 py-1 text-[10px] font-mono font-bold rounded-full bg-green-50 text-green-700 uppercase tracking-wider">Completed</span>
                                            @break
                                        @case('cancelled')
                                            <span class="px-3 py-1 text-[10px] font-mono font-bold rounded-full bg-strawberry/10 text-strawberry uppercase tracking-wider">Cancelled</span>
                                            @break
                                    @endswitch
                                </td>
                                <td class="px-8 py-4 text-right">
                                    <a href="{{ route('customer.orders.show', $order->id) }}" class="text-xs font-mono font-semibold text-caramel hover:text-cocoa bg-cream px-4 py-2 rounded-full border border-dough/50 hover:border-cocoa transition-all">
                                        Details
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
