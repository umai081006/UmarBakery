@extends('layouts.customer')

@section('content')
<div class="space-y-10" x-data="{
        showModal: false,
        isEdit: false,
        formAction: '{{ route('customer.addresses.store') }}',
        provinces: [],
        cities: [],
        districts: [],
        address: {
            id: '', label: 'Rumah', recipient_name: '{{ auth()->user()->name }}', phone: '{{ auth()->user()->phone }}',
            province: '', city: '', district: '', postal_code: '', address: '', detail_address: '', is_default: false
        },
        init() {
            this.fetchProvinces();
            this.$watch('address.province', (val) => { if(!this.isEdit) { this.address.city = ''; this.address.district = ''; } this.fetchCities(val); });
            this.$watch('address.city', (val) => { if(!this.isEdit) { this.address.district = ''; } this.fetchDistricts(this.address.province, val); });
            this.$watch('address.district', (val) => { 
                if(val && this.districts.length > 0) {
                    let d = this.districts.find(x => x.district === val);
                    if(d && d.postal_code && !this.isEdit) {
                        this.address.postal_code = d.postal_code;
                    }
                }
            });
        },
        async fetchProvinces() {
            let res = await fetch('/shipping/provinces');
            this.provinces = await res.json();
        },
        async fetchCities(province) {
            this.cities = [];
            if(!province) return;
            let res = await fetch('/shipping/cities?province=' + encodeURIComponent(province));
            this.cities = await res.json();
        },
        async fetchDistricts(province, city) {
            this.districts = [];
            if(!province || !city) return;
            let res = await fetch('/shipping/districts?province=' + encodeURIComponent(province) + '&city=' + encodeURIComponent(city));
            this.districts = await res.json();
        },
        openCreate() {
            this.isEdit = false;
            this.formAction = '{{ route('customer.addresses.store') }}';
            this.address = {
                id: '', label: 'Rumah', recipient_name: '{{ auth()->user()->name }}', phone: '{{ auth()->user()->phone }}',
                province: '', city: '', district: '', postal_code: '', address: '', detail_address: '', is_default: false
            };
            this.showModal = true;
        },
        openEdit(addr) {
            this.isEdit = true;
            this.formAction = '/customer/addresses/' + addr.id;
            this.address = { ...addr, is_default: addr.is_default == 1 };
            this.fetchCities(addr.province).then(() => {
                this.fetchDistricts(addr.province, addr.city).then(() => {
                    this.address.district = addr.district;
                    // Reset isEdit flag so watchers can autofill postal code on future changes
                    setTimeout(() => { this.isEdit = false; }, 500);
                });
            });
            this.showModal = true;
        }
    }">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-serif text-cocoa tracking-tight">Alamat Pengiriman</h1>
            <p class="text-sm text-cocoa/50 mt-2 font-sans">Kelola alamat pengiriman Anda untuk mempermudah checkout.</p>
        </div>
        <button @click="openCreate()" class="bg-cocoa hover:bg-caramel text-white px-6 py-3 rounded-full text-sm font-mono font-semibold shadow-soft hover:shadow-float transition-all duration-300">
            + Tambah Alamat
        </button>
    </div>

    @if($addresses->isEmpty())
        <div class="bg-white rounded-4xl p-16 text-center shadow-soft">
            <svg class="h-12 w-12 text-dough mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <h3 class="text-xl font-serif text-cocoa">Belum ada alamat</h3>
            <p class="text-sm font-sans text-cocoa/50 mt-2">Tambahkan alamat pertama Anda untuk mulai berbelanja.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($addresses as $addr)
                <div class="bg-white rounded-4xl border {{ $addr->is_default ? 'border-caramel/50 shadow-md ring-4 ring-cream' : 'border-dough/30 shadow-soft' }} p-8 relative transition-all duration-300 flex flex-col h-full hover:-translate-y-1">
                    @if($addr->is_default)
                        <span class="absolute top-6 right-6 bg-dough/40 text-cocoa text-[10px] font-mono font-bold px-3 py-1 rounded-md uppercase tracking-widest">Utama</span>
                    @endif
                    
                    <h3 class="font-serif text-xl text-cocoa flex items-center gap-2">
                        {{ $addr->label }}
                    </h3>
                    <div class="mt-4 space-y-1 text-sm font-sans text-cocoa/70 flex-1">
                        <p><span class="font-semibold text-cocoa">{{ $addr->recipient_name }}</span> <span class="font-mono text-xs ml-1 opacity-70">({{ $addr->phone }})</span></p>
                        <p class="leading-relaxed mt-2 text-stone-900 font-medium">{{ $addr->address }}</p>
                        @if($addr->detail_address)
                            <p class="text-xs italic text-stone-500">Patokan: {{ $addr->detail_address }}</p>
                        @endif
                        <p class="mt-1">{{ $addr->district ? $addr->district . ', ' : '' }}{{ $addr->city }}, {{ $addr->province }} {{ $addr->postal_code }}</p>
                    </div>

                    <div class="mt-6 flex items-center gap-4 border-t border-dough/30 pt-6">
                        <button @click="openEdit({{ $addr->toJson() }})" class="text-sm font-mono font-semibold text-caramel hover:text-cocoa transition-colors">Edit</button>
                        
                        <form action="{{ route('customer.addresses.destroy', $addr->id) }}" method="POST" onsubmit="return confirm('Hapus alamat ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-sm font-mono font-semibold text-strawberry/70 hover:text-strawberry transition-colors">Hapus</button>
                        </form>

                        @if(!$addr->is_default)
                            <form action="{{ route('customer.addresses.set_default', $addr->id) }}" method="POST" class="ml-auto">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs font-mono font-bold bg-cream text-cocoa/70 hover:text-cocoa hover:bg-dough/30 px-4 py-2 rounded-full transition-colors border border-dough/50">Jadikan Utama</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Modal Form -->
    <div x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="display: none;">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-cocoa/60 backdrop-blur-sm" @click="showModal = false" x-transition.opacity></div>
        
        <!-- Modal -->
        <div class="bg-white rounded-4xl w-full max-w-lg shadow-float overflow-hidden flex flex-col max-h-[90vh] relative z-10" 
             x-show="showModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
             
            <div class="px-8 py-6 border-b border-dough/30 flex justify-between items-center bg-cream/50 shrink-0">
                <h3 class="font-serif text-2xl text-cocoa" x-text="isEdit ? 'Edit Alamat' : 'Tambah Alamat'"></h3>
                <button type="button" @click="showModal = false" class="text-cocoa/30 hover:text-cocoa transition-colors">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <form :action="formAction" method="POST" class="flex flex-col overflow-y-auto custom-scrollbar">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>
                
                <div class="p-8 space-y-5">
                    <div>
                        <label class="block text-xs font-mono font-semibold text-cocoa uppercase tracking-widest mb-2">Label Alamat</label>
                        <input type="text" name="label" x-model="address.label" class="w-full rounded-2xl border border-dough/50 bg-cream/50 focus:bg-white focus:ring-2 focus:ring-caramel focus:border-caramel text-sm font-sans px-4 py-3 transition-colors" placeholder="Misal: Rumah, Kantor, Kosan" required>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-mono font-semibold text-cocoa uppercase tracking-widest mb-2">Nama Penerima</label>
                            <input type="text" name="recipient_name" x-model="address.recipient_name" class="w-full rounded-2xl border border-dough/50 bg-cream/50 focus:bg-white focus:ring-2 focus:ring-caramel focus:border-caramel text-sm font-sans px-4 py-3 transition-colors" required>
                        </div>
                        <div>
                            <label class="block text-xs font-mono font-semibold text-cocoa uppercase tracking-widest mb-2">Nomor HP</label>
                            <input type="text" name="phone" x-model="address.phone" class="w-full rounded-2xl border border-dough/50 bg-cream/50 focus:bg-white focus:ring-2 focus:ring-caramel focus:border-caramel text-sm font-sans px-4 py-3 transition-colors" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-mono font-semibold text-cocoa uppercase tracking-widest mb-2">Provinsi</label>
                            <select name="province" x-model="address.province" class="w-full rounded-2xl border border-dough/50 bg-cream/50 focus:bg-white focus:ring-2 focus:ring-caramel focus:border-caramel text-sm font-sans px-4 py-3 transition-colors" required>
                                <option value="">Pilih Provinsi...</option>
                                <template x-for="p in provinces" :key="p">
                                    <option :value="p" x-text="p"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-mono font-semibold text-cocoa uppercase tracking-widest mb-2">Kota/Kabupaten</label>
                            <select name="city" x-model="address.city" class="w-full rounded-2xl border border-dough/50 bg-cream/50 focus:bg-white focus:ring-2 focus:ring-caramel focus:border-caramel text-sm font-sans px-4 py-3 transition-colors" required :disabled="!address.province">
                                <option value="">Pilih Kota...</option>
                                <template x-for="c in cities" :key="c">
                                    <option :value="c" x-text="c"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-mono font-semibold text-cocoa uppercase tracking-widest mb-2">Kecamatan</label>
                            <select name="district" x-model="address.district" class="w-full rounded-2xl border border-dough/50 bg-cream/50 focus:bg-white focus:ring-2 focus:ring-caramel focus:border-caramel text-sm font-sans px-4 py-3 transition-colors" :disabled="!address.city">
                                <option value="">Pilih Kecamatan...</option>
                                <template x-for="d in districts" :key="d.district">
                                    <option :value="d.district" x-text="d.district"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-mono font-semibold text-cocoa uppercase tracking-widest mb-2">Kode Pos</label>
                            <input type="text" name="postal_code" x-model="address.postal_code" class="w-full rounded-2xl border border-dough/50 bg-cream/50 focus:bg-white focus:ring-2 focus:ring-caramel focus:border-caramel text-sm font-sans px-4 py-3 transition-colors">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-mono font-semibold text-cocoa uppercase tracking-widest mb-2">Alamat Lengkap (Jalan, No)</label>
                        <textarea name="address" x-model="address.address" rows="2" class="w-full rounded-2xl border border-dough/50 bg-cream/50 focus:bg-white focus:ring-2 focus:ring-caramel focus:border-caramel text-sm font-sans px-4 py-3 resize-none transition-colors" placeholder="Nama Jalan, Blok, Nomor Rumah..." required></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-mono font-semibold text-cocoa uppercase tracking-widest mb-2">Detail Patokan / RT RW (Opsional)</label>
                        <textarea name="detail_address" x-model="address.detail_address" rows="2" class="w-full rounded-2xl border border-dough/50 bg-cream/50 focus:bg-white focus:ring-2 focus:ring-caramel focus:border-caramel text-sm font-sans px-4 py-3 resize-none transition-colors" placeholder="Contoh: Pagar hitam, depan indomaret, RT 01 RW 02..."></textarea>
                    </div>

                    <div class="pt-2 flex items-center gap-3">
                        <div class="relative flex items-center justify-center">
                            <input type="checkbox" name="is_default" value="1" id="is_default" x-model="address.is_default" class="peer appearance-none w-5 h-5 border border-dough rounded-md checked:bg-caramel checked:border-caramel transition-colors cursor-pointer focus:ring-2 focus:ring-caramel focus:ring-offset-2">
                            <svg class="absolute w-3 h-3 text-white opacity-0 peer-checked:opacity-100 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <label for="is_default" class="text-sm font-sans text-cocoa/70 cursor-pointer select-none">Jadikan alamat utama</label>
                    </div>
                </div>

                <div class="p-6 border-t border-dough/30 bg-cream/50 mt-auto shrink-0 flex justify-end gap-4">
                    <button type="button" @click="showModal = false" class="px-6 py-3 rounded-full text-sm font-mono font-semibold text-cocoa/60 hover:text-cocoa hover:bg-dough/30 transition-colors">Cancel</button>
                    <button type="submit" class="px-8 py-3 rounded-full text-sm font-mono font-semibold bg-cocoa text-white hover:bg-caramel shadow-soft hover:shadow-float transition-all duration-300">Save Address</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
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
