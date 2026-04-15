@extends('layouts.spekta')
@section('title', 'Admin Dashboard')

@section('content')
{{-- Pastikan pembungkus utamanya menggunakan padding agar tidak menempel ke sidebar --}}
<div class="p-4 w-full">

    <!-- Welcome Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-800 tracking-tight">HELLO, <span class="text-[#990000]">ADMINISTRATOR!</span></h1>
            <p class="text-gray-500 text-sm font-medium">Welcome back to Spekta Academy Control Panel.</p>
        </div>
        <div class="text-right hidden md:block">
            <div class="text-gray-800 font-black text-lg">{{ now()->translatedFormat('d F Y') }}</div>
            <span class="bg-gray-100 text-gray-400 text-[10px] px-3 py-1 rounded-full font-bold uppercase tracking-widest">Dashboard System v2.0</span>
        </div>
    </div>

    <!-- Stats Grid (Menggunakan Grid Tailwind agar presisi) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">

        <!-- Total Students -->
        <div class="bg-white p-6 rounded-[2rem] shadow-xl shadow-gray-100 border-l-8 border-[#990000] flex justify-between items-center transition-transform hover:scale-105 duration-300">
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Students</p>
                <h2 class="text-3xl font-black text-gray-800">{{ $total_siswa }}</h2>
            </div>
            <div class="text-red-100">
                <i class="fas fa-user-graduate fa-2x"></i>
            </div>
        </div>

        <!-- Teachers -->
        <div class="bg-white p-6 rounded-[2rem] shadow-xl shadow-gray-100 border-l-8 border-blue-600 flex justify-between items-center transition-transform hover:scale-105 duration-300">
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Teachers</p>
                <h2 class="text-3xl font-black text-gray-800">{{ $total_pengajar }}</h2>
            </div>
            <div class="text-blue-100">
                <i class="fas fa-chalkboard-teacher fa-2x"></i>
            </div>
        </div>

        <!-- Class Activation -->
        <div class="bg-white p-6 rounded-[2rem] shadow-xl shadow-gray-100 border-l-8 border-yellow-500 flex justify-between items-center transition-transform hover:scale-105 duration-300">
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Class Activation</p>
                <h2 class="text-3xl font-black text-yellow-600">{{ $pendaftaran_pending }}</h2>
            </div>
            <div class="text-yellow-100">
                <i class="fas fa-key fa-2x"></i>
            </div>
        </div>

        <!-- Tutor Request -->
        <div class="bg-white p-6 rounded-[2rem] shadow-xl shadow-gray-100 border-l-8 border-green-600 flex justify-between items-center transition-transform hover:scale-105 duration-300">
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Tutor Request</p>
                <h2 class="text-3xl font-black text-green-600">{{ $tutor_pending }}</h2>
            </div>
            <div class="text-green-100">
                <i class="fas fa-headset fa-2x"></i>
            </div>
        </div>

    </div>

    <!-- Log Activity & Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Activity Log -->
        <div class="lg:col-span-2 bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-8">
                <h5 class="font-black text-gray-800 uppercase tracking-tight">Recent Activity Log</h5>
                <a href="#" class="text-[10px] font-black text-gray-400 uppercase hover:text-[#990000]">View All &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="text-[10px] font-black text-gray-300 uppercase tracking-widest border-b border-gray-50">
                        <tr>
                            <th class="pb-4">Time</th>
                            <th class="pb-4">Activity Description</th>
                            <th class="pb-4 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <tr class="border-b border-gray-50">
                            <td class="py-4 text-gray-400">10 Mins ago</td>
                            <td class="py-4 font-bold text-gray-700">Student "Budi" enrolled in SMA Reguler Class</td>
                            <td class="py-4 text-right">
                                <span class="bg-green-50 text-green-600 px-3 py-1 rounded-full text-[10px] font-black uppercase">Success</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-4 text-gray-400">1 Hour ago</td>
                            <td class="py-4 font-bold text-gray-700">Admin updated PTN material data</td>
                            <td class="py-4 text-right">
                                <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-full text-[10px] font-black uppercase">Update</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Access -->
        <div class="bg-gray-900 p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden group">
            {{-- Abstract Decoration --}}
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/5 rounded-full"></div>

            <h5 class="text-white font-black uppercase tracking-widest mb-8 relative z-10 flex items-center gap-2">
                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"></path></svg>
                Quick Access
            </h5>

            <div class="flex flex-col gap-4 relative z-10">
                <a href="{{ route('admin.siswa.index') }}" class="group/btn bg-white/10 hover:bg-[#990000] p-4 rounded-2xl flex items-center gap-4 transition-all duration-300 transform hover:translate-x-2">
                    <div class="bg-white/10 p-2 rounded-xl group-hover/btn:bg-white/20">
                        <i class="fas fa-users-cog text-white text-xs"></i>
                    </div>
                    <span class="text-white text-xs font-black uppercase tracking-wider">Student Management</span>
                </a>

                <a href="{{ route('admin.tutor.index') }}" class="group/btn bg-white/10 hover:bg-[#990000] p-4 rounded-2xl flex items-center gap-4 transition-all duration-300 transform hover:translate-x-2">
                    <div class="bg-white/10 p-2 rounded-xl group-hover/btn:bg-white/20">
                        <i class="fas fa-user-check text-white text-xs"></i>
                    </div>
                    <span class="text-white text-xs font-black uppercase tracking-wider">Confirm Tutor Request</span>
                </a>

                <a href="{{ route('admin.promo.index') }}" class="group/btn bg-white/10 hover:bg-[#990000] p-4 rounded-2xl flex items-center gap-4 transition-all duration-300 transform hover:translate-x-2">
                    <div class="bg-white/10 p-2 rounded-xl group-hover/btn:bg-white/20">
                        <i class="fas fa-tags text-white text-xs"></i>
                    </div>
                    <span class="text-white text-xs font-black uppercase tracking-wider">Create New Promo</span>
                </a>
            </div>
        </div>

    </div>

</div>
@endsection
