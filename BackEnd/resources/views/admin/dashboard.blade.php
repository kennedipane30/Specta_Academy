@extends('layouts.spekta')
@section('title', 'Admin Dashboard - Spekta Academy')

@section('content')
<div class="p-6 w-full min-h-screen bg-[#FDFDFD]">

    <!-- TOP BAR: Greeting & Date -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
        <div class="animate__animated animate__fadeInLeft">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight flex items-center gap-3">
                <span class="w-2 h-10 bg-[#990000] rounded-full"></span>
                DASHBOARD <span class="text-[#990000]">ADMINISTRATOR</span>
            </h1>
            <p class="text-gray-500 font-medium ml-5 mt-1">Sistem Manajemen Terpadu Spekta Academy Indonesia</p>
        </div>

        <div class="bg-white px-6 py-3 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 animate__animated animate__fadeInRight">
            <div class="text-right border-r pr-4 border-gray-100">
                <div class="text-gray-900 font-black text-sm">{{ now()->translatedFormat('l, d F Y') }}</div>
                <div class="text-[10px] text-[#990000] font-bold uppercase tracking-widest">Waktu Server Aktif</div>
            </div>
            <div class="bg-red-50 p-2 rounded-xl">
                <i class="fas fa-calendar-alt text-[#990000]"></i>
            </div>
        </div>
    </div>

    <!-- STATS SECTION: 4 Main Pillars -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <!-- Total Students -->
        <div class="bg-white p-7 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
            <div class="flex justify-between items-start mb-4">
                <div class="bg-red-50 p-4 rounded-2xl group-hover:bg-[#990000] transition-colors duration-300">
                    <i class="fas fa-user-graduate text-[#990000] group-hover:text-white text-xl"></i>
                </div>
                <span class="text-[10px] font-black text-green-500 bg-green-50 px-2 py-1 rounded-lg">+ Live Data</span>
            </div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Students</p>
            <h2 class="text-4xl font-black text-gray-900">{{ number_format($total_siswa) }}</h2>
        </div>

        <!-- Active Teachers -->
        <div class="bg-white p-7 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
            <div class="flex justify-between items-start mb-4">
                <div class="bg-blue-50 p-4 rounded-2xl group-hover:bg-blue-600 transition-colors duration-300">
                    <i class="fas fa-chalkboard-teacher text-blue-600 group-hover:text-white text-xl"></i>
                </div>
                <span class="text-[10px] font-black text-blue-500 bg-blue-50 px-2 py-1 rounded-lg">Verified</span>
            </div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Teachers</p>
            <h2 class="text-4xl font-black text-gray-900">{{ number_format($total_pengajar) }}</h2>
        </div>

        <!-- Class Activations (Pending) -->
        <div class="bg-white p-7 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
            <div class="flex justify-between items-start mb-4">
                <div class="bg-yellow-50 p-4 rounded-2xl group-hover:bg-yellow-500 transition-colors duration-300">
                    <i class="fas fa-wallet text-yellow-600 group-hover:text-white text-xl"></i>
                </div>
                @if($pendaftaran_pending > 0)
                <span class="animate-pulse text-[10px] font-black text-white bg-red-600 px-2 py-1 rounded-lg">Action Needed</span>
                @endif
            </div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Class Activations</p>
            <h2 class="text-4xl font-black {{ $pendaftaran_pending > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ $pendaftaran_pending }}</h2>
        </div>

        <!-- Tutor Requests -->
        <div class="bg-white p-7 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
            <div class="flex justify-between items-start mb-4">
                <div class="bg-green-50 p-4 rounded-2xl group-hover:bg-green-600 transition-colors duration-300">
                    <i class="fas fa-headset text-green-600 group-hover:text-white text-xl"></i>
                </div>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Dedicated</span>
            </div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Tutor Requests</p>
            <h2 class="text-4xl font-black text-gray-900">{{ $tutor_pending }}</h2>
        </div>
    </div>

    <!-- MAIN GRID: Activities & Quick Access -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- RECENT ACTIVITY -->
        <div class="lg:col-span-2 bg-white p-10 rounded-[3rem] shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-8 pb-4 border-b">
                <h5 class="font-black text-gray-800 uppercase tracking-tight text-lg">LOG AKTIVITAS SISTEM</h5>
                <button class="text-[10px] font-black text-[#990000] uppercase hover:bg-red-50 px-4 py-2 rounded-full transition-all">Audit Trail &rarr;</button>
            </div>

            <div class="space-y-6">
                <div class="flex items-center justify-between p-4 hover:bg-gray-50 rounded-2xl transition-all border-l-4 border-transparent hover:border-[#990000]">
                    <div class="flex items-center gap-5">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-green-600 font-bold">BK</div>
                        <div>
                            <h6 class="text-sm font-black text-gray-800 mb-1 uppercase">Pendaftaran Siswa Baru</h6>
                            <p class="text-[11px] text-gray-400">Siswa mendaftar pada Kelas Reguler</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[9px] bg-green-50 text-green-600 px-3 py-1 rounded-full font-black uppercase">Success</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- QUICK ACCESS: Sidebar Action Panel -->
        <div class="space-y-6">
            <div class="bg-gray-900 p-10 rounded-[3rem] shadow-2xl relative overflow-hidden group min-h-[450px]">
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-[#990000]/20 rounded-full blur-3xl group-hover:bg-[#990000]/30 transition-all duration-500"></div>

                <h5 class="text-white font-black uppercase tracking-[0.2em] text-xs mb-10 relative z-10 opacity-60">Kontrol Cepat</h5>

                <div class="flex flex-col gap-5 relative z-10">
                    <a href="{{ route('admin.siswa.index') }}" class="group/link bg-white/5 hover:bg-white p-5 rounded-[1.5rem] flex items-center gap-5 transition-all duration-500 transform hover:scale-105">
                        <div class="bg-[#990000] p-3 rounded-xl group-hover/link:rotate-12 transition-transform shadow-lg shadow-red-900/50">
                            <i class="fas fa-users-cog text-white text-sm"></i>
                        </div>
                        <span class="text-white group-hover/link:text-gray-900 text-xs font-black uppercase tracking-wider">Student Management</span>
                    </a>

                    <a href="{{ route('admin.tutor.index') }}" class="group/link bg-white/5 hover:bg-white p-5 rounded-[1.5rem] flex items-center gap-5 transition-all duration-500 transform hover:scale-105">
                        <div class="bg-blue-600 p-3 rounded-xl group-hover/link:rotate-12 transition-transform shadow-lg shadow-blue-900/50">
                            <i class="fas fa-user-check text-white text-sm"></i>
                        </div>
                        <span class="text-white group-hover/link:text-gray-900 text-xs font-black uppercase tracking-wider">Tutor Requests</span>
                    </a>

                    <a href="{{ route('admin.promo.index') }}" class="group/link bg-white/5 hover:bg-white p-5 rounded-[1.5rem] flex items-center gap-5 transition-all duration-500 transform hover:scale-105">
                        <div class="bg-yellow-500 p-3 rounded-xl group-hover/link:rotate-12 transition-transform shadow-lg shadow-yellow-900/50">
                            <i class="fas fa-tags text-white text-sm"></i>
                        </div>
                        <span class="text-white group-hover/link:text-gray-900 text-xs font-black uppercase tracking-wider">Marketing Promo</span>
                    </a>

                    <!-- Class Content Management (FITUR BARU) -->
                    <a href="{{ route('admin.classes.index') }}" class="group/link bg-white/5 hover:bg-white p-5 rounded-[1.5rem] flex items-center gap-5 transition-all duration-500 transform hover:scale-105">
                        <div class="bg-indigo-600 p-3 rounded-xl group-hover/link:rotate-12 transition-transform shadow-lg shadow-indigo-900/50">
                            <i class="fas fa-edit text-white text-sm"></i>
                        </div>
                        <span class="text-white group-hover/link:text-gray-900 text-xs font-black uppercase tracking-wider">Class Content</span>
                    </a>
                </div>

                <div class="mt-12 pt-8 border-t border-white/10 relative z-10">
                    <p class="text-[10px] text-white/40 font-bold uppercase tracking-widest text-center italic">Spekta Academy High-Performance Mode</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;900&display=swap');
    body { font-family: 'Inter', sans-serif; }
</style>
@endsection