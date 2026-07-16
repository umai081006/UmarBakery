@php
    $unreadCount = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0;
@endphp

{{-- Notification Bell (Alpine.js Dropdown) --}}
<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" @click.outside="open = false"
            id="notification-bell-btn"
            class="relative flex items-center justify-center w-9 h-9 rounded-xl bg-stone-100 hover:bg-amber-50 text-stone-500 hover:text-amber-700 transition">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        @if($unreadCount > 0)
            <span id="notif-badge" class="absolute -top-1 -right-1 h-4 w-4 flex items-center justify-center rounded-full bg-rose-500 text-[9px] font-bold text-white leading-none">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown Panel --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-1"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-1"
         class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-stone-100 z-50"
         style="display: none;">

        <div class="p-4 border-b border-stone-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-stone-800">Notifikasi</h3>
            @if($unreadCount > 0)
                <form action="{{ route('customer.notifications.mark_all_read') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-[11px] font-semibold text-amber-700 hover:text-amber-900 transition">
                        Tandai semua dibaca
                    </button>
                </form>
            @endif
        </div>

        <div class="max-h-80 overflow-y-auto divide-y divide-stone-50">
            @php
                $recentNotifs = auth()->user()->notifications()->take(5)->get();
            @endphp

            @forelse($recentNotifs as $notif)
                <div class="px-4 py-3 flex items-start gap-3 {{ is_null($notif->read_at) ? 'bg-amber-50/50' : '' }} hover:bg-stone-50 transition">
                    <div class="mt-0.5 shrink-0">
                        @if($notif->type === 'payment')
                            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center">
                                <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        @elseif($notif->type === 'order')
                            <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center">
                                <svg class="h-4 w-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            </div>
                        @else
                            <div class="w-8 h-8 rounded-full bg-stone-100 flex items-center justify-center">
                                <svg class="h-4 w-4 text-stone-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-stone-800 truncate">{{ $notif->title }}</p>
                        <p class="text-[11px] text-stone-500 mt-0.5 leading-snug">{{ Str::limit($notif->message, 60) }}</p>
                        <p class="text-[10px] text-stone-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                    </div>
                    @if(is_null($notif->read_at))
                        <div class="w-1.5 h-1.5 rounded-full bg-amber-500 mt-1.5 shrink-0"></div>
                    @endif
                </div>
            @empty
                <div class="p-6 text-center text-sm text-stone-400">
                    Tidak ada notifikasi
                </div>
            @endforelse
        </div>

        <div class="p-3 border-t border-stone-100 text-center">
            <a href="{{ route('customer.notifications.index') }}" class="text-xs font-semibold text-amber-700 hover:text-amber-900 transition">
                Lihat Semua Notifikasi →
            </a>
        </div>
    </div>
</div>
