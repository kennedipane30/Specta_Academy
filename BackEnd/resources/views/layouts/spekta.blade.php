<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spekta Academy - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-spekta { background-color: #990000; }
        .text-spekta { color: #990000; }
        .border-spekta { border-color: #990000; }
        .rotate-180 { transform: rotate(180deg); }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen overflow-hidden">
        <div class="w-64 bg-spekta text-white flex-shrink-0 shadow-lg">
            <div class="p-6 border-b border-red-800 flex flex-col items-center">
                <h1 class="text-2xl font-bold tracking-widest">SPEKTA</h1>
                <p class="text-xs italic">ACADEMY</p>
            </div>
            <nav class="mt-4 overflow-y-auto h-full pb-20">
                @if(Auth::user()->role_id == 1) <!-- MENU ADMIN -->
                    <a href="{{ route('admin.dashboard') }}" class="block py-3 px-6 hover:bg-red-800">🏠 Dashboard</a>
                    <a href="{{ route('admin.jadwal.index') }}" class="block py-3 px-6 hover:bg-red-800">📅 Jadwal Kelas</a>
                    <div class="relative">
                        <button onclick="toggleSiswaDropdown()" class="w-full flex justify-between items-center py-3 px-6 hover:bg-red-800 transition focus:outline-none">
                            <span class="flex items-center">👥 Siswa</span>
                            <svg id="siswa-arrow" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div id="siswa-menu" class="hidden bg-red-950/30">
                            <a href="{{ route('admin.siswa.index') }}" class="block py-2 pl-12 pr-6 text-sm hover:text-yellow-400">○ Semua Siswa</a>
                            <a href="{{ route('admin.siswa.pendaftaran') }}" class="block py-2 pl-12 pr-6 text-sm hover:text-yellow-400 flex justify-between items-center">
                                <span>○ Tambah Kelas</span>
                                @php $pendingCount = \App\Models\Enrollment::where('status', 'pending')->count(); @endphp
                                @if($pendingCount > 0) <span class="bg-yellow-500 text-white text-[10px] px-2 py-0.5 rounded-full font-bold">{{ $pendingCount }}</span> @endif
                            </a>
                        </div>
                    </div>
                    <a href="{{ route('admin.promo') }}" class="block py-3 px-6 hover:bg-red-800">🎁 Kode Promo</a>
                    <a href="{{ route('admin.manajemen-pengajar.index') }}" class="block py-3 px-6 hover:bg-red-800">👨‍🏫 Manajemen Pengajar</a>
                    <a href="{{ route('admin.galeri.index') }}" class="block py-3 px-6 hover:bg-red-800">🖼️ Galeri & Info</a>

                @elseif(Auth::user()->role_id == 2) <!-- MENU PENGAJAR -->
                    <a href="{{ route('pengajar.dashboard') }}" class="block py-3 px-6 hover:bg-red-800">🏠 Dashboard</a>
                    <a href="{{ route('pengajar.jadwal.index') }}" class="block py-3 px-6 hover:bg-red-800">📅 Jadwal Mengajar</a>

                    <!-- DROPDOWN ABSENSI -->
                    <div class="relative">
                        <button onclick="togglePengajarDropdown()" class="w-full flex justify-between items-center py-3 px-6 hover:bg-red-800 transition focus:outline-none">
                            <span class="flex items-center">📝 Absensi Siswa</span>
                            <svg id="pengajar-arrow" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div id="pengajar-menu" class="hidden bg-red-950/30">
                            @foreach(\App\Models\ClassModel::all() as $c)
                                <a href="{{ route('pengajar.absensi.show', $c->class_modelsID) }}" class="block py-2 pl-12 pr-6 text-[11px] hover:text-yellow-400">○ {{ $c->nama_program }}</a>
                            @endforeach
                        </div>
                    </div>

                    <a href="{{ route('pengajar.materi.index') }}" class="block py-3 px-6 hover:bg-red-800">📚 Upload Materi</a>

                    <!-- DROPDOWN INPUT SOAL TO (MODIFIKASI BARU) -->
                    <div class="relative">
                        <button onclick="toggleTryoutDropdown()" class="w-full flex justify-between items-center py-3 px-6 hover:bg-red-800 transition focus:outline-none">
                            <span class="flex items-center">⏱️ Input Soal TO</span>
                            <svg id="tryout-arrow" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div id="tryout-menu" class="hidden bg-red-950/30">
                            @foreach(\App\Models\ClassModel::all() as $c)
                                <a href="{{ route('pengajar.tryout.create', ['class_id' => $c->class_modelsID]) }}" class="block py-2 pl-12 pr-6 text-[11px] hover:text-yellow-400">○ {{ $c->nama_program }}</a>
                            @endforeach
                        </div>
                    </div>

                    <a href="{{ route('pengajar.tryout.nilai') }}" class="block py-3 px-6 hover:bg-red-800">📊 Lihat Nilai</a>
                @endif
            </nav>
        </div>

        <div class="flex-1 flex flex-col">
            <header class="bg-white shadow px-8 py-4 flex justify-between items-center">
                <h2 class="font-bold text-gray-700 uppercase">@yield('title')</h2>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-sm font-bold">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-spekta">{{ Auth::user()->role->name }}</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="bg-spekta text-white px-4 py-2 rounded-lg text-xs font-bold shadow-md hover:bg-red-700">LOGOUT</button>
                    </form>
                </div>
            </header>
            <main class="p-8 overflow-y-auto pb-24">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        function toggleSiswaDropdown() {
            document.getElementById('siswa-menu').classList.toggle('hidden');
            document.getElementById('siswa-arrow').classList.toggle('rotate-180');
        }
        function togglePengajarDropdown() {
            document.getElementById('pengajar-menu').classList.toggle('hidden');
            document.getElementById('pengajar-arrow').classList.toggle('rotate-180');
        }
        // FUNGSI BARU UNTUK TRYOUT
        function toggleTryoutDropdown() {
            document.getElementById('tryout-menu').classList.toggle('hidden');
            document.getElementById('tryout-arrow').classList.toggle('rotate-180');
        }

        window.onload = function() {
            if (window.location.href.includes('admin/siswa')) {
                document.getElementById('siswa-menu').classList.remove('hidden');
                document.getElementById('siswa-arrow').classList.add('rotate-180');
            }
            if (window.location.href.includes('pengajar/absensi')) {
                document.getElementById('pengajar-menu').classList.remove('hidden');
                document.getElementById('pengajar-arrow').classList.add('rotate-180');
            }
            // AUTO OPEN JIKA DI HALAMAN TRYOUT
            if (window.location.href.includes('pengajar/tryout')) {
                document.getElementById('tryout-menu').classList.remove('hidden');
                document.getElementById('tryout-arrow').classList.add('rotate-180');
            }
        }
    </script>
</body>
</html>
