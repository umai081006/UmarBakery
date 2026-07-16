@extends('layouts.customer')

@section('content')
<div class="space-y-10">
    <div>
        <h1 class="text-3xl font-serif text-cocoa tracking-tight">My Orders</h1>
        <p class="text-sm font-sans text-cocoa/50 mt-2">A complete list of your artisan bakery orders.</p>
    </div>

    <div class="bg-white rounded-4xl shadow-soft overflow-hidden">
        @if($orders->isEmpty())
            <div class="p-16 text-center">
                <div class="w-20 h-20 bg-dough/30 rounded-full flex items-center justify-center text-caramel mx-auto mb-6">
                    <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
                <h3 class="font-serif text-xl text-cocoa">No orders yet</h3>
                <p class="text-cocoa/50 text-sm mt-2 font-sans">Start exploring our premium artisan bakes.</p>
                <a href="{{ route('products.index') }}" class="mt-8 inline-flex items-center gap-2 font-mono text-sm font-semibold bg-caramel text-white px-8 py-4 rounded-full hover:bg-cocoa shadow-soft hover:shadow-float transition-all duration-300">
                    Start Shopping
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-cream text-[10px] uppercase font-mono font-bold text-cocoa/50 tracking-widest">
                        <tr>
                            <th class="px-8 py-5">Order #</th>
                            <th class="px-8 py-5">Date</th>
                            <th class="px-8 py-5">Recipient</th>
                            <th class="px-8 py-5">Total</th>
                            <th class="px-8 py-5">Status</th>
                            <th class="px-8 py-5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-dough/20 font-sans">
                        @foreach($orders as $order)
                            <tr class="hover:bg-cream/50 transition-colors">
                                <td class="px-8 py-5 font-mono font-semibold text-cocoa">{{ $order->order_number }}</td>
                                <td class="px-8 py-5 text-cocoa/50 text-xs font-mono">{{ $order->created_at->format('d M Y H:i') }}</td>
                                <td class="px-8 py-5 text-cocoa/70">{{ $order->recipient_name }}</td>
                                <td class="px-8 py-5 font-mono font-bold text-caramel">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                <td class="px-8 py-5">
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
                                <td class="px-8 py-5 text-right">
                                    <a href="{{ route('customer.orders.show', $order->id) }}" class="text-xs font-mono font-semibold text-caramel hover:text-cocoa bg-cream px-4 py-2 rounded-full border border-dough/50 hover:border-cocoa transition-all">
                                        Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="p-8 border-t border-dough/30 bg-cream/20">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
