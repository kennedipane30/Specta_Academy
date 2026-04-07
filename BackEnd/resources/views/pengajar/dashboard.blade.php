@extends('layouts.spekta')
@section('title', 'Dashboard Pengajar')

@section('content')
<div class="space-y-8">

    {{-- BANNER SELAMAT DATANG DENGAN GRADIENT --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-[#990000] to-[#cc0000] p-10 rounded-3xl text-white shadow-xl">
        <div class="relative z-10">
            <span class="bg-white/20 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest mb-4 inline-block backdrop-blur-md">
                Spekta Teacher Portal
            </span>
            <h1 class="text-4xl font-black tracking-tight">Selamat Datang, Bapak/Ibu Guru!</h1>
            <p class="text-red-100 mt-3 max-w-2xl font-medium opacity-90">
                Kelola materi pembelajaran, pantau kehadiran siswa, dan lihat perkembangan nilai tryout dalam satu platform terintegrasi.
            </p>
            <div class="mt-8 flex gap-4">
                <a href="{{ route('pengajar.materi.index') }}" class="bg-white text-[#990000] px-6 py-3 rounded-2xl font-black text-sm hover:bg-gray-100 transition shadow-lg flex items-center gap-2 uppercase tracking-tighter">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Upload Materi Baru
                </a>
            </div>
        </div>

        {{-- DEKORASI LINGKARAN --}}
        <div class="absolute -right-10 -top-10 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute right-20 bottom-0 w-32 h-32 bg-black/10 rounded-full blur-2xl"></div>
    </div>

    {{-- STATISTIK CEPAT & ACTIONS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- CARD ABSENSI --}}
        <div class="group bg-white p-8 rounded-3xl border-l-[12px] border-[#990000] shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300">
            <div class="flex justify-between items-start">
                <div>
                    <h4 class="text-gray-400 font-bold text-xs uppercase tracking-widest">Aktivitas Siswa</h4>
                    <p class="text-2xl font-black text-gray-800 mt-1 uppercase">Absensi Siswa</p>
                </div>
                <div class="bg-red-50 p-3 rounded-2xl group-hover:bg-[#990000] group-hover:text-white transition duration-300 text-[#990000]">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </div>
            </div>
            <div class="mt-8 flex items-center justify-between">
                <a href="/pengajar/absensi" class="text-[#990000] font-black text-xs underline uppercase tracking-tighter hover:text-red-800">Mulai Absen &rarr;</a>
                <span class="text-[10px] font-bold text-gray-400 bg-gray-50 px-3 py-1 rounded-full uppercase">Update Setiap Hari</span>
            </div>
        </div>

        {{-- CARD MATERI --}}
        <div class="group bg-white p-8 rounded-3xl border-l-[12px] border-orange-500 shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300">
            <div class="flex justify-between items-start">
                <div>
                    <h4 class="text-gray-400 font-bold text-xs uppercase tracking-widest">Media Pembelajaran</h4>
                    <p class="text-2xl font-black text-gray-800 mt-1 uppercase text-nowrap">Materi PDF</p>
                </div>
                <div class="bg-orange-50 p-3 rounded-2xl group-hover:bg-orange-500 group-hover:text-white transition duration-300 text-orange-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
            </div>
            <div class="mt-8 flex items-center justify-between">
                <p class="text-xs font-black text-gray-800 italic uppercase">12 Modul Terupload</p>
                <span class="text-[10px] font-bold text-orange-600 bg-orange-50 px-3 py-1 rounded-full uppercase tracking-tighter">Kelola Materi</span>
            </div>
        </div>

        {{-- CARD NILAI / TRYOUT --}}
        <div class="group bg-white p-8 rounded-3xl border-l-[12px] border-blue-600 shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300">
            <div class="flex justify-between items-start">
                <div>
                    <h4 class="text-gray-400 font-bold text-xs uppercase tracking-widest">Akademik Siswa</h4>
                    <p class="text-2xl font-black text-gray-800 mt-1 uppercase text-nowrap">Nilai Tryout</p>
                </div>
                <div class="bg-blue-50 p-3 rounded-2xl group-hover:bg-blue-600 group-hover:text-white transition duration-300 text-blue-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
            </div>
            <div class="mt-8 flex items-center justify-between">
                <a href="/pengajar/nilai" class="text-blue-600 font-black text-xs underline uppercase tracking-tighter">Lihat Ranking &rarr;</a>
                <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-full uppercase">Real-Time</span>
            </div>
        </div>

    </div>

    {{-- BAGIAN BAWAH: JADWAL MENGAJAR SINGKAT --}}
    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-black text-gray-800 uppercase tracking-tight">Jadwal Mengajar Anda Hari Ini</h3>
            <a href="/pengajar/jadwal-mengajar" class="text-[#990000] text-xs font-bold uppercase tracking-widest hover:underline italic">Lihat Selengkapnya &rarr;</a>
        </div>

        <div class="flex items-center p-5 bg-red-50 rounded-2xl border border-red-100">
            <div class="text-center pr-6 border-r border-red-200">
                <p class="text-xs font-bold text-gray-500 uppercase">Waktu</p>
                <h4 class="text-lg font-black text-[#990000]">08:00</h4>
            </div>
            <div class="pl-6 flex-1">
                <h5 class="text-md font-black text-gray-800 uppercase tracking-tight tracking-tight">BAHASA INGGRIS - KELAS CPNS</h5>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">Ruang Lab 01 • Sesi Pagi</p>
            </div>
            <div class="bg-white px-4 py-2 rounded-xl text-[#990000] font-black text-[10px] shadow-sm uppercase tracking-widest border border-red-100">
                SIAP MENGAJAR
            </div>
        </div>
    </div>
</div>
@endsection
