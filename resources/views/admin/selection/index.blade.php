<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Sistem Seleksi Otomatis') }}
            </h2>
            @if(!$isPublished)
                <form action="{{ route('admin.selection.run') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition duration-200">
                        &#9654; Jalankan Seleksi
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg flex items-start">
                    <svg class="h-5 w-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg flex items-start">
                    <svg class="h-5 w-5 text-red-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- ============================================ --}}
            {{-- STATE 1: Already Published                   --}}
            {{-- ============================================ --}}
            @if($isPublished)
                {{-- Published Success Banner --}}
                <div class="bg-gradient-to-r from-green-600 to-emerald-700 rounded-xl shadow-lg overflow-hidden">
                    <div class="p-8 text-center text-white">
                        <div class="text-5xl mb-4">✅</div>
                        <h2 class="text-3xl font-extrabold mb-2">Hasil Seleksi Telah Dipublikasikan</h2>
                        <p class="text-lg opacity-90 mb-2">Siswa sudah dapat melihat hasil seleksi di dashboard masing-masing.</p>
                        <div class="flex justify-center gap-8 mt-6">
                            <div class="bg-white/20 rounded-lg px-6 py-3">
                                <p class="text-sm opacity-80">Total Diterima</p>
                                <p class="text-3xl font-bold">{{ $totalDiterima }}</p>
                            </div>
                            <div class="bg-white/20 rounded-lg px-6 py-3">
                                <p class="text-sm opacity-80">Total Tidak Diterima</p>
                                <p class="text-3xl font-bold">{{ $totalTidakDiterima }}</p>
                            </div>
                        </div>
                        <div class="mt-8 space-x-4">
                            <a href="{{ route('admin.selection.export.all') }}" class="inline-block bg-white text-green-700 font-bold py-2 px-6 rounded-lg shadow hover:bg-green-50 transition duration-200">
                                📊 Export Semua Diterima (Excel)
                            </a>
                            <form action="{{ route('admin.selection.reset') }}" method="POST" class="inline" 
                                  onsubmit="return confirm('⚠️ PERHATIAN!\n\nAnda yakin ingin mereset hasil seleksi?\n\nSemua status akan dikembalikan ke \'verified\' dan siswa tidak dapat melihat hasilnya lagi.\n\nAksi ini tidak dapat dibatalkan.');">
                                @csrf
                                <button type="submit" class="bg-white/20 hover:bg-white/30 text-white font-semibold py-2 px-6 rounded-lg border border-white/40 transition duration-200">
                                    🔄 Reset Hasil Seleksi
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Published Results Tables --}}
                @if($publishedResults)
                    @foreach($publishedResults as $jalurId => $data)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="px-6 py-4 border-b bg-gray-50 flex justify-between items-center">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800">Jalur: {{ $data['jalur']->nama_jalur }}</h3>
                                    <p class="text-sm text-gray-500">Kuota: {{ $data['jalur']->kuota }}</p>
                                </div>
                                <div class="text-right flex items-center gap-4">
                                    <div>
                                        <span class="inline-block bg-green-100 text-green-800 text-xs px-3 py-1 rounded-full uppercase font-semibold tracking-wide">Diterima: {{ count($data['accepted']) }}</span>
                                        <span class="inline-block bg-red-100 text-red-800 text-xs px-3 py-1 rounded-full uppercase font-semibold tracking-wide ml-2">Tidak Diterima: {{ count($data['rejected']) }}</span>
                                    </div>
                                    <a href="{{ route('admin.selection.export.jalur', $data['jalur']->id) }}" class="inline-flex items-center text-sm font-semibold text-green-700 bg-green-100 hover:bg-green-200 py-1.5 px-3 rounded-lg transition duration-200">
                                        ⬇️ Export
                                    </a>
                                </div>
                            </div>
                            
                            <div class="p-6">
                                <h4 class="font-bold text-green-700 mb-2 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    Siswa Diterima
                                </h4>
                                <div class="overflow-x-auto mb-6">
                                    <table class="min-w-full divide-y divide-gray-200 border">
                                        <thead class="bg-green-50">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Peringkat</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama / NIK</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nilai Rata-rata</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kecamatan</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @forelse($data['accepted'] as $index => $reg)
                                                <tr>
                                                    <td class="px-4 py-2 text-sm">{{ $index + 1 }}</td>
                                                    <td class="px-4 py-2 text-sm font-medium">{{ $reg->student->user->name ?? '-' }} <br><span class="text-xs text-gray-500">{{ $reg->student->nik }}</span></td>
                                                    <td class="px-4 py-2 text-sm">{{ $reg->nilai_rata_rata }}</td>
                                                    <td class="px-4 py-2 text-sm">{{ $reg->student->kecamatan ?? '-' }}</td>
                                                    <td class="px-4 py-2 text-sm"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">✓ Diterima</span></td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="5" class="px-4 py-2 text-center text-sm text-gray-500">Tidak ada siswa diterima.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <h4 class="font-bold text-red-700 mb-2 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                    Siswa Tidak Diterima
                                </h4>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 border opacity-75">
                                        <thead class="bg-red-50">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama / NIK</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nilai Rata-rata</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kecamatan</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @forelse($data['rejected'] as $index => $reg)
                                                <tr>
                                                    <td class="px-4 py-2 text-sm">{{ $index + 1 }}</td>
                                                    <td class="px-4 py-2 text-sm">{{ $reg->student->user->name ?? '-' }} <br><span class="text-xs text-gray-500">{{ $reg->student->nik }}</span></td>
                                                    <td class="px-4 py-2 text-sm">{{ $reg->nilai_rata_rata }}</td>
                                                    <td class="px-4 py-2 text-sm">{{ $reg->student->kecamatan ?? '-' }}</td>
                                                    <td class="px-4 py-2 text-sm"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">✗ Tidak Diterima</span></td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="5" class="px-4 py-2 text-center text-sm text-gray-500">Tidak ada.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

            {{-- ============================================ --}}
            {{-- STATE 2: Preview Available (Not Yet Published) --}}
            {{-- ============================================ --}}
            @elseif($previewData)
                {{-- Info Box --}}
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 shadow-sm rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Aturan Seleksi</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    <li><strong>Reguler:</strong> Diambil berdasarkan nilai rata-rata tertinggi hingga batas kuota.</li>
                                    <li><strong>Prestasi:</strong> Wajib memiliki dokumen "Piagam Prestasi" yang disetujui, lalu diurutkan dari nilai tertinggi.</li>
                                    <li><strong>Zonasi:</strong> Siswa yang memiliki kesamaan kecamatan dengan sekolah, diurutkan dari nilai tertinggi.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Preview Warning + Publish Button --}}
                <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border-2 border-yellow-400 p-6 rounded-xl shadow-sm">
                    <div class="flex items-start">
                        <div class="text-3xl mr-4">⚠️</div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-yellow-800 mb-2">Preview Hasil Seleksi</h3>
                            <p class="text-yellow-700 text-sm mb-4">Ini adalah <strong>preview</strong> dari hasil seleksi. Siswa <strong>belum dapat melihat</strong> hasil ini. Setelah dipublikasikan, status pendaftaran akan berubah menjadi <strong>"Diterima"</strong> atau <strong>"Tidak Diterima"</strong> dan siswa dapat melihat hasilnya di dashboard.</p>
                            <form action="{{ route('admin.selection.publish') }}" method="POST" class="inline" 
                                  onsubmit="return confirm('🚀 Publikasikan Hasil Seleksi?\n\nAksi ini akan:\n• Mengubah status pendaftar menjadi DITERIMA atau TIDAK DITERIMA\n• Siswa akan langsung dapat melihat hasilnya\n\nAnda yakin ingin melanjutkan?');">
                                @csrf
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg transition duration-200 transform hover:scale-105 inline-flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Publikasikan Hasil
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Preview Tables --}}
                @foreach($previewData as $jalurId => $data)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b bg-gray-50 flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800">Jalur: {{ $data['jalur']->nama_jalur }}</h3>
                                <p class="text-sm text-gray-500">Kuota: {{ $data['jalur']->kuota }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-block bg-green-100 text-green-800 text-xs px-2 rounded-full uppercase font-semibold tracking-wide">Diterima: {{ count($data['accepted']) }}</span>
                                <span class="inline-block bg-red-100 text-red-800 text-xs px-2 rounded-full uppercase font-semibold tracking-wide ml-2">Ditolak: {{ count($data['rejected']) }}</span>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <h4 class="font-bold text-green-700 mb-2">Calon Siswa Diterima</h4>
                            <div class="overflow-x-auto mb-6">
                                <table class="min-w-full divide-y divide-gray-200 border">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Peringkat</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama / NIK</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nilai Rata-rata</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kecamatan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @forelse($data['accepted'] as $index => $reg)
                                            <tr>
                                                <td class="px-4 py-2 text-sm">{{ $index + 1 }}</td>
                                                <td class="px-4 py-2 text-sm font-medium">{{ $reg->student->user->name ?? '-' }} <br><span class="text-xs text-gray-500">{{ $reg->student->nik }}</span></td>
                                                <td class="px-4 py-2 text-sm">{{ $reg->nilai_rata_rata }}</td>
                                                <td class="px-4 py-2 text-sm">{{ $reg->student->kecamatan ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="px-4 py-2 text-center text-sm text-gray-500">Tidak ada calon siswa diterima (Belum ada yang terverifikasi / memenuhi syarat).</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <h4 class="font-bold text-red-700 mb-2">Calon Siswa Tidak Diterima</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 border opacity-75">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Peringkat</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama / NIK</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nilai Rata-rata</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kecamatan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @forelse($data['rejected'] as $index => $reg)
                                            <tr>
                                                <td class="px-4 py-2 text-sm">{{ count($data['accepted']) + $index + 1 }}</td>
                                                <td class="px-4 py-2 text-sm">{{ $reg->student->user->name ?? '-' }} <br><span class="text-xs text-gray-500">{{ $reg->student->nik }}</span></td>
                                                <td class="px-4 py-2 text-sm">{{ $reg->nilai_rata_rata }}</td>
                                                <td class="px-4 py-2 text-sm">{{ $reg->student->kecamatan ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="px-4 py-2 text-center text-sm text-gray-500">Tidak ada.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach

            {{-- ============================================ --}}
            {{-- STATE 3: No Preview, No Published Data       --}}
            {{-- ============================================ --}}
            @else
                {{-- Info Box --}}
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 shadow-sm rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Aturan Seleksi</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    <li><strong>Reguler:</strong> Diambil berdasarkan nilai rata-rata tertinggi hingga batas kuota.</li>
                                    <li><strong>Prestasi:</strong> Wajib memiliki dokumen "Piagam Prestasi" yang disetujui, lalu diurutkan dari nilai tertinggi.</li>
                                    <li><strong>Zonasi:</strong> Siswa yang memiliki kesamaan kecamatan dengan sekolah, diurutkan dari nilai tertinggi.</li>
                                </ul>
                                <p class="mt-2">Klik <strong>Jalankan Seleksi</strong> untuk membuat hasil preview (Data tidak akan dipublikasikan sampai Anda mengkonfirmasinya).</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8 text-center">
                    <div class="text-5xl mb-4">📋</div>
                    <p class="text-gray-500 text-lg">Belum ada data preview seleksi.</p>
                    <p class="text-gray-400 text-sm mt-2">Silakan klik tombol <strong>"Jalankan Seleksi"</strong> di atas untuk membuat preview.</p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
