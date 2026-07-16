@extends('layouts.customer')

@section('title', 'Notifikasi Saya')

@section('content')
<div class="max-w-3xl mx-auto space-y-10">
    <div>
        <h1 class="text-3xl font-serif text-cocoa tracking-tight">Notifications</h1>
        <p class="text-sm font-sans text-cocoa/50 mt-2">Updates on your orders, payments, and account activity.</p>
    </div>

    <div class="bg-white rounded-4xl shadow-soft border border-dough/20 overflow-hidden">
        @if($notifications->isEmpty())
            <div class="p-16 text-center">
                <div class="w-20 h-20 bg-cream rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="h-10 w-10 text-dough" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <p class="text-cocoa font-serif text-xl">No notifications yet</p>
                <p class="text-cocoa/50 text-sm font-sans mt-2">We'll let you know when there's an update.</p>
            </div>
        @else
            <div class="divide-y divide-dough/30 font-sans">
                @foreach($notifications as $notif)
                    <div class="p-6 flex items-start gap-5 {{ is_null($notif->read_at) ? 'bg-cream/40' : '' }} hover:bg-cream transition-colors">
                        {{-- Icon by type --}}
                        <div class="shrink-0">
                            @if($notif->type === 'payment')
                                <div class="w-12 h-12 rounded-2xl bg-green-50 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                            @elseif($notif->type === 'order')
                                <div class="w-12 h-12 rounded-2xl bg-butter/20 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-caramel" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                </div>
                            @elseif($notif->type === 'promo')
                                <div class="w-12 h-12 rounded-2xl bg-purple-50 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                </div>
                            @else
                                <div class="w-12 h-12 rounded-2xl bg-dough/30 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-cocoa/50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3 mb-1">
                                <p class="text-base font-semibold text-cocoa">{{ $notif->title }}</p>
                                @if(is_null($notif->read_at))
                                    <span class="px-2 py-0.5 bg-caramel text-[10px] font-mono font-bold uppercase tracking-widest rounded-md text-white">New</span>
                                @endif
                            </div>
                            <p class="text-sm text-cocoa/70 leading-relaxed">{{ $notif->message }}</p>
                            <p class="text-xs font-mono text-cocoa/40 mt-2">{{ $notif->created_at->diffForHumans() }} &middot; {{ $notif->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
            
            {{-- Pagination --}}
            @if($notifications->hasPages())
                <div class="px-8 py-6 border-t border-dough/30 bg-cream/20">
                    {{ $notifications->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
