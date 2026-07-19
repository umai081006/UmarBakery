<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 text-center">
                    <h2 class="text-2xl font-bold text-yellow-600 mb-4">Selesaikan Pembayaran Anda</h2>
                    <p class="mb-6 text-gray-600">
                        Anda memiliki pesanan yang masih menunggu pembayaran (Order ID: <span class="font-semibold">{{ $activePendingOrder->order_number }}</span>).
                    </p>
                    
                    <div class="bg-gray-50 p-4 rounded-md inline-block text-left mb-6 w-full max-w-md">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Total:</span>
                            <span class="font-bold">Rp {{ number_format($activePendingOrder->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Batas Waktu:</span>
                            <span class="font-medium text-red-600">{{ $activePendingOrder->payment->expires_at->format('d M Y H:i') }}</span>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-center gap-4">
                        @if($activePendingOrder->payment->snap_redirect_url)
                            <a href="{{ $activePendingOrder->payment->snap_redirect_url }}" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition">
                                Lanjutkan Pembayaran
                            </a>
                        @endif
                        
                        <form action="{{ route('customer.orders.cancel', $activePendingOrder->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-red-100 text-red-600 px-6 py-2 rounded-md hover:bg-red-200 transition" onclick="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini? Stok akan dikembalikan dan Anda dapat membuat pesanan baru.')">
                                Batalkan Pesanan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
