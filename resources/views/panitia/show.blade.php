<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Pendaftar') }} - {{ $registration->no_pendaftaran }}
            </h2>
            <a href="{{ route('panitia.dashboard') }}" class="text-sm text-blue-600 hover:underline">&larr; Kembali</a>
        </div>
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

            @if ($errors->any())
                <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 border-b">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Data Diri Siswa</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500 mb-1">Nama Lengkap</p>
                            <p class="font-semibold">{{ $registration->student->user->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 mb-1">NIK</p>
                            <p class="font-semibold">{{ $registration->student->nik }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 mb-1">Tempat, Tanggal Lahir</p>
                            <p class="font-semibold">{{ $registration->student->tempat_lahir }}, {{ \Carbon\Carbon::parse($registration->student->tanggal_lahir)->format('d F Y') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 mb-1">Asal Sekolah</p>
                            <p class="font-semibold">{{ $registration->student->asal_sekolah }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 mb-1">Jalur Pendaftaran</p>
                            <p class="font-semibold">{{ $registration->jalurPendaftaran->nama_jalur ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 mb-1">Status Pendaftaran</p>
                            <p class="font-semibold uppercase {{ $registration->status === 'verified' ? 'text-green-600' : ($registration->status === 'rejected' ? 'text-red-600' : 'text-yellow-600') }}">
                                {{ $registration->status === 'verified' ? 'TERVERIFIKASI' : ($registration->status === 'rejected' ? 'DITOLAK' : 'PENDING') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Verifikasi Dokumen</h3>

                    @php
                        $requiredDocs = ['foto_3x4' => 'Foto 3x4', 'kartu_keluarga' => 'Kartu Keluarga', 'ijazah' => 'Ijazah', 'rapor' => 'Rapor', 'piagam_prestasi' => 'Piagam Prestasi'];
                    @endphp

                    <div class="space-y-6">
                        @foreach($requiredDocs as $key => $label)
                            @php
                                $doc = $registration->documents->firstWhere('tipe_dokumen', $key);
                            @endphp
                            <div class="p-4 border rounded-lg {{ $doc ? ($doc->status === 'approved' ? 'border-green-300 bg-green-50' : ($doc->status === 'rejected' ? 'border-red-300 bg-red-50' : 'border-yellow-300 bg-yellow-50')) : 'border-gray-300 bg-gray-50' }}">
                                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                    <div class="flex-1">
                                        <h4 class="font-bold text-gray-800">{{ $label }}</h4>
                                        @if(!$doc)
                                            <p class="text-sm text-gray-500">Belum di-upload</p>
                                        @else
                                            <p class="text-sm mt-1">Status: 
                                                <span class="font-semibold {{ $doc->status === 'approved' ? 'text-green-600' : ($doc->status === 'rejected' ? 'text-red-600' : 'text-yellow-600') }}">
                                                    {{ strtoupper($doc->status) }}
                                                </span>
                                            </p>
                                            @if($doc->catatan)
                                                <p class="text-sm text-red-600 mt-1">Catatan: {{ $doc->catatan }}</p>
                                            @endif
                                            <a href="{{ $doc->cloudinary_url }}" target="_blank" class="mt-2 inline-flex items-center text-sm text-blue-600 hover:text-blue-800 hover:underline">
                                                &#128065; Preview Dokumen
                                            </a>
                                        @endif
                                    </div>
                                    
                                    @if($doc)
                                    <div class="w-full md:w-auto mt-4 md:mt-0">
                                        <form action="{{ route('panitia.document.verify', $doc->id) }}" method="POST" class="flex flex-col gap-2">
                                            @csrf
                                            <select name="status" class="border-gray-300 rounded text-sm w-full md:w-48" onchange="toggleCatatan(this, 'catatan-{{ $doc->id }}')" required>
                                                <option value="pending" {{ $doc->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="approved" {{ $doc->status === 'approved' ? 'selected' : '' }}>Setujui</option>
                                                <option value="rejected" {{ $doc->status === 'rejected' ? 'selected' : '' }}>Tolak</option>
                                            </select>
                                            
                                            <div id="catatan-{{ $doc->id }}" class="{{ $doc->status === 'rejected' ? 'block' : 'hidden' }}">
                                                <textarea name="catatan" class="border-gray-300 rounded text-sm w-full md:w-48 placeholder-gray-400" placeholder="Catatan penolakan..." rows="2">{{ $doc->catatan }}</textarea>
                                            </div>
                                            
                                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-1 px-4 rounded text-sm">Simpan</button>
                                        </form>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleCatatan(selectElement, catatanId) {
            const catatanDiv = document.getElementById(catatanId);
            const textarea = catatanDiv.querySelector('textarea');
            if (selectElement.value === 'rejected') {
                catatanDiv.classList.remove('hidden');
                textarea.required = true;
            } else {
                catatanDiv.classList.add('hidden');
                textarea.required = false;
            }
        }
    </script>
</x-app-layout>
