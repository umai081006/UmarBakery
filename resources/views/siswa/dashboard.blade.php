<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Hasil Seleksi Banner (Prioritas utama - tampil paling atas jika sudah dipublikasikan) --}}
            @if($registration && in_array($registration->status, ['diterima', 'tidak_diterima']))
                <div class="rounded-xl shadow-lg overflow-hidden {{ $registration->status == 'diterima' ? 'bg-gradient-to-r from-green-600 to-emerald-700' : 'bg-gradient-to-r from-red-600 to-rose-700' }}">
                    <div class="p-8 text-center text-white">
                        @if($registration->status == 'diterima')
                            <div class="text-6xl mb-4">🎉</div>
                            <h2 class="text-3xl font-extrabold mb-3">Selamat! Anda Diterima</h2>
                            <p class="text-lg opacity-90">Anda dinyatakan <strong>DITERIMA</strong> melalui jalur <strong>{{ $registration->jalurPendaftaran->nama_jalur ?? '-' }}</strong>.</p>
                            <div class="mt-6 bg-white/20 inline-block rounded-lg px-6 py-3">
                                <p class="text-sm opacity-80">Nomor Pendaftaran</p>
                                <p class="text-xl font-bold">{{ $registration->no_pendaftaran }}</p>
                            </div>
                            <p class="text-sm mt-6 opacity-75">Silakan pantau pengumuman dari sekolah mengenai langkah selanjutnya (daftar ulang, dll).</p>
                        @else
                            <div class="text-6xl mb-4">😔</div>
                            <h2 class="text-3xl font-extrabold mb-3">Mohon Maaf</h2>
                            <p class="text-lg opacity-90">Anda dinyatakan <strong>TIDAK DITERIMA</strong> pada jalur <strong>{{ $registration->jalurPendaftaran->nama_jalur ?? '-' }}</strong>.</p>
                            <div class="mt-6 bg-white/20 inline-block rounded-lg px-6 py-3">
                                <p class="text-sm opacity-80">Nomor Pendaftaran</p>
                                <p class="text-xl font-bold">{{ $registration->no_pendaftaran }}</p>
                            </div>
                            <p class="text-sm mt-6 opacity-75">Jangan berkecil hati, tetap semangat dan pantau informasi jalur pendaftaran lainnya.</p>
                        @endif
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Halo, {{ Auth::user()->name }}!</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
                        
                        <!-- Nomor Pendaftaran -->
                        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg shadow-sm">
                            <p class="text-sm text-blue-600 font-semibold mb-1">Nomor Pendaftaran</p>
                            <p class="text-xl font-bold text-gray-800">
                                {{ $registration ? $registration->no_pendaftaran : 'Belum Mendaftar' }}
                            </p>
                        </div>

                        <!-- Status Pendaftaran -->
                        <div class="p-4 rounded-lg shadow-sm border
                            @if($registration)
                                @if($registration->status == 'diterima')
                                    bg-green-50 border-green-300
                                @elseif($registration->status == 'tidak_diterima')
                                    bg-red-50 border-red-300
                                @elseif($registration->status == 'verified')
                                    bg-emerald-50 border-emerald-200
                                @else
                                    bg-yellow-50 border-yellow-200
                                @endif
                            @else
                                bg-gray-50 border-gray-200
                            @endif
                        ">
                            <p class="text-sm font-semibold mb-1
                                @if($registration)
                                    @if($registration->status == 'diterima') text-green-600
                                    @elseif($registration->status == 'tidak_diterima') text-red-600
                                    @elseif($registration->status == 'verified') text-emerald-600
                                    @else text-yellow-600
                                    @endif
                                @else text-gray-600
                                @endif
                            ">Status Pendaftaran</p>
                            <p class="text-xl font-bold 
                                @if($registration)
                                    @if($registration->status == 'diterima') text-green-700
                                    @elseif($registration->status == 'tidak_diterima') text-red-700
                                    @elseif($registration->status == 'verified') text-emerald-700
                                    @else text-yellow-700
                                    @endif
                                @else text-gray-700
                                @endif
                            ">
                                @if($registration)
                                    @if($registration->status == 'pending')
                                        Menunggu Verifikasi
                                    @elseif($registration->status == 'verified')
                                        Terverifikasi
                                    @elseif($registration->status == 'diterima')
                                        ✓ Diterima
                                    @elseif($registration->status == 'tidak_diterima')
                                        ✗ Tidak Diterima
                                    @else
                                        {{ $registration->status }}
                                    @endif
                                @else
                                    Belum Mendaftar
                                @endif
                            </p>
                        </div>

                        <!-- Status Dokumen -->
                        <div class="p-4 bg-indigo-50 border border-indigo-200 rounded-lg shadow-sm">
                            <p class="text-sm text-indigo-600 font-semibold mb-1">Status Dokumen</p>
                            <p class="text-xl font-bold text-gray-800">
                                @if($registration)
                                    {{ $registration->documents->count() > 0 ? 'Sudah Upload' : 'Belum Upload' }}
                                @else
                                    Belum Mendaftar
                                @endif
                            </p>
                        </div>

                        <!-- Jalur Pendaftaran -->
                        <div class="p-4 bg-purple-50 border border-purple-200 rounded-lg shadow-sm">
                            <p class="text-sm text-purple-600 font-semibold mb-1">Jalur Pendaftaran</p>
                            <p class="text-xl font-bold text-gray-800">
                                @if($registration && $registration->jalurPendaftaran)
                                    {{ $registration->jalurPendaftaran->nama_jalur }}
                                @else
                                    Belum Mendaftar
                                @endif
                            </p>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Detail Hasil Seleksi --}}
            @if($registration && in_array($registration->status, ['diterima', 'tidak_diterima']))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2 {{ $registration->status == 'diterima' ? 'text-green-600' : 'text-red-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                @if($registration->status == 'diterima')
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                @else
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                @endif
                            </svg>
                            Detail Hasil Seleksi
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="p-4 rounded-lg {{ $registration->status == 'diterima' ? 'bg-green-50' : 'bg-red-50' }}">
                                <p class="text-sm {{ $registration->status == 'diterima' ? 'text-green-600' : 'text-red-600' }} font-semibold">Hasil</p>
                                <p class="text-2xl font-bold {{ $registration->status == 'diterima' ? 'text-green-700' : 'text-red-700' }}">
                                    {{ $registration->status == 'diterima' ? 'DITERIMA' : 'TIDAK DITERIMA' }}
                                </p>
                            </div>
                            <div class="p-4 rounded-lg bg-gray-50">
                                <p class="text-sm text-gray-600 font-semibold">Jalur Pendaftaran</p>
                                <p class="text-2xl font-bold text-gray-800">{{ $registration->jalurPendaftaran->nama_jalur ?? '-' }}</p>
                            </div>
                            <div class="p-4 rounded-lg bg-gray-50">
                                <p class="text-sm text-gray-600 font-semibold">Nilai Rata-Rata</p>
                                <p class="text-2xl font-bold text-gray-800">{{ $registration->nilai_rata_rata ?? '-' }}</p>
                            </div>
                            <div class="p-4 rounded-lg bg-gray-50">
                                <p class="text-sm text-gray-600 font-semibold">Nomor Pendaftaran</p>
                                <p class="text-2xl font-bold text-gray-800">{{ $registration->no_pendaftaran }}</p>
                            </div>
                        </div>
                        @if($registration->status == 'diterima')
                            <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                                <p class="text-sm text-green-700">
                                    <strong>📌 Langkah Selanjutnya:</strong> Silakan pantau pengumuman resmi dari sekolah mengenai jadwal daftar ulang dan persyaratan tambahan.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if(!$registration)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-8 text-center">
                        <h4 class="text-lg font-semibold text-gray-700 mb-2">Anda belum melakukan pendaftaran PPDB.</h4>
                        <p class="text-gray-500 mb-4">Silakan lengkapi biodata dan daftar melalui jalur yang tersedia.</p>
                        <a href="{{ route('siswa.pendaftaran.create') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow transition duration-200">
                            Mulai Pendaftaran
                        </a>
                    </div>
                </div>
            @endif

        </div>
    </div>

</x-app-layout>
