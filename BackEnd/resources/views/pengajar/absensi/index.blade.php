@extends('layouts.spekta')
@section('title', 'Manajemen Absensi')

@section('content')
<div class="mb-10">
    <h2 class="text-3xl font-black text-gray-800 uppercase tracking-tighter">📝 Manajemen Absensi</h2>
    <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Kelola kehadiran siswa berdasarkan pertemuan mingguan</p>
</div>

{{-- Alert Notifikasi --}}
@if(session('success'))
    <div class="bg-green-600 text-white p-4 rounded-2xl mb-8 shadow-lg shadow-green-100 font-bold text-xs uppercase">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    @foreach($assignments as $as)
        @php
            // Cek apakah hari ini ada jadwal mengajar di kelas ini
            $canAbsenToday = in_array($as->class_id, $jadwalHariIni);
        @endphp

        <div class="bg-white p-8 rounded-[40px] shadow-sm border border-gray-100 transition duration-300 hover:shadow-2xl group {{ $canAbsenToday ? 'border-l-[12px] border-green-500' : 'border-l-[12px] border-gray-200' }}">
            <div class="flex justify-between items-start mb-8">
                <div class="flex-1">
                    <h3 class="text-xl font-black text-gray-800 uppercase leading-tight group-hover:text-[#990000] transition">
                        {{ $as->classModel->program_name }}
                    </h3>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="bg-red-50 text-[#990000] px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest">
                            BIDANG: {{ $as->subject_name }}
                        </span>
                    </div>
                </div>

                @if($canAbsenToday)
                    <div class="bg-green-100 text-green-700 p-2 rounded-xl animate-pulse">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                @endif
            </div>

            <div class="mb-10">
                @if($canAbsenToday)
                    <p class="text-gray-500 text-xs font-bold uppercase leading-relaxed">
                        Anda memiliki jadwal mengajar hari ini. Segera lakukan absensi pertemuan.
                    </p>
                @else
                    <p class="text-gray-300 text-xs font-bold uppercase italic leading-relaxed">
                        Tidak ada jadwal mengajar hari ini. Anda tetap bisa memantau rekap mingguan.
                    </p>
                @endif
            </div>

            <div class="flex items-center gap-4">
                {{-- ✨ MODIFIKASI: Diarahkan ke rute 'weeks' membawa Class ID dan Subject --}}
                <a href="{{ route('pengajar.absensi.weeks', [$as->class_id, $as->subject_name]) }}"
                   class="flex-1 bg-[#990000] text-white py-4 rounded-2xl font-black text-[11px] text-center uppercase tracking-widest shadow-xl shadow-red-100 hover:bg-red-800 transition transform active:scale-95">
                   Buka Daftar Minggu &rarr;
                </a>

                @if($canAbsenToday)
                    <div class="bg-green-50 text-green-600 px-4 py-4 rounded-2xl font-black text-[9px] uppercase tracking-tighter border border-green-100">
                        ACTIVE
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>

@if($assignments->isEmpty())
    <div class="bg-gray-50 p-20 rounded-[50px] border-2 border-dashed border-gray-200 text-center">
        <p class="text-gray-400 font-black uppercase text-xs tracking-widest">Anda belum memiliki penugasan materi.</p>
    </div>
@endif

@endsection
