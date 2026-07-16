<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Pengumuman') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form action="{{ route('admin.announcements.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="judul" class="block text-sm font-medium text-gray-700">Judul Pengumuman</label>
                            <input type="text" name="judul" id="judul" value="{{ old('judul') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required autofocus>
                            @error('judul') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="isi" class="block text-sm font-medium text-gray-700">Isi Pengumuman</label>
                            <textarea name="isi" id="isi" rows="6" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>{{ old('isi') }}</textarea>
                            @error('isi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-6">
                            <div class="flex items-center">
                                <input id="is_published" name="is_published" type="checkbox" value="1" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" {{ old('is_published') ? 'checked' : '' }}>
                                <label for="is_published" class="ml-2 block text-sm text-gray-900">Publish (Tampilkan ke pendaftar)</label>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('admin.announcements.index') }}" class="text-gray-600 hover:text-gray-900 text-sm">Batal</a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm transition">
                                Simpan Pengumuman
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
