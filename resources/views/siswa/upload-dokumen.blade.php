<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Upload Dokumen Persyaratan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <p class="mb-2 text-gray-600">No. Pendaftaran: <strong>{{ $registration->no_pendaftaran }}</strong></p>
                    <p class="mb-6 text-sm text-gray-500">Format file: JPG, PNG, PDF. Maksimal 2MB per file.</p>

                    {{-- Status Upload --}}
                    <h3 class="text-lg font-bold text-blue-700 border-b pb-2 mb-4">Status Dokumen</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
                        @foreach($tipeDokumen as $key => $label)
                            @php
                                $doc = $registration->documents->firstWhere('tipe_dokumen', $key);
                            @endphp
                            <div class="p-4 border rounded-lg {{ $doc ? 'bg-green-50 border-green-300' : 'bg-gray-50 border-gray-200' }}">
                                <p class="font-semibold text-sm {{ $doc ? 'text-green-700' : 'text-gray-700' }}">{{ $label }}</p>
                                @if($doc)
                                    <p class="text-xs text-green-600 mt-1">✅ Sudah di-upload</p>
                                    <a href="{{ $doc->cloudinary_url }}" target="_blank" class="text-xs text-blue-600 hover:underline mt-1 inline-block">Lihat File</a>
                                @else
                                    <p class="text-xs text-gray-400 mt-1">❌ Belum di-upload</p>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Form Upload --}}
                    <h3 class="text-lg font-bold text-blue-700 border-b pb-2 mb-4">Upload Dokumen</h3>
                    <form action="{{ route('siswa.dokumen.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="tipe_dokumen" class="block text-sm font-medium text-gray-700 mb-1">Tipe Dokumen</label>
                                <select name="tipe_dokumen" id="tipe_dokumen" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                    <option value="">-- Pilih Tipe --</option>
                                    @foreach($tipeDokumen as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('tipe_dokumen') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="file" class="block text-sm font-medium text-gray-700 mb-1">Pilih File</label>
                                <input type="file" name="file" id="file" accept=".jpg,.jpeg,.png,.pdf" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm" required>
                                @error('file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="flex justify-between items-center">
                            <a href="{{ route('siswa.dashboard') }}" class="text-blue-600 hover:underline text-sm">&larr; Kembali ke Dashboard</a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow transition duration-200">
                                Upload Dokumen
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
