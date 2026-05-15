@extends('layouts.spekta')
@section('title', 'Dashboard Portal Pengajar')

@section('content')
<div class="space-y-10">

    {{-- BANNER SELAMAT DATANG --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-[#990000] to-[#660000] p-12 rounded-[40px] text-white shadow-2xl">
        <div class="relative z-10">
            <span class="bg-white/10 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest mb-4 inline-block border border-white/20 backdrop-blur-md">
                Spekta Teacher Workspace
            </span>
            <h1 class="text-4xl font-black tracking-tight">Selamat Datang, {{ Auth::user()->name }}!</h1>
            <p class="text-red-100 mt-3 max-w-xl font-medium opacity-80 leading-relaxed">
                Platform pusat kendali Anda. Pantau agenda mengajar kelas reguler dan permintaan dedicated tutor dalam satu halaman.
            </p>
        </div>
        <div class="absolute -right-10 -bottom-10 w-80 h-80 bg-white/5 rounded-full blur-3xl"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

        {{-- KOLOM KIRI: JADWAL MENGAJAR KELAS --}}
        <div class="space-y-6">
            <div class="flex items-center justify-between px-2">
                <h3 class="text-xl font-black text-gray-800 uppercase tracking-tighter">📅 Kelas Reguler</h3>
                <span class="text-[10px] font-bold text-gray-400 uppercase">Jadwal Terdekat</span>
            </div>

            <div class="space-y-4">
                @forelse($jadwalMendatang as $item)
                <div class="bg-white p-6 rounded-[30px] shadow-sm border border-gray-100 hover:shadow-md transition group">
                    <div class="flex items-center gap-6">
                        <div class="text-center pr-6 border-r border-gray-100">
                            <p class="text-2xl font-black text-[#990000]">{{ date('d', strtotime($item->date)) }}</p>
                            <p class="text-[9px] font-bold text-gray-400 uppercase">{{ date('M', strtotime($item->date)) }}</p>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-black text-gray-800 uppercase">{{ $item->title }}</h4>
                            <p class="text-[10px] font-bold text-[#990000] uppercase mt-1">{{ $item->class->program_name ?? 'Program' }}</p>
                            <p class="text-[10px] text-gray-400 mt-2 font-bold"><i class="far fa-clock mr-1"></i> {{ substr($item->start_time, 0, 5) }} WIB</p>
                        </div>

                        {{-- ✨ PERBAIKAN: Tombol Absen diarahkan ke 20 Minggu --}}
                        @if($item->date == date('Y-m-d'))
                        @endif
                    </div>
                </div>
                @empty
                <div class="p-10 bg-gray-50 rounded-[30px] border-2 border-dashed text-center">
                    <p class="text-gray-400 font-bold text-[10px] uppercase">Belum ada jadwal kelas.</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- KOLOM KANAN: JADWAL DEDICATED TUTOR --}}
        <div class="space-y-6">
            <div class="flex items-center justify-between px-2">
                <h3 class="text-xl font-black text-gray-800 uppercase tracking-tighter">🤝 Dedicated Tutor</h3>
                <span class="text-[10px] font-bold text-blue-500 uppercase italic">Privat Session</span>
            </div>

            <div class="space-y-4">
                @forelse($jadwalTutor as $tutor)
                <div class="bg-white p-6 rounded-[30px] shadow-sm border border-blue-50 hover:shadow-md transition">
                    <div class="flex items-center gap-6">
                        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center font-black text-lg">
                            {{ substr($tutor->student->user->name, 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-black text-gray-800 uppercase">{{ $tutor->student->user->name }}</h4>
                            <p class="text-[10px] font-bold text-blue-600 uppercase mt-1">{{ $tutor->material->title ?? 'Materi Privat' }}</p>
                            <p class="text-[10px] text-gray-400 mt-2 font-bold">
                                <i class="far fa-calendar-alt mr-1"></i> {{ date('d M Y', strtotime($tutor->date)) }}
                            </p>
                        </div>
                        <span class="bg-blue-600 text-white px-4 py-2 rounded-xl font-black text-[9px] uppercase">
                            CONFIRMED
                        </span>
                    </div>
                </div>
                @empty
                <div class="p-10 bg-gray-50 rounded-[30px] border-2 border-dashed text-center">
                    <p class="text-gray-400 font-bold text-[10px] uppercase">Belum ada jadwal tutor.</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
