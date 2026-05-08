@extends('layouts.spekta')
@section('title', 'Materi Saya')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-black text-gray-800 uppercase tracking-tighter">📚 Materi Pembelajaran</h2>
    <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Pilih program dan subjek untuk mengelola 20 minggu materi</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @foreach($assignments as $assign)
    <div class="bg-white p-8 rounded-[30px] shadow-sm border-l-[10px] border-[#990000] hover:shadow-xl transition duration-300">
        <h3 class="text-xl font-black text-gray-800 uppercase">{{ $assign->classModel->program_name }}</h3>
        <p class="text-[#990000] font-black text-xs mt-1 mb-6 italic uppercase tracking-widest">
            BIDANG: {{ $assign->subject_name }}
        </p>

        {{-- Mengirim class_id DAN subject_name secara berurutan --}}
        <a href="{{ route('pengajar.materi.pilih', [$assign->class_id, $assign->subject_name]) }}"
           class="bg-[#990000] text-white px-6 py-3 rounded-2xl font-black text-[10px] inline-block shadow-lg shadow-red-100 hover:bg-red-800 uppercase tracking-widest transition">
           Kelola 20 Minggu
        </a>
    </div>
    @endforeach
</div>
@endsection
