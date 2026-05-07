@extends('layouts.spekta')
@section('title', 'Pilih Program Latihan')

@section('content')
<div class="mb-10">
    <h3 class="text-2xl font-black text-gray-800 uppercase tracking-tighter">📖 Latihan Soal (CSV)</h3>
    <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Kelola Bank Soal sesuai penugasan Anda</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    {{-- MODIFIKASI: Gunakan $assignments --}}
    @forelse($assignments as $assign)
    <div class="group relative bg-white rounded-3xl border border-gray-100 overflow-hidden hover:shadow-2xl transition duration-500">
        <div class="h-32 bg-[#990000] relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full"></div>
            <div class="p-6">
                <span class="bg-white/20 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase">Spekta Program</span>
            </div>
        </div>

        <div class="p-6">
            <h3 class="text-lg font-black text-gray-800 uppercase leading-tight">{{ $assign->classModel->program_name }}</h3>
            <p class="text-[#990000] font-black text-[10px] mt-1 mb-6 italic uppercase tracking-widest">
                BIDANG SOAL: {{ $assign->subject_name }}
            </p>

            {{-- MODIFIKASI: Route membawa 2 parameter --}}
                <a href="{{ route('pengajar.latihan.pilih', [$assign->class_id, $assign->subject_name]) }}"
               class="block text-center bg-[#990000] text-white py-3 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-red-800 transition">
               Kelola Bank Soal
            </a>
        </div>
    </div>
    @empty
    <div class="col-span-3 p-10 text-center bg-gray-50 rounded-3xl border-2 border-dashed border-gray-100">
        <p class="text-gray-400 font-bold uppercase text-[10px] tracking-widest">Belum ada penugasan latihan soal.</p>
    </div>
    @endforelse
</div>
@endsection
