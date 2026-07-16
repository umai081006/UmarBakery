<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem PPDB Online</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans text-gray-800">

    <nav class="bg-blue-600 p-4 text-white shadow">
        <div class="container mx-auto flex justify-between items-center">
            <a href="{{ url('/') }}" class="text-xl font-bold">PPDB Online</a>
            <div class="space-x-4">
                <a href="{{ url('/') }}" class="hover:underline">Beranda</a>
                <a href="#" class="hover:underline">Pendaftaran</a>
                <a href="#" class="hover:underline">Pengumuman</a>
                @auth
                    <a href="{{ url('/dashboard') }}" class="bg-white text-blue-600 px-4 py-2 rounded shadow hover:bg-gray-100">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="hover:underline">Login</a>
                    <a href="{{ route('register') }}" class="bg-white text-blue-600 px-4 py-2 rounded shadow hover:bg-gray-100">Daftar</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="container mx-auto mt-8 p-4">
        <div class="bg-white p-8 rounded-lg shadow-md text-center">
            <h1 class="text-3xl font-bold mb-4">Selamat Datang di Sistem PPDB Online</h1>
            <p class="text-gray-600 mb-8">Penerimaan Peserta Didik Baru Tahun Ajaran {{ date('Y') }}/{{ date('Y')+1 }}</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-6 border rounded shadow-sm bg-blue-50">
                    <h2 class="text-xl font-semibold mb-2 text-blue-700">1. Pendaftaran</h2>
                    <p class="text-sm text-gray-700">Isi formulir pendaftaran secara online dengan melengkapi data diri dan berkas persyaratan.</p>
                </div>
                <div class="p-6 border rounded shadow-sm bg-yellow-50">
                    <h2 class="text-xl font-semibold mb-2 text-yellow-700">2. Seleksi</h2>
                    <p class="text-sm text-gray-700">Proses seleksi berkas dan nilai akan dilakukan oleh panitia PPDB sekolah.</p>
                </div>
                <div class="p-6 border rounded shadow-sm bg-green-50">
                    <h2 class="text-xl font-semibold mb-2 text-green-700">3. Pengumuman</h2>
                    <p class="text-sm text-gray-700">Lihat hasil seleksi secara online di website ini pada tanggal yang telah ditentukan.</p>
                </div>
            </div>

            <div class="mt-8">
                @auth
                    <a href="{{ url('/dashboard') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow hover:bg-blue-700 text-lg font-semibold transition">Buka Dashboard</a>
                @else
                    <a href="{{ route('register') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow hover:bg-blue-700 text-lg font-semibold transition">Mulai Daftar Sekarang</a>
                @endauth
            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-white text-center p-4 mt-8">
        <p>&copy; {{ date('Y') }} PPDB Online. Project Umar Ramadhan.</p>
    </footer>

</body>
</html>
