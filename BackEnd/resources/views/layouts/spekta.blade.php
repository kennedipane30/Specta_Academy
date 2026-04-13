<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spekta Academy - @yield('title')</title>

    <!-- Google Fonts: Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { font-family: 'Montserrat', sans-serif; }
        .bg-spekta { background: linear-gradient(180deg, #990000 0%, #700000 100%); }
        .text-spekta { color: #990000; }

        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #990000; border-radius: 10px; }

        .nav-link { transition: all 0.3s ease; position: relative; overflow: hidden; }
        .nav-link:hover { padding-left: 1.75rem; background: rgba(255, 255, 255, 0.1); }
        .nav-link.active { background: rgba(255, 255, 255, 0.15); border-right: 4px solid #fbbf24; }

        .dropdown-item { transition: all 0.2s; }
        .dropdown-item:hover { color: #fbbf24; transform: translateX(5px); }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">
    <div class="flex h-screen overflow-hidden">

        <!-- SIDEBAR -->
        <div class="w-72 bg-spekta text-white flex-shrink-0 shadow-2xl flex flex-col">
            <div class="p-8 flex flex-col items-center border-b border-white/10">
                <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mb-3 shadow-lg">
                    <span class="text-spekta font-black text-3xl">S</span>
                </div>
                <h1 class="text-xl font-extrabold tracking-[0.2em] leading-none uppercase">Spekta</h1>
                <p class="text-[10px] font-medium tracking-[0.4em] opacity-60 uppercase mt-1">Academy</p>
            </div>

            <nav class="flex-1 mt-6 overflow-y-auto px-4 space-y-1 pb-10">
                <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest px-4 mb-2">Main Menu</p>

                @if(Auth::user()->role_id == 1) <!-- MENU ADMIN -->
                    <a href="{{ route('admin.dashboard') }}" class="nav-link flex items-center py-3 px-4 rounded-xl mb-1">
                        <span class="mr-3 text-lg">🏠</span> Dashboard
                    </a>

                     <div class="relative">
                        <button onclick="toggleSiswaDropdown()" class="nav-link w-full flex justify-between items-center py-3 px-4 rounded-xl focus:outline-none">
                            <span class="flex items-center"><span class="mr-3 text-lg">👥</span> Siswa</span>
                            <svg id="siswa-arrow" class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div id="siswa-menu" class="hidden mt-1 ml-4 border-l border-white/20 space-y-1">
                            <a href="{{ route('admin.siswa.index') }}" class="dropdown-item block py-2 pl-8 text-sm opacity-80">Semua Siswa</a>
                            <a href="{{ route('admin.siswa.pendaftaran') }}" class="dropdown-item block py-2 pl-8 text-sm opacity-80 flex justify-between items-center pr-4">
                                <span>Tambah Kelas</span>
                                @php $pendingCount = \App\Models\Enrollment::where('status', 'pending')->count(); @endphp
                                @if($pendingCount > 0) <span class="bg-yellow-500 text-black text-[9px] px-2 py-0.5 rounded-full font-bold shadow-sm animate-pulse">{{ $pendingCount }}</span> @endif
                            </a>
                        </div>
                    </div>
                    <a href="{{ route('admin.jadwal.index') }}" class="nav-link flex items-center py-3 px-4 rounded-xl mb-1">
                        <span class="mr-3 text-lg">📅</span> Jadwal Kelas
                    </a>

                    <a href="{{ route('admin.tutor.index') }}" class="nav-link flex items-center py-3 px-4 rounded-xl mb-1">
                        <span class="mr-3 text-lg">🤝</span> Konfirmasi Tutor
                    </a>

                    <a href="{{ route('admin.promo.index') }}" class="nav-link flex items-center py-3 px-4 rounded-xl mb-1">
                        <span class="mr-3 text-lg">🎁</span> Kode Promo
                    </a>
                    <a href="{{ route('admin.manajemen-pengajar.index') }}" class="nav-link flex items-center py-3 px-4 rounded-xl mb-1">
                        <span class="mr-3 text-lg">👨‍🏫</span> Pengajar
                    </a>

                    <a href="{{ route('admin.announcement.index') }}" class="nav-link flex items-center py-3 px-4 rounded-xl mb-1">
                        <span class="mr-3 text-lg">📢</span> Pengumuman
                    </a>
                    <a href="{{ route('admin.galeri.index') }}" class="nav-link flex items-center py-3 px-4 rounded-xl mb-1">
                        <span class="mr-3 text-lg">🖼️</span> Galeri & Info
                    </a>
                    <a href="{{ route('admin.alumni.index') }}" class="nav-link flex items-center py-3 px-4 rounded-xl mb-1">
                        <span class="mr-3 text-lg">🎓</span> Manajemen Alumni
                    </a>

                @elseif(Auth::user()->role_id == 2) <!-- MENU PENGAJAR -->
                    <a href="{{ route('pengajar.dashboard') }}" class="nav-link flex items-center py-3 px-4 rounded-xl mb-1">
                        <span class="mr-3 text-lg">🏠</span> Dashboard
                    </a>

                    {{-- MODIFIKASI: DROPDOWN DIHAPUS, LANGSUNG KE INDEX ABSENSI --}}
                    <a href="{{ route('pengajar.absensi.index') }}" class="nav-link flex items-center py-3 px-4 rounded-xl mb-1">
                        <span class="mr-3 text-lg">📝</span> Absensi Siswa
                    </a>

                    <a href="{{ route('pengajar.jadwal.index') }}" class="nav-link flex items-center py-3 px-4 rounded-xl mb-1">
                        <span class="mr-3 text-lg">📅</span> Jadwal Mengajar
                    </a>

                    <a href="{{ route('pengajar.tutor.index') }}" class="nav-link flex items-center py-3 px-4 rounded-xl mb-1">
                        <span class="mr-3 text-lg">🗓️</span> Jadwal Tutor
                    </a>

                    <a href="{{ route('pengajar.materi.index') }}" class="nav-link flex items-center py-3 px-4 rounded-xl mb-1">
                        <span class="mr-3 text-lg">📚</span> Upload Materi
                    </a>

                    <a href="{{ route('pengajar.latihan.index') }}" class="nav-link flex items-center py-3 px-4 rounded-xl mb-1">
                        <span class="mr-3 text-lg">📖</span> Latihan Soal
                    </a>

                    <a href="{{ route('pengajar.tryout.index') }}" class="nav-link flex items-center py-3 px-4 rounded-xl mb-1">
                        <span class="mr-3 text-lg">⏱️</span> Input Soal TO
                    </a>

                    <a href="{{ route('pengajar.tryout.nilai') }}" class="nav-link flex items-center py-3 px-4 rounded-xl mb-1">
                        <span class="mr-3 text-lg">📊</span> Lihat Nilai
                    </a>
                @endif
            </nav>

            <div class="p-6 text-center text-[10px] opacity-40 font-bold uppercase tracking-widest border-t border-white/10">
                Spekta Academy &copy; {{ date('Y') }}
            </div>
        </div>

        <div class="flex-1 flex flex-col bg-gray-50">
            <header class="bg-white shadow-sm px-10 py-5 flex justify-between items-center border-b border-gray-100">
                <h2 class="font-extrabold text-gray-800 uppercase tracking-widest text-sm">@yield('title')</h2>

                <div class="flex items-center space-x-6">
                    <div class="flex items-center space-x-3 pr-6 border-r border-gray-200 text-right">
                        <div>
                            <p class="text-sm font-black text-gray-800">{{ Auth::user()->name }}</p>
                            <span class="text-[9px] font-bold text-spekta bg-red-50 px-2 py-0.5 rounded-full uppercase tracking-tighter">{{ Auth::user()->role->name }}</span>
                        </div>
                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center font-bold text-gray-500">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    </div>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="group flex items-center gap-2 bg-[#990000] text-white px-5 py-2.5 rounded-2xl text-[10px] font-black shadow-lg shadow-red-200 hover:bg-red-800 transition transform active:scale-95">
                            LOGOUT
                            <svg class="w-3 h-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </button>
                    </form>
                </div>
            </header>

            <main class="p-10 overflow-y-auto pb-32">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script>
        function toggleSiswaDropdown() {
            document.getElementById('siswa-menu').classList.toggle('hidden');
            document.getElementById('siswa-arrow').classList.toggle('rotate-180');
        }

        window.onload = function() {
            const currentUrl = window.location.href;
            const navLinks = document.querySelectorAll('.nav-link');

            navLinks.forEach(link => {
                if (currentUrl.includes(link.getAttribute('href'))) {
                    link.classList.add('active');
                }
            });

            if (currentUrl.includes('admin/siswa')) toggleSiswaDropdown();
        }
    </script>
</body>
</html>
